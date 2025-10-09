<?php

namespace App\Service;

use App\Entity\Site;

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
        $site = new Site();
        $site->setName($siteName);
        
        // Définir le domaine selon le site
        $domain = match($siteName) {
            'silenus' => 'silenus.local',
            'insidiome' => 'insidiome.local',
            default => 'localhost'
        };
        
        $site->setDomain($domain);

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
        $em = $this->dbManager->getEntityManagerForSite($siteName);
        
        $site = $em->getRepository(Site::class)
            ->findOneBy(['name' => $siteName]);

        if (!$site) {
            $site = new Site();
            $site->setName($siteName);
            
            $domain = match($siteName) {
                'silenus' => 'silenus.local',
                'insidiome' => 'insidiome.local',
                default => 'localhost'
            };
            
            $site->setDomain($domain);

            $em->persist($site);
            $em->flush();
        }

        return $site;
    }
}
