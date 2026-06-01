<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagFormType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tagmngt')]
#[IsGranted('ROLE_BARISTA')]
final class TagController extends AbstractController
{
    #[Route('', name: 'app_tag_index', methods: ['GET'])]
    public function index(TagRepository $tagRepository): Response
    {
        return $this->render('tag/index.html.twig', [
            'tags' => $tagRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_tag_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $tag  = new Tag();
        $form = $this->createForm(TagFormType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();
            $this->addFlash('success', 'Tag "' . $tag->getName() . '" created.');

            return $this->redirectToRoute('app_tag_index');
        }

        return $this->render('tag/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'app_tag_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, TagRepository $tagRepository, EntityManagerInterface $em): Response
    {
        $tag = $tagRepository->find($id);

        if (!$tag) {
            throw $this->createNotFoundException('Tag not found.');
        }

        $form = $this->createForm(TagFormType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Tag updated.');

            return $this->redirectToRoute('app_tag_index');
        }

        return $this->render('tag/edit.html.twig', ['tag' => $tag, 'form' => $form]);
    }

    #[Route('/{id}/delete', name: 'app_tag_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, TagRepository $tagRepository, EntityManagerInterface $em): Response
    {
        $tag = $tagRepository->find($id);

        if (!$tag) {
            throw $this->createNotFoundException('Tag not found.');
        }

        if ($this->isCsrfTokenValid('delete_tag_' . $id, $request->request->get('_token'))) {
            $em->remove($tag);
            $em->flush();
            $this->addFlash('success', 'Tag deleted.');
        }

        return $this->redirectToRoute('app_tag_index');
    }
}