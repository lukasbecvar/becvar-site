<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250113135246
 * 
 * Migration add referer to visitors table
 * 
 * @package DoctrineMigrations
 */
final class Version20250113135246 extends AbstractMigration
{
    /**
     * Get the description of this migration
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add referer to visitors table';
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
        // add referer column to visitors table
        $this->addSql('ALTER TABLE visitors ADD referer VARCHAR(255) NOT NULL');

        // set default referer to unknown
        $this->addSql("UPDATE visitors SET referer = 'Unknown' WHERE referer IS NULL OR referer = ''");
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
        $this->addSql('ALTER TABLE visitors DROP referer');
    }
}
