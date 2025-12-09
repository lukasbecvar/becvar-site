<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20251209093201
 * 
 * Migration for add first-visit-site column to visitors table
 * 
 * @package DoctrineMigrations
 */
final class Version20251209093201 extends AbstractMigration
{
    /**
     * Get description of this migration
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add first visit site to visitors table';
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
        $this->addSql('ALTER TABLE visitors ADD first_visit_site VARCHAR(255) NOT NULL');
        $this->addSql("UPDATE visitors SET first_visit_site = 'Unknown' WHERE first_visit_site IS NULL");
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
        $this->addSql('ALTER TABLE visitors DROP first_visit_site');
    }
}
