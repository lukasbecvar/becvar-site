<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231021072755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE todos CHANGE text text LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE visitors DROP login_attempts');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visitors ADD login_attempts VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE todos CHANGE text text VARCHAR(255) NOT NULL');
    }
}
