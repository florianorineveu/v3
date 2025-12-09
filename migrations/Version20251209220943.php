<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209220943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Replace ownerType/ownerId polymorphic association with real FK to Post and Project';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Add new FK columns (nullable temporarily)
        $this->addSql('ALTER TABLE content_block ADD post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE content_block ADD project_id INT DEFAULT NULL');

        // Step 2: Migrate data from ownerType/ownerId to FK columns
        $this->addSql("UPDATE content_block SET post_id = owner_id WHERE owner_type = 'blog_post'");
        $this->addSql("UPDATE content_block SET project_id = owner_id WHERE owner_type = 'portfolio_project'");

        // Step 3: Drop old columns and indexes
        $this->addSql('DROP INDEX idx_content_block_owner');
        $this->addSql('DROP INDEX idx_content_block_owner_position');
        $this->addSql('ALTER TABLE content_block DROP owner_type');
        $this->addSql('ALTER TABLE content_block DROP owner_id');

        // Step 4: Add FK constraints with CASCADE DELETE
        $this->addSql('ALTER TABLE content_block ADD CONSTRAINT FK_68D8C3F04B89032C FOREIGN KEY (post_id) REFERENCES blog_post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_block ADD CONSTRAINT FK_68D8C3F0166D1F9C FOREIGN KEY (project_id) REFERENCES portfolio_project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Step 5: Create new indexes
        $this->addSql('CREATE INDEX idx_content_block_post ON content_block (post_id)');
        $this->addSql('CREATE INDEX idx_content_block_project ON content_block (project_id)');
    }

    public function down(Schema $schema): void
    {
        // Step 1: Add old columns back (nullable temporarily)
        $this->addSql('ALTER TABLE content_block ADD owner_type VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE content_block ADD owner_id INT DEFAULT NULL');

        // Step 2: Migrate data back from FK to ownerType/ownerId
        $this->addSql("UPDATE content_block SET owner_type = 'blog_post', owner_id = post_id WHERE post_id IS NOT NULL");
        $this->addSql("UPDATE content_block SET owner_type = 'portfolio_project', owner_id = project_id WHERE project_id IS NOT NULL");

        // Step 3: Make old columns NOT NULL
        $this->addSql('ALTER TABLE content_block ALTER COLUMN owner_type SET NOT NULL');
        $this->addSql('ALTER TABLE content_block ALTER COLUMN owner_id SET NOT NULL');

        // Step 4: Drop FK constraints and indexes
        $this->addSql('ALTER TABLE content_block DROP CONSTRAINT FK_68D8C3F04B89032C');
        $this->addSql('ALTER TABLE content_block DROP CONSTRAINT FK_68D8C3F0166D1F9C');
        $this->addSql('DROP INDEX idx_content_block_post');
        $this->addSql('DROP INDEX idx_content_block_project');

        // Step 5: Drop new FK columns
        $this->addSql('ALTER TABLE content_block DROP post_id');
        $this->addSql('ALTER TABLE content_block DROP project_id');

        // Step 6: Recreate old indexes
        $this->addSql('CREATE INDEX idx_content_block_owner ON content_block (owner_type, owner_id)');
        $this->addSql('CREATE INDEX idx_content_block_owner_position ON content_block (owner_type, owner_id, "position")');
    }
}
