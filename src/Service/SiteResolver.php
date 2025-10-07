<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class SiteResolver
{
    private ?string $currentSite = null;

    // Mapping domaine → site
    private const SITE_MAPPING = [
        'silenus.local' => 'silenus',
        'insidiome.local' => 'insidiome',
        // Production
        'silenus.com' => 'silenus',
        'insidiome.com' => 'insidiome',
        // Docker (si besoin)
        'localhost' => 'silenus', // Par défaut
    ];

    public function __construct(
        private RequestStack $requestStack
    ) {}

    public function getCurrentSite(): string
    {
        if ($this->currentSite !== null) {
            return $this->currentSite;
        }

        $request = $this->requestStack->getCurrentRequest();
        
        if (!$request) {
            throw new \RuntimeException('Pas de requête disponible');
        }

        $host = $request->getHost();
        
        if (!isset(self::SITE_MAPPING[$host])) {
            throw new \RuntimeException("Site inconnu pour le domaine : {$host}");
        }

        $this->currentSite = self::SITE_MAPPING[$host];
        
        return $this->currentSite;
    }

    public function isSilenus(): bool
    {
        return $this->getCurrentSite() === 'silenus';
    }

    public function isInsidiome(): bool
    {
        return $this->getCurrentSite() === 'insidiome';
    }

    /**
     * Retourne le nom du site pour les logs/debug
     */
    public function getSiteName(): string
    {
        return match($this->getCurrentSite()) {
            'silenus' => 'Silenus',
            'insidiome' => 'Insidiome',
            default => 'Unknown'
        };
    }
}
