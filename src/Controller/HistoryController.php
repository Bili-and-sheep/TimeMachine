<?php

namespace App\Controller;

use App\Repository\ModificationHistoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/product/{id}/history')]
#[IsGranted('ROLE_BARISTA')]
final class HistoryController extends AbstractController
{
    #[Route('', name: 'app_product_history', methods: ['GET'])]
    public function show(int $id, ProductRepository $productRepository, ModificationHistoryRepository $historyRepository): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }

        return $this->render('history/show.html.twig', [
            'product' => $product,
            'entries' => $historyRepository->findByProduct($product),
        ]);
    }
}
