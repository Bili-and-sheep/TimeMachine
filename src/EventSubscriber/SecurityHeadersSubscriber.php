<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
            "style-src 'self' 'unsafe-inline'; " .
            "img-src 'self' data: https:; " .
            "font-src 'self'; " .
            "frame-ancestors 'none'"
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => 'onKernelResponse'];
    }
}
