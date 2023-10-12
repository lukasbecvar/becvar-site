<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231012082601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visitor ADD ip_address VARCHAR(255) NOT NULL, ADD banned_status VARCHAR(255) NOT NULL, ADD ban_reason VARCHAR(255) NOT NULL, ADD banned_time VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, DROP ip_adress, CHANGE visited_sites visited_sites VARCHAR(255) NOT NULL, CHANGE first_visit first_visit VARCHAR(255) NOT NULL, CHANGE last_visit last_visit VARCHAR(255) NOT NULL, CHANGE browser browser VARCHAR(255) NOT NULL, CHANGE os os VARCHAR(255) NOT NULL, CHANGE location location VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visitor ADD ip_adress CHAR(255) CHARACTER SET cp1250 NOT NULL COLLATE `cp1250_bin`, DROP ip_address, DROP banned_status, DROP ban_reason, DROP banned_time, DROP email, CHANGE visited_sites visited_sites INT NOT NULL, CHANGE first_visit first_visit CHAR(255) CHARACTER SET cp1250 NOT NULL COLLATE `cp1250_bin`, CHANGE last_visit last_visit CHAR(255) CHARACTER SET cp1250 NOT NULL COLLATE `cp1250_bin`, CHANGE browser browser CHAR(255) CHARACTER SET cp1250 NOT NULL COLLATE `cp1250_bin`, CHANGE os os CHAR(255) CHARACTER SET cp1250 NOT NULL COLLATE `cp1250_bin`, CHANGE location location CHAR(255) CHARACTER SET cp1250 NOT NULL COLLATE `cp1250_bin`');
    }
}
