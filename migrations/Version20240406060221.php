<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240406060221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat_messages (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT NOT NULL, sender VARCHAR(255) NOT NULL, day VARCHAR(255) NOT NULL, time VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE code_paste (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, time VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, image LONGTEXT NOT NULL, time VARCHAR(2555) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inbox_messages (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, time VARCHAR(255) NOT NULL, ip_address VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, visitor_id VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, time VARCHAR(255) NOT NULL, ip_address VARCHAR(255) NOT NULL, browser VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, visitor_id VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, technology VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE todos (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, added_time VARCHAR(255) NOT NULL, completed_time VARCHAR(255) NOT NULL, added_by VARCHAR(255) NOT NULL, closed_by VARCHAR(2555) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, ip_address VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, registed_time VARCHAR(255) NOT NULL, last_login_time VARCHAR(255) NOT NULL, profile_pic LONGTEXT NOT NULL, visitor_id VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visitors (id INT AUTO_INCREMENT NOT NULL, first_visit VARCHAR(255) NOT NULL, last_visit VARCHAR(255) NOT NULL, browser VARCHAR(255) NOT NULL, os VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, ip_address VARCHAR(255) NOT NULL, banned_status VARCHAR(255) NOT NULL, ban_reason VARCHAR(255) NOT NULL, banned_time VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, status_update_time VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE chat_messages');
        $this->addSql('DROP TABLE code_paste');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE inbox_messages');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE todos');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE visitors');
    }
}
