<?php

namespace App\Controller\Ndsm;

use App\Service\SiteResolver;
use App\Service\DatabaseManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ndsm')]
class HomeController extends AbstractController
{
    public function __construct(
        private SiteResolver $siteResolver,
        private DatabaseManager $dbManager
    ) {}

    #[Route('/', name: 'ndsm_home')]
    public function index(): Response
    {
        // Vérifier qu'on est bien sur Insidiome
        if (!$this->siteResolver->isInsidiome()) {
            throw $this->createNotFoundException('Page non disponible sur ce site');
        }

        return $this->render('insidiome/front/home/index.html.twig', [
            'site_name' => $this->siteResolver->getSiteName(),
            'message' => 'Bienvenue sur Insidiome'
        ]);
    }

    #[Route('/about', name: 'ndsm_about')]
    public function about(): Response
    {
        return $this->render('insidiome/front/home/about.html.twig', [
            'site_name' => $this->siteResolver->getSiteName()
        ]);
    }
}
