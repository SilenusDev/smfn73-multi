<?php

namespace App\EventSubscriber;

use App\Service\SiteResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SiteSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SiteResolver $siteResolver
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 20],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        // Force la détection du site dès la première requête
        try {
            $site = $this->siteResolver->getCurrentSite();
            
            // Log pour debug (optionnel, peut être commenté en prod)
            // error_log("[SiteSubscriber] Site détecté : " . $site);
        } catch (\RuntimeException $e) {
            // En cas d'erreur de détection, on laisse l'exception remonter
            throw $e;
        }
    }
}
