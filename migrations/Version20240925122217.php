<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20240925122217
 * 
 * The schema for create indexes
 * 
 * @package DoctrineMigrations
 */
final class Version20240925122217 extends AbstractMigration
{
    /**
     * Get the description of this migration
     * 
     * @return string
     */
    public function getDescription(): string
    {
        return 'Create indexes';
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
        $this->addSql('CREATE INDEX inbox_messages_name_idx ON inbox_messages (name)');
        $this->addSql('CREATE INDEX inbox_messages_email_idx ON inbox_messages (email)');
        $this->addSql('CREATE INDEX inbox_messages_status_idx ON inbox_messages (status)');
        $this->addSql('CREATE INDEX inbox_messages_ip_address_idx ON inbox_messages (ip_address)');
        $this->addSql('CREATE INDEX inbox_messages_visitor_id_idx ON inbox_messages (visitor_id)');
        $this->addSql('CREATE INDEX logs_name_idx ON logs (name)');
        $this->addSql('CREATE INDEX logs_status_idx ON logs (status)');
        $this->addSql('CREATE INDEX logs_ip_address_idx ON logs (ip_address)');
        $this->addSql('CREATE INDEX projects_name_idx ON projects (name)');
        $this->addSql('CREATE INDEX projects_status_idx ON projects (status)');
        $this->addSql('CREATE INDEX users_role_idx ON users (role)');
        $this->addSql('CREATE INDEX users_token_idx ON users (token)');
        $this->addSql('CREATE INDEX users_name_idx ON users (username)');
        $this->addSql('CREATE INDEX users_ip_address_idx ON users (ip_address)');
        $this->addSql('CREATE INDEX users_visitor_id_idx ON users (visitor_id)');
        $this->addSql('CREATE INDEX visitors_ip_address_idx ON visitors (ip_address)');
        $this->addSql('CREATE INDEX visitors_banned_status_idx ON visitors (banned_status)');
        $this->addSql('CREATE INDEX visitors_email_idx ON visitors (email)');
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
        $this->addSql('DROP INDEX users_role_idx ON users');
        $this->addSql('DROP INDEX users_token_idx ON users');
        $this->addSql('DROP INDEX users_name_idx ON users');
        $this->addSql('DROP INDEX users_ip_address_idx ON users');
        $this->addSql('DROP INDEX users_visitor_id_idx ON users');
        $this->addSql('DROP INDEX inbox_messages_name_idx ON inbox_messages');
        $this->addSql('DROP INDEX inbox_messages_email_idx ON inbox_messages');
        $this->addSql('DROP INDEX inbox_messages_status_idx ON inbox_messages');
        $this->addSql('DROP INDEX inbox_messages_ip_address_idx ON inbox_messages');
        $this->addSql('DROP INDEX inbox_messages_visitor_id_idx ON inbox_messages');
        $this->addSql('DROP INDEX projects_name_idx ON projects');
        $this->addSql('DROP INDEX projects_status_idx ON projects');
        $this->addSql('DROP INDEX visitors_ip_address_idx ON visitors');
        $this->addSql('DROP INDEX visitors_banned_status_idx ON visitors');
        $this->addSql('DROP INDEX visitors_email_idx ON visitors');
        $this->addSql('DROP INDEX logs_name_idx ON logs');
        $this->addSql('DROP INDEX logs_status_idx ON logs');
        $this->addSql('DROP INDEX logs_ip_address_idx ON logs');
    }
}
