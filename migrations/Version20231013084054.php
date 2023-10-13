<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231013084054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image CHANGE date time VARCHAR(2555) NOT NULL');
        $this->addSql('ALTER TABLE log CHANGE date time VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE paste CHANGE date time VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE todo ADD added_time VARCHAR(255) NOT NULL, ADD completed_time VARCHAR(255) NOT NULL, DROP added_date, DROP completed_date');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image CHANGE time date VARCHAR(2555) NOT NULL');
        $this->addSql('ALTER TABLE todo ADD added_date VARCHAR(255) NOT NULL, ADD completed_date VARCHAR(255) NOT NULL, DROP added_time, DROP completed_time');
        $this->addSql('ALTER TABLE log CHANGE time date VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE paste CHANGE time date VARCHAR(255) NOT NULL');
    }
}
