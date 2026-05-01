<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GentlePuppyController extends AbstractController
{
    #[Route('/puppy', name: 'app_gentle_puppy')]
    public function index(): Response
    {
        return $this->render('gentle_puppy/index.html.twig', [
            'controller_name' => 'GentlePuppyController',
        ]);
    }
}
