<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class DatabaseManager
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private SiteResolver $siteResolver
    ) {}

    /**
     * Retourne l'Entity Manager correspondant au site actuel
     */
    public function getEntityManager(): EntityManagerInterface
    {
        $site = $this->siteResolver->getCurrentSite();
        
        return $this->doctrine->getManager($site);
    }

    /**
     * Raccourci pour obtenir un repository
     */
    public function getRepository(string $entityClass)
    {
        return $this->getEntityManager()->getRepository($entityClass);
    }

    /**
     * Persiste une entité dans la bonne base
     */
    public function persist(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * Flush dans la bonne base
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Remove une entité de la bonne base
     */
    public function remove(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * Récupère l'EM d'un site spécifique (utile pour les commandes)
     */
    public function getEntityManagerForSite(string $site): EntityManagerInterface
    {
        if (!in_array($site, ['silenus', 'insidiome'])) {
            throw new \InvalidArgumentException("Site invalide : {$site}");
        }
        
        return $this->doctrine->getManager($site);
    }

    /**
     * Retourne le nom de la connexion active
     */
    public function getCurrentConnectionName(): string
    {
        return $this->siteResolver->getCurrentSite();
    }
}
