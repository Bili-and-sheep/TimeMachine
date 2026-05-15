<?php

namespace App\Twig;

use App\Entity\Product;
use App\Enum\SubmissionStatus;
use App\Repository\ProductRepository;
use App\Service\InflationService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly InflationService $inflationService,
    ) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('inflation_adjusted', [$this, 'inflationAdjusted']),
        ];
    }

    public function inflationAdjusted(Product $product): ?int
    {
        if ($product->getOriginalPrice() === null || $product->getReleaseDate() === null) {
            return null;
        }

        return $this->inflationService->getAdjustedPrice(
            $product->getOriginalPrice(),
            (int) $product->getReleaseDate()->format('Y'),
        );
    }

    public function getGlobals(): array
    {
        return [
            'pending_count' => $this->productRepository->count(['status' => SubmissionStatus::Pending]),
        ];
    }
}
