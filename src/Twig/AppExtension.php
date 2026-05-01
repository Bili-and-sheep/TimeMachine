<?php

namespace App\Twig;

use App\Enum\SubmissionStatus;
use App\Repository\ProductRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private readonly ProductRepository $productRepository) {}

    public function getGlobals(): array
    {
        return [
            'pending_count' => $this->productRepository->count(['status' => SubmissionStatus::Pending]),
        ];
    }
}
