<?php

namespace App\Service;

use App\Entity\Site;
use App\Enum\SiteEnum;

/**
 * Gère l'entité Site locale de chaque base de données
 * Chaque base a sa propre entrée Site (id=1)
 */
class SiteManager
{
    public function __construct(
        private DatabaseManager $dbManager,
        private SiteResolver $siteResolver
    ) {}

    /**
     * Récupère l'entité Site de la base courante
     * Crée l'entrée si elle n'existe pas
     */
    public function getCurrentSiteEntity(): Site
    {
        $siteName = $this->siteResolver->getCurrentSite();
        
        // Cherche l'entité Site dans la base courante
        $site = $this->dbManager
            ->getRepository(Site::class)
            ->findOneBy(['name' => $siteName]);

        // Si elle n'existe pas, la créer
        if (!$site) {
            $site = $this->createSiteEntity($siteName);
        }

        return $site;
    }

    /**
     * Crée l'entité Site pour la base courante
     */
    private function createSiteEntity(string $siteName): Site
    {
        $siteEnum = SiteEnum::fromName($siteName);
        
        if (!$siteEnum) {
            throw new \InvalidArgumentException("Site inconnu: {$siteName}");
        }
        
        $site = new Site();
        $site->setName($siteEnum);
        $site->setDomain($siteEnum->getDomain());

        $this->dbManager->persist($site);
        $this->dbManager->flush();

        return $site;
    }

    /**
     * Vérifie si l'entité Site existe dans la base courante
     */
    public function siteEntityExists(): bool
    {
        $siteName = $this->siteResolver->getCurrentSite();
        
        $site = $this->dbManager
            ->getRepository(Site::class)
            ->findOneBy(['name' => $siteName]);

        return $site !== null;
    }

    /**
     * Initialise l'entité Site pour une base spécifique (commandes)
     */
    public function initializeSiteForDatabase(string $siteName): Site
    {
        $siteEnum = SiteEnum::fromName($siteName);
        
        if (!$siteEnum) {
            throw new \InvalidArgumentException("Site inconnu: {$siteName}");
        }
        
        $em = $this->dbManager->getEntityManagerForSite($siteName);
        
        $site = $em->getRepository(Site::class)
            ->findOneBy(['name' => $siteEnum]);

        if (!$site) {
            $site = new Site();
            $site->setName($siteEnum);
            $site->setDomain($siteEnum->getDomain());

            $em->persist($site);
            $em->flush();
        }

        return $site;
    }
}
