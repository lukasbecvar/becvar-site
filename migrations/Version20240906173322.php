<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20240906173322
 * 
 * The default database schema
 * 
 * @package DoctrineMigrations
 */
final class Version20240906173322 extends AbstractMigration
{
    /**
     * Get the description of this migration
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return 'Default database schema';
    }

    /**
     * Execute the migration
     * 
     * @param Schema $schema
     * 
     * @return void
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE inbox_messages (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, time VARCHAR(255) NOT NULL, ip_address VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, visitor_id VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, time VARCHAR(255) NOT NULL, ip_address VARCHAR(255) NOT NULL, browser VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, visitor_id VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, technology VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5C93B3A45E237E06 (name), UNIQUE INDEX UNIQ_5C93B3A436AC99F1 (link), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, ip_address VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, registed_time VARCHAR(255) NOT NULL, last_login_time VARCHAR(255) NOT NULL, profile_pic LONGTEXT NOT NULL, visitor_id VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E95F37A13B (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visitors (id INT AUTO_INCREMENT NOT NULL, first_visit VARCHAR(255) NOT NULL, last_visit VARCHAR(255) NOT NULL, browser VARCHAR(255) NOT NULL, os VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, ip_address VARCHAR(255) NOT NULL, banned_status VARCHAR(255) NOT NULL, ban_reason VARCHAR(255) NOT NULL, banned_time VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    /**
     * Undo the migration
     *
     * @param Schema $schema
     * 
     * @return void
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE inbox_messages');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE visitors');
    }
}
