<?php

namespace App\Controller;

use App\Entity\ModificationHistory;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\ModificationAction;
use App\Enum\SubmissionStatus;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/product')]
final class ProductController extends AbstractController
{
    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $product->setModifiedByUser($user);
            $em->persist($product);

            $em->persist(new ModificationHistory($product, $user, ModificationAction::Created));


            $em->flush();

            $this->addFlash('success', 'Product submitted and pending review.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(int $id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        if (!$product || $product->getStatus() !== SubmissionStatus::Approved) {
            throw $this->createNotFoundException('Product not found.');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_BARISTA')]
    public function edit(int $id, Request $request, ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $product->setModifiedByUser($user);
            $em->persist(new ModificationHistory($product, $user, ModificationAction::Edited));
            $em->flush();

            $this->addFlash('success', 'Product updated.');

            return $this->redirectToRoute('app_product_show', ['id' => $id]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form'    => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_BARISTA')]
    public function delete(int $id, Request $request, ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found.');
        }

        if ($this->isCsrfTokenValid('delete_product_' . $id, $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();
            $this->addFlash('success', 'Product deleted.');
        }

        return $this->redirectToRoute('app_home');
    }
}
