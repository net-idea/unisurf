<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251121134702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE form_contact (name VARCHAR(160) NOT NULL, email_address VARCHAR(200) NOT NULL, phone VARCHAR(40) DEFAULT NULL, consent BOOLEAN NOT NULL, message CLOB NOT NULL, copy BOOLEAN NOT NULL, id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_at DATETIME NOT NULL, meta_id INTEGER DEFAULT NULL, CONSTRAINT FK_7D0E860339FCA6F9 FOREIGN KEY (meta_id) REFERENCES form_submission_meta (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D0E860339FCA6F9 ON form_contact (meta_id)');
        $this->addSql('CREATE TABLE form_submission_meta (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ip VARCHAR(64) DEFAULT NULL, user_agent VARCHAR(400) DEFAULT NULL, time VARCHAR(40) DEFAULT NULL, host VARCHAR(200) DEFAULT NULL)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE form_contact');
        $this->addSql('DROP TABLE form_submission_meta');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
