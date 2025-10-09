<?php

namespace App\Controller\Ndsm;

use App\Service\SiteResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/nsdm')]
class SecurityController extends AbstractController
{
    public function __construct(
        private SiteResolver $siteResolver
    ) {}

    #[Route('/login', name: 'nsdm_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Vérifier qu'on est sur Insidiome
        if (!$this->siteResolver->isInsidiome()) {
            throw $this->createNotFoundException('Page non disponible sur ce site');
        }

        // Si déjà connecté, rediriger
        if ($this->getUser()) {
            return $this->redirectToRoute('nsdm_home');
        }

        // Récupérer les erreurs de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('insidiome/front/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'site_name' => $this->siteResolver->getSiteName()
        ]);
    }

    #[Route('/logout', name: 'nsdm_logout')]
    public function logout(): void
    {
        // Cette méthode ne sera jamais appelée
        // Symfony intercepte la route et gère la déconnexion
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
