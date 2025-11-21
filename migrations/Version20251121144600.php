<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Create form_submission_meta and form_contact tables
 */
final class Version20251121144600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create form_submission_meta and form_contact tables for contact form functionality';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        // Create form_submission_meta table if it doesn't exist
        if (!$schema->hasTable('form_submission_meta')) {
            $this->addSql('CREATE TABLE form_submission_meta (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ip VARCHAR(64) DEFAULT NULL, user_agent VARCHAR(400) DEFAULT NULL, time VARCHAR(40) DEFAULT NULL, host VARCHAR(200) DEFAULT NULL)');
        }

        // Create form_contact table if it doesn't exist
        if (!$schema->hasTable('form_contact')) {
            $this->addSql('CREATE TABLE form_contact (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, meta_id INTEGER DEFAULT NULL, name VARCHAR(160) NOT NULL, email_address VARCHAR(200) NOT NULL, phone VARCHAR(40) DEFAULT NULL, consent BOOLEAN NOT NULL, message CLOB NOT NULL, copy BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable), CONSTRAINT FK_D029D7C65F13E7C FOREIGN KEY (meta_id) REFERENCES form_submission_meta (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_D029D7C65F13E7C ON form_contact (meta_id)');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE form_contact');
        $this->addSql('DROP TABLE form_submission_meta');
    }
}
