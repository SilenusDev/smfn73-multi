<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251013114354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blg_article_blg_tag (blg_article_id INT NOT NULL, blg_tag_id INT NOT NULL, INDEX IDX_AB00B018ED31369 (blg_article_id), INDEX IDX_AB00B018886C3CC5 (blg_tag_id), PRIMARY KEY(blg_article_id, blg_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_category_blg_tag (blg_category_id INT NOT NULL, blg_tag_id INT NOT NULL, INDEX IDX_7A8E06AFDFEDDCE4 (blg_category_id), INDEX IDX_7A8E06AF886C3CC5 (blg_tag_id), PRIMARY KEY(blg_category_id, blg_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_page_blg_tag (blg_page_id INT NOT NULL, blg_tag_id INT NOT NULL, INDEX IDX_A909724A8DCACB76 (blg_page_id), INDEX IDX_A909724A886C3CC5 (blg_tag_id), PRIMARY KEY(blg_page_id, blg_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blg_section_blg_tag (blg_section_id INT NOT NULL, blg_tag_id INT NOT NULL, INDEX IDX_949539A0A464768F (blg_section_id), INDEX IDX_949539A0886C3CC5 (blg_tag_id), PRIMARY KEY(blg_section_id, blg_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blg_article_blg_tag ADD CONSTRAINT FK_AB00B018ED31369 FOREIGN KEY (blg_article_id) REFERENCES blg_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_article_blg_tag ADD CONSTRAINT FK_AB00B018886C3CC5 FOREIGN KEY (blg_tag_id) REFERENCES blg_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_category_blg_tag ADD CONSTRAINT FK_7A8E06AFDFEDDCE4 FOREIGN KEY (blg_category_id) REFERENCES blg_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_category_blg_tag ADD CONSTRAINT FK_7A8E06AF886C3CC5 FOREIGN KEY (blg_tag_id) REFERENCES blg_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_page_blg_tag ADD CONSTRAINT FK_A909724A8DCACB76 FOREIGN KEY (blg_page_id) REFERENCES blg_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_page_blg_tag ADD CONSTRAINT FK_A909724A886C3CC5 FOREIGN KEY (blg_tag_id) REFERENCES blg_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_section_blg_tag ADD CONSTRAINT FK_949539A0A464768F FOREIGN KEY (blg_section_id) REFERENCES blg_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_section_blg_tag ADD CONSTRAINT FK_949539A0886C3CC5 FOREIGN KEY (blg_tag_id) REFERENCES blg_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blg_article ADD site_id INT NOT NULL, ADD author_id INT NOT NULL, ADD category_id INT DEFAULT NULL, ADD section_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL, ADD titre VARCHAR(255) NOT NULL, ADD title VARCHAR(255) DEFAULT NULL, ADD content LONGTEXT DEFAULT NULL, ADD contenu LONGTEXT DEFAULT NULL, ADD slug_fr VARCHAR(254) NOT NULL, ADD slug_en VARCHAR(254) DEFAULT NULL, ADD descr_fr VARCHAR(254) NOT NULL, ADD descr_en VARCHAR(254) DEFAULT NULL, ADD tags_fr VARCHAR(254) DEFAULT NULL, ADD tags_en VARCHAR(254) DEFAULT NULL, ADD url_fr VARCHAR(254) DEFAULT NULL, ADD url_en VARCHAR(254) DEFAULT NULL, ADD img VARCHAR(254) DEFAULT NULL, ADD status VARCHAR(20) NOT NULL, ADD view_count INT DEFAULT 0 NOT NULL, ADD like_count INT DEFAULT 0 NOT NULL, ADD comment_count INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE blg_article ADD CONSTRAINT FK_83B6A6F4F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_article ADD CONSTRAINT FK_83B6A6F4F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blg_article ADD CONSTRAINT FK_83B6A6F412469DE2 FOREIGN KEY (category_id) REFERENCES blg_category (id)');
        $this->addSql('ALTER TABLE blg_article ADD CONSTRAINT FK_83B6A6F4D823E37A FOREIGN KEY (section_id) REFERENCES blg_section (id)');
        $this->addSql('CREATE INDEX IDX_83B6A6F4F6BD1646 ON blg_article (site_id)');
        $this->addSql('CREATE INDEX IDX_83B6A6F4F675F31B ON blg_article (author_id)');
        $this->addSql('CREATE INDEX IDX_83B6A6F412469DE2 ON blg_article (category_id)');
        $this->addSql('CREATE INDEX IDX_83B6A6F4D823E37A ON blg_article (section_id)');
        $this->addSql('ALTER TABLE blg_category ADD site_id INT NOT NULL, ADD parent_id INT DEFAULT NULL, ADD titre VARCHAR(255) NOT NULL, ADD title VARCHAR(255) DEFAULT NULL, ADD slug_fr VARCHAR(254) NOT NULL, ADD slug_en VARCHAR(254) DEFAULT NULL, ADD descr_fr VARCHAR(254) NOT NULL, ADD descr_en VARCHAR(254) DEFAULT NULL, ADD tags_fr VARCHAR(254) DEFAULT NULL, ADD tags_en VARCHAR(254) DEFAULT NULL, ADD url_fr VARCHAR(254) DEFAULT NULL, ADD url_en VARCHAR(254) DEFAULT NULL, ADD img VARCHAR(254) DEFAULT NULL');
        $this->addSql('ALTER TABLE blg_category ADD CONSTRAINT FK_18CC6701F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_category ADD CONSTRAINT FK_18CC6701727ACA70 FOREIGN KEY (parent_id) REFERENCES blg_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_18CC6701F6BD1646 ON blg_category (site_id)');
        $this->addSql('CREATE INDEX IDX_18CC6701727ACA70 ON blg_category (parent_id)');
        $this->addSql('ALTER TABLE blg_comment ADD site_id INT NOT NULL, ADD author_id INT NOT NULL, ADD article_id INT DEFAULT NULL, ADD page_id INT DEFAULT NULL, ADD parent_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL, ADD content LONGTEXT DEFAULT NULL, ADD contenu LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE blg_comment ADD CONSTRAINT FK_15F8FAFEF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_comment ADD CONSTRAINT FK_15F8FAFEF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blg_comment ADD CONSTRAINT FK_15F8FAFE7294869C FOREIGN KEY (article_id) REFERENCES blg_article (id)');
        $this->addSql('ALTER TABLE blg_comment ADD CONSTRAINT FK_15F8FAFEC4663E4 FOREIGN KEY (page_id) REFERENCES blg_page (id)');
        $this->addSql('ALTER TABLE blg_comment ADD CONSTRAINT FK_15F8FAFE727ACA70 FOREIGN KEY (parent_id) REFERENCES blg_comment (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_15F8FAFEF6BD1646 ON blg_comment (site_id)');
        $this->addSql('CREATE INDEX IDX_15F8FAFEF675F31B ON blg_comment (author_id)');
        $this->addSql('CREATE INDEX IDX_15F8FAFE7294869C ON blg_comment (article_id)');
        $this->addSql('CREATE INDEX IDX_15F8FAFEC4663E4 ON blg_comment (page_id)');
        $this->addSql('CREATE INDEX IDX_15F8FAFE727ACA70 ON blg_comment (parent_id)');
        $this->addSql('ALTER TABLE blg_like ADD site_id INT NOT NULL, ADD user_id INT NOT NULL, ADD article_id INT DEFAULT NULL, ADD page_id INT DEFAULT NULL, ADD comment_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE blg_like ADD CONSTRAINT FK_996FA669F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_like ADD CONSTRAINT FK_996FA669A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blg_like ADD CONSTRAINT FK_996FA6697294869C FOREIGN KEY (article_id) REFERENCES blg_article (id)');
        $this->addSql('ALTER TABLE blg_like ADD CONSTRAINT FK_996FA669C4663E4 FOREIGN KEY (page_id) REFERENCES blg_page (id)');
        $this->addSql('ALTER TABLE blg_like ADD CONSTRAINT FK_996FA669F8697D13 FOREIGN KEY (comment_id) REFERENCES blg_comment (id)');
        $this->addSql('CREATE INDEX IDX_996FA669F6BD1646 ON blg_like (site_id)');
        $this->addSql('CREATE INDEX IDX_996FA669A76ED395 ON blg_like (user_id)');
        $this->addSql('CREATE INDEX IDX_996FA6697294869C ON blg_like (article_id)');
        $this->addSql('CREATE INDEX IDX_996FA669C4663E4 ON blg_like (page_id)');
        $this->addSql('CREATE INDEX IDX_996FA669F8697D13 ON blg_like (comment_id)');
        $this->addSql('ALTER TABLE blg_page ADD site_id INT NOT NULL, ADD author_id INT NOT NULL, ADD category_id INT DEFAULT NULL, ADD section_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL, ADD titre VARCHAR(255) NOT NULL, ADD title VARCHAR(255) DEFAULT NULL, ADD content LONGTEXT DEFAULT NULL, ADD contenu LONGTEXT DEFAULT NULL, ADD slug_fr VARCHAR(254) NOT NULL, ADD slug_en VARCHAR(254) DEFAULT NULL, ADD descr_fr VARCHAR(254) NOT NULL, ADD descr_en VARCHAR(254) DEFAULT NULL, ADD tags_fr VARCHAR(254) DEFAULT NULL, ADD tags_en VARCHAR(254) DEFAULT NULL, ADD url_fr VARCHAR(254) DEFAULT NULL, ADD url_en VARCHAR(254) DEFAULT NULL, ADD img VARCHAR(254) DEFAULT NULL, ADD status VARCHAR(20) NOT NULL, ADD view_count INT DEFAULT 0 NOT NULL, ADD like_count INT DEFAULT 0 NOT NULL, ADD comment_count INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE blg_page ADD CONSTRAINT FK_210650FAF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_page ADD CONSTRAINT FK_210650FAF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blg_page ADD CONSTRAINT FK_210650FA12469DE2 FOREIGN KEY (category_id) REFERENCES blg_category (id)');
        $this->addSql('ALTER TABLE blg_page ADD CONSTRAINT FK_210650FAD823E37A FOREIGN KEY (section_id) REFERENCES blg_section (id)');
        $this->addSql('CREATE INDEX IDX_210650FAF6BD1646 ON blg_page (site_id)');
        $this->addSql('CREATE INDEX IDX_210650FAF675F31B ON blg_page (author_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_210650FA12469DE2 ON blg_page (category_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_210650FAD823E37A ON blg_page (section_id)');
        $this->addSql('ALTER TABLE blg_section ADD site_id INT NOT NULL, ADD parent_id INT DEFAULT NULL, ADD titre VARCHAR(255) NOT NULL, ADD title VARCHAR(255) DEFAULT NULL, ADD slug_fr VARCHAR(254) NOT NULL, ADD slug_en VARCHAR(254) DEFAULT NULL, ADD descr_fr VARCHAR(254) NOT NULL, ADD descr_en VARCHAR(254) DEFAULT NULL, ADD tags_fr VARCHAR(254) DEFAULT NULL, ADD tags_en VARCHAR(254) DEFAULT NULL, ADD url_fr VARCHAR(254) DEFAULT NULL, ADD url_en VARCHAR(254) DEFAULT NULL, ADD img VARCHAR(254) DEFAULT NULL');
        $this->addSql('ALTER TABLE blg_section ADD CONSTRAINT FK_ACFFD27DF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE blg_section ADD CONSTRAINT FK_ACFFD27D727ACA70 FOREIGN KEY (parent_id) REFERENCES blg_section (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_ACFFD27DF6BD1646 ON blg_section (site_id)');
        $this->addSql('CREATE INDEX IDX_ACFFD27D727ACA70 ON blg_section (parent_id)');
        $this->addSql('ALTER TABLE blg_tag ADD site_id INT NOT NULL, ADD titre VARCHAR(255) NOT NULL, ADD title VARCHAR(255) DEFAULT NULL, ADD slug_fr VARCHAR(254) NOT NULL, ADD slug_en VARCHAR(254) DEFAULT NULL, ADD descr_fr VARCHAR(254) NOT NULL, ADD descr_en VARCHAR(254) DEFAULT NULL, ADD tags_fr VARCHAR(254) DEFAULT NULL, ADD tags_en VARCHAR(254) DEFAULT NULL, ADD url_fr VARCHAR(254) DEFAULT NULL, ADD url_en VARCHAR(254) DEFAULT NULL, ADD img VARCHAR(254) DEFAULT NULL');
        $this->addSql('ALTER TABLE blg_tag ADD CONSTRAINT FK_BAC797ABF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('CREATE INDEX IDX_BAC797ABF6BD1646 ON blg_tag (site_id)');
        $this->addSql('ALTER TABLE site CHANGE name name VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blg_article_blg_tag DROP FOREIGN KEY FK_AB00B018ED31369');
        $this->addSql('ALTER TABLE blg_article_blg_tag DROP FOREIGN KEY FK_AB00B018886C3CC5');
        $this->addSql('ALTER TABLE blg_category_blg_tag DROP FOREIGN KEY FK_7A8E06AFDFEDDCE4');
        $this->addSql('ALTER TABLE blg_category_blg_tag DROP FOREIGN KEY FK_7A8E06AF886C3CC5');
        $this->addSql('ALTER TABLE blg_page_blg_tag DROP FOREIGN KEY FK_A909724A8DCACB76');
        $this->addSql('ALTER TABLE blg_page_blg_tag DROP FOREIGN KEY FK_A909724A886C3CC5');
        $this->addSql('ALTER TABLE blg_section_blg_tag DROP FOREIGN KEY FK_949539A0A464768F');
        $this->addSql('ALTER TABLE blg_section_blg_tag DROP FOREIGN KEY FK_949539A0886C3CC5');
        $this->addSql('DROP TABLE blg_article_blg_tag');
        $this->addSql('DROP TABLE blg_category_blg_tag');
        $this->addSql('DROP TABLE blg_page_blg_tag');
        $this->addSql('DROP TABLE blg_section_blg_tag');
        $this->addSql('ALTER TABLE site CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE blg_comment DROP FOREIGN KEY FK_15F8FAFEF6BD1646');
        $this->addSql('ALTER TABLE blg_comment DROP FOREIGN KEY FK_15F8FAFEF675F31B');
        $this->addSql('ALTER TABLE blg_comment DROP FOREIGN KEY FK_15F8FAFE7294869C');
        $this->addSql('ALTER TABLE blg_comment DROP FOREIGN KEY FK_15F8FAFEC4663E4');
        $this->addSql('ALTER TABLE blg_comment DROP FOREIGN KEY FK_15F8FAFE727ACA70');
        $this->addSql('DROP INDEX IDX_15F8FAFEF6BD1646 ON blg_comment');
        $this->addSql('DROP INDEX IDX_15F8FAFEF675F31B ON blg_comment');
        $this->addSql('DROP INDEX IDX_15F8FAFE7294869C ON blg_comment');
        $this->addSql('DROP INDEX IDX_15F8FAFEC4663E4 ON blg_comment');
        $this->addSql('DROP INDEX IDX_15F8FAFE727ACA70 ON blg_comment');
        $this->addSql('ALTER TABLE blg_comment DROP site_id, DROP author_id, DROP article_id, DROP page_id, DROP parent_id, DROP created_at, DROP updated_at, DROP content, DROP contenu');
        $this->addSql('ALTER TABLE blg_tag DROP FOREIGN KEY FK_BAC797ABF6BD1646');
        $this->addSql('DROP INDEX IDX_BAC797ABF6BD1646 ON blg_tag');
        $this->addSql('ALTER TABLE blg_tag DROP site_id, DROP titre, DROP title, DROP slug_fr, DROP slug_en, DROP descr_fr, DROP descr_en, DROP tags_fr, DROP tags_en, DROP url_fr, DROP url_en, DROP img');
        $this->addSql('ALTER TABLE blg_page DROP FOREIGN KEY FK_210650FAF6BD1646');
        $this->addSql('ALTER TABLE blg_page DROP FOREIGN KEY FK_210650FAF675F31B');
        $this->addSql('ALTER TABLE blg_page DROP FOREIGN KEY FK_210650FA12469DE2');
        $this->addSql('ALTER TABLE blg_page DROP FOREIGN KEY FK_210650FAD823E37A');
        $this->addSql('DROP INDEX IDX_210650FAF6BD1646 ON blg_page');
        $this->addSql('DROP INDEX IDX_210650FAF675F31B ON blg_page');
        $this->addSql('DROP INDEX UNIQ_210650FA12469DE2 ON blg_page');
        $this->addSql('DROP INDEX UNIQ_210650FAD823E37A ON blg_page');
        $this->addSql('ALTER TABLE blg_page DROP site_id, DROP author_id, DROP category_id, DROP section_id, DROP created_at, DROP updated_at, DROP titre, DROP title, DROP content, DROP contenu, DROP slug_fr, DROP slug_en, DROP descr_fr, DROP descr_en, DROP tags_fr, DROP tags_en, DROP url_fr, DROP url_en, DROP img, DROP status, DROP view_count, DROP like_count, DROP comment_count');
        $this->addSql('ALTER TABLE blg_like DROP FOREIGN KEY FK_996FA669F6BD1646');
        $this->addSql('ALTER TABLE blg_like DROP FOREIGN KEY FK_996FA669A76ED395');
        $this->addSql('ALTER TABLE blg_like DROP FOREIGN KEY FK_996FA6697294869C');
        $this->addSql('ALTER TABLE blg_like DROP FOREIGN KEY FK_996FA669C4663E4');
        $this->addSql('ALTER TABLE blg_like DROP FOREIGN KEY FK_996FA669F8697D13');
        $this->addSql('DROP INDEX IDX_996FA669F6BD1646 ON blg_like');
        $this->addSql('DROP INDEX IDX_996FA669A76ED395 ON blg_like');
        $this->addSql('DROP INDEX IDX_996FA6697294869C ON blg_like');
        $this->addSql('DROP INDEX IDX_996FA669C4663E4 ON blg_like');
        $this->addSql('DROP INDEX IDX_996FA669F8697D13 ON blg_like');
        $this->addSql('ALTER TABLE blg_like DROP site_id, DROP user_id, DROP article_id, DROP page_id, DROP comment_id, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE blg_article DROP FOREIGN KEY FK_83B6A6F4F6BD1646');
        $this->addSql('ALTER TABLE blg_article DROP FOREIGN KEY FK_83B6A6F4F675F31B');
        $this->addSql('ALTER TABLE blg_article DROP FOREIGN KEY FK_83B6A6F412469DE2');
        $this->addSql('ALTER TABLE blg_article DROP FOREIGN KEY FK_83B6A6F4D823E37A');
        $this->addSql('DROP INDEX IDX_83B6A6F4F6BD1646 ON blg_article');
        $this->addSql('DROP INDEX IDX_83B6A6F4F675F31B ON blg_article');
        $this->addSql('DROP INDEX IDX_83B6A6F412469DE2 ON blg_article');
        $this->addSql('DROP INDEX IDX_83B6A6F4D823E37A ON blg_article');
        $this->addSql('ALTER TABLE blg_article DROP site_id, DROP author_id, DROP category_id, DROP section_id, DROP created_at, DROP updated_at, DROP titre, DROP title, DROP content, DROP contenu, DROP slug_fr, DROP slug_en, DROP descr_fr, DROP descr_en, DROP tags_fr, DROP tags_en, DROP url_fr, DROP url_en, DROP img, DROP status, DROP view_count, DROP like_count, DROP comment_count');
        $this->addSql('ALTER TABLE blg_category DROP FOREIGN KEY FK_18CC6701F6BD1646');
        $this->addSql('ALTER TABLE blg_category DROP FOREIGN KEY FK_18CC6701727ACA70');
        $this->addSql('DROP INDEX IDX_18CC6701F6BD1646 ON blg_category');
        $this->addSql('DROP INDEX IDX_18CC6701727ACA70 ON blg_category');
        $this->addSql('ALTER TABLE blg_category DROP site_id, DROP parent_id, DROP titre, DROP title, DROP slug_fr, DROP slug_en, DROP descr_fr, DROP descr_en, DROP tags_fr, DROP tags_en, DROP url_fr, DROP url_en, DROP img');
        $this->addSql('ALTER TABLE blg_section DROP FOREIGN KEY FK_ACFFD27DF6BD1646');
        $this->addSql('ALTER TABLE blg_section DROP FOREIGN KEY FK_ACFFD27D727ACA70');
        $this->addSql('DROP INDEX IDX_ACFFD27DF6BD1646 ON blg_section');
        $this->addSql('DROP INDEX IDX_ACFFD27D727ACA70 ON blg_section');
        $this->addSql('ALTER TABLE blg_section DROP site_id, DROP parent_id, DROP titre, DROP title, DROP slug_fr, DROP slug_en, DROP descr_fr, DROP descr_en, DROP tags_fr, DROP tags_en, DROP url_fr, DROP url_en, DROP img');
    }
}
