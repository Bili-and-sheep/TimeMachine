<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class UuidAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserRepository $userRepository,
        private RouterInterface $router,
    ) {}

    /**
     * Only intercept POST requests to /login.
     */
    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST')
            && $request->attributes->get('_route') === 'app_login';
    }

    public function authenticate(Request $request): Passport
    {
        $uuid = trim((string) $request->request->get('uuid', ''));
        $csrfToken = (string) $request->request->get('_csrf_token', '');

        if ($uuid === '') {
            throw new CustomUserMessageAuthenticationException('Please enter your account UUID.');
        }

        $user = null;
        foreach ($this->userRepository->findAll() as $candidate) {
            if (password_verify($uuid, (string) $candidate->getUuid())) {
                $user = $candidate;
                break;
            }
        }

        if ($user === null) {
            throw new CustomUserMessageAuthenticationException('Invalid UUID — no account found.');
        }

        // SelfValidatingPassport = no password check, the user lookup is enough
        return new SelfValidatingPassport(
            new \Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge(
                $uuid,
                fn() => $user,
            ),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirect to wherever you want after login
        return new RedirectResponse($this->router->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Store the error and UUID in the session so the login form can re-display them
        $request->getSession()->set(
            \Symfony\Component\Security\Http\SecurityRequestAttributes::AUTHENTICATION_ERROR,
            $exception
        );
        $request->getSession()->set(
            \Symfony\Component\Security\Http\SecurityRequestAttributes::LAST_USERNAME,
            $request->request->get('uuid', '')
        );

        return new RedirectResponse($this->router->generate('app_login'));
    }
}
