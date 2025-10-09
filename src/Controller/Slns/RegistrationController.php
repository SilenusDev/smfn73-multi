<?php

namespace App\Controller\Slns;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\SiteResolver;
use App\Service\DatabaseManager;
use App\Service\SiteManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/slns')]
class RegistrationController extends AbstractController
{
    public function __construct(
        private SiteResolver $siteResolver,
        private DatabaseManager $dbManager,
        private SiteManager $siteManager
    ) {}

    #[Route('/register', name: 'slns_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // Vérifier qu'on est sur Silenus
        if (!$this->siteResolver->isSilenus()) {
            throw $this->createNotFoundException('Page non disponible sur ce site');
        }

        // Si déjà connecté, rediriger
        if ($this->getUser()) {
            return $this->redirectToRoute('slns_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Associer le user au site Silenus
            $site = $this->siteManager->getCurrentSiteEntity();
            $user->setSite($site);

            // Sauvegarder dans la base Silenus
            $this->dbManager->persist($user);
            $this->dbManager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès !');

            return $this->redirectToRoute('slns_login');
        }

        return $this->render('silenus/front/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'site_name' => $this->siteResolver->getSiteName()
        ]);
    }
}
