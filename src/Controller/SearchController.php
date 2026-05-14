<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $query    = trim($request->query->get('q', ''));
        $products = $query !== '' ? $productRepository->search($query) : [];

        return $this->render('search/index.html.twig', [
            'query'    => $query,
            'products' => $products,
        ]);
    }
}
