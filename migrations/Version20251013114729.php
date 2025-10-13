<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251013114729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blg_article (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, author_id INT NOT NULL, category_id INT DEFAULT NULL, section_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, titre VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, slug_fr VARCHAR(254) NOT NULL, slug_en VARCHAR(254) DEFAULT NULL, descr_fr VARCHAR(254) NOT NULL, descr_en VARCHAR(254) DEFAULT NULL, tags_fr VARCHAR(254) DEFAULT NULL, tags_en VARCHAR(254) DEFAULT NULL, url_fr VARCHAR(254) DEFAULT NULL, url_en VARCHAR(254) DEFAULT NULL, img VARCHAR(254) DEFAULT NULL, status VARCHAR(20) NOT NULL, view_count INT DEFAULT 0 NOT NULL, like_count INT DEFAULT 0 NOT NULL, comment_count INT DEFAULT 0 NOT NULL, INDEX IDX_83B6A6F4F6BD1646 (site_id), INDEX IDX_83B6A6F4F675F31B (author_id), INDEX IDX_83B6A6F412469DE2 (category_id), INDEX IDX_83B6A6F4D823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_article_blg_tag (blg_article_id INT NOT NULL, blg_tag_id INT NOT NULL, INDEX IDX_AB00B018ED31369 (blg_article_id), INDEX IDX_AB00B018886C3CC5 (blg_tag_id), PRIMARY KEY(blg_article_id, blg_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_category (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, parent_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, slug_fr VARCHAR(254) NOT NULL, slug_en VARCHAR(254) DEFAULT NULL, descr_fr VARCHAR(254) NOT NULL, descr_en VARCHAR(254) DEFAULT NULL, tags_fr VARCHAR(254) DEFAULT NULL, tags_en VARCHAR(254) DEFAULT NULL, url_fr VARCHAR(254) DEFAULT NULL, url_en VARCHAR(254) DEFAULT NULL, img VARCHAR(254) DEFAULT NULL, INDEX IDX_18CC6701F6BD1646 (site_id), INDEX IDX_18CC6701727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_category_blg_tag (blg_category_id INT NOT NULL, blg_tag_id INT NOT NULL, INDEX IDX_7A8E06AFDFEDDCE4 (blg_category_id), INDEX IDX_7A8E06AF886C3CC5 (blg_tag_id), PRIMARY KEY(blg_category_id, blg_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_comment (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, author_id INT NOT NULL, article_id INT DEFAULT NULL, page_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, content LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, INDEX IDX_15F8FAFEF6BD1646 (site_id), INDEX IDX_15F8FAFEF675F31B (author_id), INDEX IDX_15F8FAFE7294869C (article_id), INDEX IDX_15F8FAFEC4663E4 (page_id), INDEX IDX_15F8FAFE727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_like (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, user_id INT NOT NULL, article_id INT DEFAULT NULL, page_id INT DEFAULT NULL, comment_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_996FA669F6BD1646 (site_id), INDEX IDX_996FA669A76ED395 (user_id), INDEX IDX_996FA6697294869C (article_id), INDEX IDX_996FA669C4663E4 (page_id), INDEX IDX_996FA669F8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_page (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, author_id INT NOT NULL, category_id INT DEFAULT NULL, section_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, titre VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, slug_fr VARCHAR(254) NOT NULL, slug_en VARCHAR(254) DEFAULT NULL, descr_fr VARCHAR(254) NOT NULL, descr_en VARCHAR(254) DEFAULT NULL, tags_fr VARCHAR(254) DEFAULT NULL, tags_en VARCHAR(254) DEFAULT NULL, url_fr VARCHAR(254) DEFAULT NULL, url_en VARCHAR(254) DEFAULT NULL, img VARCHAR(254) DEFAULT NULL, status VARCHAR(20) NOT NULL, view_count INT DEFAULT 0 NOT NULL, like_count INT DEFAULT 0 NOT NULL, comment_count INT DEFAULT 0 NOT NULL, INDEX IDX_210650FAF6BD1646 (site_id), INDEX IDX_210650FAF675F31B (author_id), UNIQUE INDEX UNIQ_210650FA12469DE2 (category_id), UNIQUE INDEX UNIQ_210650FAD823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_page_blg_tag (blg_page_id INT NOT NULL, blg_tag_id INT NOT NULL, INDEX IDX_A909724A8DCACB76 (blg_page_id), INDEX IDX_A909724A886C3CC5 (blg_tag_id), PRIMARY KEY(blg_page_id, blg_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_section (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, parent_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, slug_fr VARCHAR(254) NOT NULL, slug_en VARCHAR(254) DEFAULT NULL, descr_fr VARCHAR(254) NOT NULL, descr_en VARCHAR(254) DEFAULT NULL, tags_fr VARCHAR(254) DEFAULT NULL, tags_en VARCHAR(254) DEFAULT NULL, url_fr VARCHAR(254) DEFAULT NULL, url_en VARCHAR(254) DEFAULT NULL, img VARCHAR(254) DEFAULT NULL, INDEX IDX_ACFFD27DF6BD1646 (site_id), INDEX IDX_ACFFD27D727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_section_blg_tag (blg_section_id INT NOT NULL, blg_tag_id INT NOT NULL, INDEX IDX_949539A0A464768F (blg_section_id), INDEX IDX_949539A0886C3CC5 (blg_tag_id), PRIMARY KEY(blg_section_id, blg_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_tag (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, titre VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, slug_fr VARCHAR(254) NOT NULL, slug_en VARCHAR(254) DEFAULT NULL, descr_fr VARCHAR(254) NOT NULL, descr_en VARCHAR(254) DEFAULT NULL, tags_fr VARCHAR(254) DEFAULT NULL, tags_en VARCHAR(254) DEFAULT NULL, url_fr VARCHAR(254) DEFAULT NULL, url_en VARCHAR(254) DEFAULT NULL, img VARCHAR(254) DEFAULT NULL, INDEX IDX_BAC797ABF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blg_article ADD CONSTRAINT FK_83B6A6F4F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_article ADD CONSTRAINT FK_83B6A6F4F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blg_article ADD CONSTRAINT FK_83B6A6F412469DE2 FOREIGN KEY (category_id) REFERENCES blg_category (id)');
        $this->addSql('ALTER TABLE blg_article ADD CONSTRAINT FK_83B6A6F4D823E37A FOREIGN KEY (section_id) REFERENCES blg_section (id)');
        $this->addSql('ALTER TABLE blg_article_blg_tag ADD CONSTRAINT FK_AB00B018ED31369 FOREIGN KEY (blg_article_id) REFERENCES blg_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_article_blg_tag ADD CONSTRAINT FK_AB00B018886C3CC5 FOREIGN KEY (blg_tag_id) REFERENCES blg_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_category ADD CONSTRAINT FK_18CC6701F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_category ADD CONSTRAINT FK_18CC6701727ACA70 FOREIGN KEY (parent_id) REFERENCES blg_category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE blg_category_blg_tag ADD CONSTRAINT FK_7A8E06AFDFEDDCE4 FOREIGN KEY (blg_category_id) REFERENCES blg_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_category_blg_tag ADD CONSTRAINT FK_7A8E06AF886C3CC5 FOREIGN KEY (blg_tag_id) REFERENCES blg_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_comment ADD CONSTRAINT FK_15F8FAFEF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_comment ADD CONSTRAINT FK_15F8FAFEF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blg_comment ADD CONSTRAINT FK_15F8FAFE7294869C FOREIGN KEY (article_id) REFERENCES blg_article (id)');
        $this->addSql('ALTER TABLE blg_comment ADD CONSTRAINT FK_15F8FAFEC4663E4 FOREIGN KEY (page_id) REFERENCES blg_page (id)');
        $this->addSql('ALTER TABLE blg_comment ADD CONSTRAINT FK_15F8FAFE727ACA70 FOREIGN KEY (parent_id) REFERENCES blg_comment (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE blg_like ADD CONSTRAINT FK_996FA669F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_like ADD CONSTRAINT FK_996FA669A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blg_like ADD CONSTRAINT FK_996FA6697294869C FOREIGN KEY (article_id) REFERENCES blg_article (id)');
        $this->addSql('ALTER TABLE blg_like ADD CONSTRAINT FK_996FA669C4663E4 FOREIGN KEY (page_id) REFERENCES blg_page (id)');
        $this->addSql('ALTER TABLE blg_like ADD CONSTRAINT FK_996FA669F8697D13 FOREIGN KEY (comment_id) REFERENCES blg_comment (id)');
        $this->addSql('ALTER TABLE blg_page ADD CONSTRAINT FK_210650FAF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_page ADD CONSTRAINT FK_210650FAF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blg_page ADD CONSTRAINT FK_210650FA12469DE2 FOREIGN KEY (category_id) REFERENCES blg_category (id)');
        $this->addSql('ALTER TABLE blg_page ADD CONSTRAINT FK_210650FAD823E37A FOREIGN KEY (section_id) REFERENCES blg_section (id)');
        $this->addSql('ALTER TABLE blg_page_blg_tag ADD CONSTRAINT FK_A909724A8DCACB76 FOREIGN KEY (blg_page_id) REFERENCES blg_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_page_blg_tag ADD CONSTRAINT FK_A909724A886C3CC5 FOREIGN KEY (blg_tag_id) REFERENCES blg_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_section ADD CONSTRAINT FK_ACFFD27DF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_section ADD CONSTRAINT FK_ACFFD27D727ACA70 FOREIGN KEY (parent_id) REFERENCES blg_section (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE blg_section_blg_tag ADD CONSTRAINT FK_949539A0A464768F FOREIGN KEY (blg_section_id) REFERENCES blg_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_section_blg_tag ADD CONSTRAINT FK_949539A0886C3CC5 FOREIGN KEY (blg_tag_id) REFERENCES blg_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_tag ADD CONSTRAINT FK_BAC797ABF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE site CHANGE name name VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE blg_article DROP FOREIGN KEY FK_83B6A6F4F6BD1646');
        $this->addSql('ALTER TABLE blg_article DROP FOREIGN KEY FK_83B6A6F4F675F31B');
        $this->addSql('ALTER TABLE blg_article DROP FOREIGN KEY FK_83B6A6F412469DE2');
        $this->addSql('ALTER TABLE blg_article DROP FOREIGN KEY FK_83B6A6F4D823E37A');
        $this->addSql('ALTER TABLE blg_article_blg_tag DROP FOREIGN KEY FK_AB00B018ED31369');
        $this->addSql('ALTER TABLE blg_article_blg_tag DROP FOREIGN KEY FK_AB00B018886C3CC5');
        $this->addSql('ALTER TABLE blg_category DROP FOREIGN KEY FK_18CC6701F6BD1646');
        $this->addSql('ALTER TABLE blg_category DROP FOREIGN KEY FK_18CC6701727ACA70');
        $this->addSql('ALTER TABLE blg_category_blg_tag DROP FOREIGN KEY FK_7A8E06AFDFEDDCE4');
        $this->addSql('ALTER TABLE blg_category_blg_tag DROP FOREIGN KEY FK_7A8E06AF886C3CC5');
        $this->addSql('ALTER TABLE blg_comment DROP FOREIGN KEY FK_15F8FAFEF6BD1646');
        $this->addSql('ALTER TABLE blg_comment DROP FOREIGN KEY FK_15F8FAFEF675F31B');
        $this->addSql('ALTER TABLE blg_comment DROP FOREIGN KEY FK_15F8FAFE7294869C');
        $this->addSql('ALTER TABLE blg_comment DROP FOREIGN KEY FK_15F8FAFEC4663E4');
        $this->addSql('ALTER TABLE blg_comment DROP FOREIGN KEY FK_15F8FAFE727ACA70');
        $this->addSql('ALTER TABLE blg_like DROP FOREIGN KEY FK_996FA669F6BD1646');
        $this->addSql('ALTER TABLE blg_like DROP FOREIGN KEY FK_996FA669A76ED395');
        $this->addSql('ALTER TABLE blg_like DROP FOREIGN KEY FK_996FA6697294869C');
        $this->addSql('ALTER TABLE blg_like DROP FOREIGN KEY FK_996FA669C4663E4');
        $this->addSql('ALTER TABLE blg_like DROP FOREIGN KEY FK_996FA669F8697D13');
        $this->addSql('ALTER TABLE blg_page DROP FOREIGN KEY FK_210650FAF6BD1646');
        $this->addSql('ALTER TABLE blg_page DROP FOREIGN KEY FK_210650FAF675F31B');
        $this->addSql('ALTER TABLE blg_page DROP FOREIGN KEY FK_210650FA12469DE2');
        $this->addSql('ALTER TABLE blg_page DROP FOREIGN KEY FK_210650FAD823E37A');
        $this->addSql('ALTER TABLE blg_page_blg_tag DROP FOREIGN KEY FK_A909724A8DCACB76');
        $this->addSql('ALTER TABLE blg_page_blg_tag DROP FOREIGN KEY FK_A909724A886C3CC5');
        $this->addSql('ALTER TABLE blg_section DROP FOREIGN KEY FK_ACFFD27DF6BD1646');
        $this->addSql('ALTER TABLE blg_section DROP FOREIGN KEY FK_ACFFD27D727ACA70');
        $this->addSql('ALTER TABLE blg_section_blg_tag DROP FOREIGN KEY FK_949539A0A464768F');
        $this->addSql('ALTER TABLE blg_section_blg_tag DROP FOREIGN KEY FK_949539A0886C3CC5');
        $this->addSql('ALTER TABLE blg_tag DROP FOREIGN KEY FK_BAC797ABF6BD1646');
        $this->addSql('DROP TABLE blg_article');
        $this->addSql('DROP TABLE blg_article_blg_tag');
        $this->addSql('DROP TABLE blg_category');
        $this->addSql('DROP TABLE blg_category_blg_tag');
        $this->addSql('DROP TABLE blg_comment');
        $this->addSql('DROP TABLE blg_like');
        $this->addSql('DROP TABLE blg_page');
        $this->addSql('DROP TABLE blg_page_blg_tag');
        $this->addSql('DROP TABLE blg_section');
        $this->addSql('DROP TABLE blg_section_blg_tag');
        $this->addSql('DROP TABLE blg_tag');
        $this->addSql('ALTER TABLE site CHANGE name name VARCHAR(255) NOT NULL');
    }
}
