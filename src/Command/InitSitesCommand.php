<?php

namespace App\Command;

use App\Service\SiteManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-sites',
    description: 'Initialise les entités Site dans chaque base de données',
)]
class InitSitesCommand extends Command
{
    public function __construct(
        private SiteManager $siteManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Initialisation des entités Site');

        // Initialiser Silenus
        $io->section('Base Silenus (slns_db)');
        try {
            $siteSilenus = $this->siteManager->initializeSiteForDatabase('silenus');
            $io->success("Site 'silenus' initialisé (ID: {$siteSilenus->getId()})");
        } catch (\Exception $e) {
            $io->error("Erreur Silenus: " . $e->getMessage());
            return Command::FAILURE;
        }

        // Initialiser Insidiome
        $io->section('Base Insidiome (nsdm_db)');
        try {
            $siteInsidiome = $this->siteManager->initializeSiteForDatabase('insidiome');
            $io->success("Site 'insidiome' initialisé (ID: {$siteInsidiome->getId()})");
        } catch (\Exception $e) {
            $io->error("Erreur Insidiome: " . $e->getMessage());
            return Command::FAILURE;
        }

        $io->success('✅ Les 2 bases ont leur entité Site initialisée !');
        
        $io->note([
            'Chaque base a maintenant sa propre table site avec 1 entrée.',
            'Les users peuvent être liés à leur site local.',
            'L\'isolation entre bases est maintenue.'
        ]);

        return Command::SUCCESS;
    }
}
