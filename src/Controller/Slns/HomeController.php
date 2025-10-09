<?php

namespace App\Controller\Slns;

use App\Service\SiteResolver;
use App\Service\DatabaseManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/slns')]
class HomeController extends AbstractController
{
    public function __construct(
        private SiteResolver $siteResolver,
        private DatabaseManager $dbManager
    ) {}

    #[Route('/', name: 'slns_home')]
    public function index(): Response
    {
        // Vérifier qu'on est bien sur Silenus
        if (!$this->siteResolver->isSilenus()) {
            throw $this->createNotFoundException('Page non disponible sur ce site');
        }

        return $this->render('silenus/front/home/index.html.twig', [
            'site_name' => $this->siteResolver->getSiteName(),
            'message' => 'Bienvenue sur Silenus'
        ]);
    }

    #[Route('/about', name: 'slns_about')]
    public function about(): Response
    {
        return $this->render('silenus/front/home/about.html.twig', [
            'site_name' => $this->siteResolver->getSiteName()
        ]);
    }
}
