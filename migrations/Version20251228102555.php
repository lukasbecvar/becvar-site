<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20251228102555
 * 
 * Migration for add foreign keys to database
 * 
 * @package DoctrineMigrations
 */
final class Version20251228102555 extends AbstractMigration
{
    /**
     * Get description of this migration
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add foreign keys to database';
    }

    /**
     * Execute migration
     * 
     * @param Schema $schema
     * 
     * @return void
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inbox_messages CHANGE visitor_id visitor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inbox_messages ADD CONSTRAINT FK_F635C51D70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id)');
        $this->addSql('ALTER TABLE logs CHANGE visitor_id visitor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65C70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id)');
        $this->addSql('CREATE INDEX IDX_F08FC65C70BEE6D ON logs (visitor_id)');
        $this->addSql('ALTER TABLE users CHANGE visitor_id visitor_id INT DEFAULT NULL, CHANGE registed_time registered_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E970BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id)');
    }

    /**
     * Undo migration
     *
     * @param Schema $schema
     * 
     * @return void
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inbox_messages DROP FOREIGN KEY FK_F635C51D70BEE6D');
        $this->addSql('ALTER TABLE inbox_messages CHANGE visitor_id visitor_id INT NOT NULL');
        $this->addSql('ALTER TABLE logs DROP FOREIGN KEY FK_F08FC65C70BEE6D');
        $this->addSql('DROP INDEX IDX_F08FC65C70BEE6D ON logs');
        $this->addSql('ALTER TABLE logs CHANGE visitor_id visitor_id INT NOT NULL');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E970BEE6D');
        $this->addSql('ALTER TABLE users CHANGE visitor_id visitor_id INT NOT NULL, CHANGE registered_time registed_time DATETIME NOT NULL');
    }
}
