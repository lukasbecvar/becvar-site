<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240901160134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C93B3A436AC99F1 ON projects (link)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E95F37A13B ON users (token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B74A43F22FFD58C ON visitors (ip_address)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_1483A5E9F85E0677 ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E95F37A13B ON users');
        $this->addSql('DROP INDEX UNIQ_5C93B3A436AC99F1 ON projects');
        $this->addSql('DROP INDEX UNIQ_7B74A43F22FFD58C ON visitors');
    }
}
