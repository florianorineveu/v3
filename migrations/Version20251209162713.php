<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create content block system tables.
 */
final class Version20251209162713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create content_block and content_block_reference tables for modular content system';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE content_block (id SERIAL NOT NULL, type VARCHAR(32) NOT NULL, position SMALLINT DEFAULT 0 NOT NULL, owner_type VARCHAR(32) NOT NULL, owner_id INT NOT NULL, data JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_content_block_owner ON content_block (owner_type, owner_id)');
        $this->addSql('CREATE INDEX idx_content_block_owner_position ON content_block (owner_type, owner_id, position)');
        $this->addSql('COMMENT ON COLUMN content_block.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN content_block.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE content_block_reference (id SERIAL NOT NULL, block_id INT NOT NULL, post_id INT DEFAULT NULL, project_id INT DEFAULT NULL, media_id INT DEFAULT NULL, position SMALLINT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1F9834314B89032C ON content_block_reference (post_id)');
        $this->addSql('CREATE INDEX IDX_1F983431166D1F9C ON content_block_reference (project_id)');
        $this->addSql('CREATE INDEX idx_content_block_reference_block ON content_block_reference (block_id)');
        $this->addSql('ALTER TABLE content_block_reference ADD CONSTRAINT FK_1F983431E9ED820C FOREIGN KEY (block_id) REFERENCES content_block (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_block_reference ADD CONSTRAINT FK_1F9834314B89032C FOREIGN KEY (post_id) REFERENCES blog_post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_block_reference ADD CONSTRAINT FK_1F983431166D1F9C FOREIGN KEY (project_id) REFERENCES portfolio_project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE content_block_reference DROP CONSTRAINT FK_1F983431E9ED820C');
        $this->addSql('ALTER TABLE content_block_reference DROP CONSTRAINT FK_1F9834314B89032C');
        $this->addSql('ALTER TABLE content_block_reference DROP CONSTRAINT FK_1F983431166D1F9C');
        $this->addSql('DROP TABLE content_block');
        $this->addSql('DROP TABLE content_block_reference');
    }
}
