<?php

namespace App\Controller;

use App\Enum\Role;
use App\Enum\SubmissionStatus;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/review')]
#[IsGranted('ROLE_REVIEWER')]
final class ReviewController extends AbstractController
{
    #[Route('/', name: 'app_review_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $pending = $productRepository->findBy(
            ['status' => SubmissionStatus::Pending],
            ['id' => 'DESC']
        );

        $approvedByReview = $productRepository->findBy(
            ['status' => SubmissionStatus::ApprovedByReview],
            ['id' => 'DESC']
        );

        return $this->render('review/index.html.twig', [
            'pending'          => $pending,
            'approvedByReview' => $approvedByReview,
        ]);
    }

    #[Route('/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(int $id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }

        return $this->render('review/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/approve', name: 'app_review_approve', methods: ['POST'])]
    public function approve(int $id, ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }

        if ($this->isGranted(Role::Barista->value)) {
            $product->setStatus(SubmissionStatus::Approved);
        } else {
            $product->setStatus(SubmissionStatus::ApprovedByReview);
        }

        $em->flush();

        $this->addFlash('success', 'Product approved.');

        return $this->redirectToRoute('app_review_index');
    }

    #[Route('/{id}/reject', name: 'app_review_reject', methods: ['POST'])]
    public function reject(int $id, Request $request, ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }

        $comment = trim($request->request->get('rejection_comment', ''));

        if ($comment === '') {
            $this->addFlash('error', 'A rejection comment is required.');

            return $this->redirectToRoute('app_review_show', ['id' => $id]);
        }

        if ($this->isGranted(Role::Barista->value)) {
            $product->setStatus(SubmissionStatus::Rejected);
        } else {
            $product->setStatus(SubmissionStatus::RejectedByReview);
        }

        $product->setRejectionComment($comment);
        $em->flush();

        $this->addFlash('success', 'Product rejected.');

        return $this->redirectToRoute('app_review_index');
    }
}
