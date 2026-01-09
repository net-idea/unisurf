<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\MariaDbPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;

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
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDbPlatform) {
            $this->addSql('DROP TABLE IF EXISTS `form_contact`');
            $this->addSql('DROP TABLE IF EXISTS `form_submission_meta`');
            $this->addSql('CREATE TABLE `form_submission_meta` (`id` INT AUTO_INCREMENT NOT NULL, `ip` VARCHAR(64) DEFAULT NULL, `user_agent` VARCHAR(400) DEFAULT NULL, `time` VARCHAR(40) DEFAULT NULL, `host` VARCHAR(200) DEFAULT NULL, PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE `form_contact` (`id` INT AUTO_INCREMENT NOT NULL, `meta_id` INT DEFAULT NULL, `name` VARCHAR(160) NOT NULL, `email_address` VARCHAR(200) NOT NULL, `phone` VARCHAR(40) DEFAULT NULL, `consent` TINYINT(1) NOT NULL, `message` LONGTEXT NOT NULL, `copy` TINYINT(1) NOT NULL, `created_at` DATETIME NOT NULL COMMENT "(DC2Type:datetime_immutable)", INDEX IDX_D029D7C65F13E7C (`meta_id`), UNIQUE INDEX UNIQ_D029D7C65F13E7C (`meta_id`), PRIMARY KEY(`id`), CONSTRAINT FK_D029D7C65F13E7C FOREIGN KEY (`meta_id`) REFERENCES `form_submission_meta` (`id`) ON DELETE SET NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            return;
        }

        if ($platform instanceof SqlitePlatform) {
            // Create form_submission_meta table if it doesn't exist
            if (!$schema->hasTable('form_submission_meta')) {
                $this->addSql('CREATE TABLE form_submission_meta (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ip VARCHAR(64) DEFAULT NULL, user_agent VARCHAR(400) DEFAULT NULL, time VARCHAR(40) DEFAULT NULL, host VARCHAR(200) DEFAULT NULL)');
            }

            // Create form_contact table if it doesn't exist
            if (!$schema->hasTable('form_contact')) {
                $this->addSql('CREATE TABLE form_contact (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, meta_id INTEGER DEFAULT NULL, name VARCHAR(160) NOT NULL, email_address VARCHAR(200) NOT NULL, phone VARCHAR(40) DEFAULT NULL, consent BOOLEAN NOT NULL, message CLOB NOT NULL, copy BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable), CONSTRAINT FK_D029D7C65F13E7C FOREIGN KEY (meta_id) REFERENCES form_submission_meta (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
                $this->addSql('CREATE UNIQUE INDEX UNIQ_D029D7C65F13E7C ON form_contact (meta_id)');
            }

            return;
        }

        throw new \RuntimeException(sprintf('Unsupported database platform: %s', get_class($platform)));
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof MySQLPlatform || $platform instanceof MariaDbPlatform) {
            $this->addSql('DROP TABLE IF EXISTS `form_contact`');
            $this->addSql('DROP TABLE IF EXISTS `form_submission_meta`');
            return;
        }

        if ($platform instanceof SqlitePlatform) {
            $this->addSql('DROP TABLE form_contact');
            $this->addSql('DROP TABLE form_submission_meta');

            return;
        }

        throw new \RuntimeException(sprintf('Unsupported database platform: %s', get_class($platform)));
    }
}
