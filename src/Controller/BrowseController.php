<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BrowseController extends AbstractController
{
    public const CATEGORIES = [
        'iphone'      => ['iPhone'],
        'mac'         => ['Mac', 'iMac', 'Mac mini', 'Mac Pro', 'Mac Studio', 'MacBook', 'MacBook Air', 'MacBook Pro'],
        'ipad'        => ['iPad'],
        'watch'       => ['Apple Watch'],
        'tv'          => ['Apple TV'],
        'accessories' => ['Accessory', 'AirPods', 'HomePod', 'Display'],
        'other'       => ['iPod', 'Software'],
    ];

    #[Route('/browse/{category}', name: 'app_browse')]
    public function category(string $category, ProductRepository $productRepository): Response
    {
        $typeNames = self::CATEGORIES[$category] ?? null;

        if ($typeNames === null) {
            throw $this->createNotFoundException('Unknown category.');
        }

        return $this->render('browse/category.html.twig', [
            'category' => $category,
            'products' => $productRepository->findApprovedByCategory($typeNames),
        ]);
    }
}
