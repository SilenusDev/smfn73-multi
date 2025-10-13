<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour convertir site.name en ENUM
 */
final class Version20251013094100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convertit la colonne site.name en type ENUM(silenus, insidiome)';
    }

    public function up(Schema $schema): void
    {
        // Modifier la colonne name pour utiliser ENUM
        $this->addSql("ALTER TABLE site MODIFY name ENUM('silenus', 'insidiome') NOT NULL");
    }

    public function down(Schema $schema): void
    {
        // Revenir à VARCHAR
        $this->addSql('ALTER TABLE site MODIFY name VARCHAR(20) NOT NULL');
    }
}
