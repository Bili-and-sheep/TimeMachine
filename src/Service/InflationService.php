<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class InflationService
{
    // World Bank US CPI index (2010 = 100), annual data, no API key required
    private const API_URL = 'https://api.worldbank.org/v2/country/US/indicator/FP.CPI.TOTL?format=json&per_page=100&mrv=70';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
    ) {}

    /**
     * @return array<int, float> year => CPI value, sorted descending
     */
    private function fetchCpiByYear(): array
    {
        return $this->cache->get('worldbank_us_cpi', function (ItemInterface $item): array {
            $item->expiresAfter(86400); // refresh once per day

            $response = $this->httpClient->request('GET', self::API_URL);
            $data = $response->toArray();

            $result = [];
            foreach ($data[1] ?? [] as $entry) {
                if (isset($entry['date'], $entry['value']) && $entry['value'] !== null) {
                    $result[(int) $entry['date']] = (float) $entry['value'];
                }
            }
            krsort($result); // most recent year first

            return $result;
        });
    }

    public function getAdjustedPrice(int $originalPrice, int $fromYear): ?int
    {
        try {
            $cpiByYear = $this->fetchCpiByYear();
        } catch (\Throwable) {
            // fallback to static 3% per year if the API is unreachable
            $years = max(0, (int) (new \DateTimeImmutable())->format('Y') - $fromYear);
            return (int) round($originalPrice * (1.03 ** $years));
        }

        if (empty($cpiByYear)) {
            return null;
        }

        $fromCpi   = $this->findNearestCpi($cpiByYear, $fromYear);
        $latestCpi = (float) reset($cpiByYear); // most recent available year

        if ($fromCpi === null) {
            return null;
        }

        return (int) round($originalPrice * ($latestCpi / $fromCpi));
    }

    /** @param array<int, float> $cpiByYear */
    private function findNearestCpi(array $cpiByYear, int $year): ?float
    {
        if (isset($cpiByYear[$year])) {
            return $cpiByYear[$year];
        }

        $nearest = null;
        $minDiff = PHP_INT_MAX;
        foreach ($cpiByYear as $y => $cpi) {
            $diff = abs($y - $year);
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $nearest = $cpi;
            }
        }

        return $nearest;
    }
}