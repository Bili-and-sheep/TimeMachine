<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUuid = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_uuid' => $lastUuid,
            'error'     => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/mydrilla', name: 'app_mydrilla')]
    public function profile(Request $request): Response
    {
        // Get the currently logged-in user
        /** @var User $user */
        $user = $this->getUser();

        // Ensure the user is authenticated
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
        // Render the profile view with the user data
        return $this->render('security/mydrilla.html.twig', [
            'user' => $user,
        ]);
    }
}
