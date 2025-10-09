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

        // Détection par path (prioritaire pour localhost)
        $path = $request->getPathInfo();
        
        if (str_starts_with($path, '/slns')) {
            $this->currentSite = 'silenus';
            return $this->currentSite;
        }
        
        if (str_starts_with($path, '/nsdm')) {
            $this->currentSite = 'insidiome';
            return $this->currentSite;
        }

        // Détection par domaine (pour production)
        $host = $request->getHost();
        
        if (isset(self::SITE_MAPPING[$host])) {
            $this->currentSite = self::SITE_MAPPING[$host];
            return $this->currentSite;
        }

        // Par défaut, silenus
        $this->currentSite = 'silenus';
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
