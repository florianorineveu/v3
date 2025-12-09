<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Reorganize entity tables with proper naming convention.
 *
 * Tables renamed:
 * - admin -> user_admin
 * - category -> blog_category
 * - post -> blog_post
 * - project -> portfolio_project
 * - short_url -> tool_short_url
 *
 * Index/constraint naming convention:
 * - Unique: uniq_{table}_{field}
 * - Index: idx_{table}_{fields}
 */
final class Version20251209160940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Reorganize entity tables and indexes with proper naming convention';
    }

    public function up(Schema $schema): void
    {
        // Drop old content_block table (STI remnant, cleaned up in previous session)
        $this->addSql('DROP TABLE IF EXISTS content_block');
        $this->addSql('DROP SEQUENCE IF EXISTS content_block_id_seq CASCADE');

        // Rename tables
        $this->addSql('ALTER TABLE admin RENAME TO user_admin');
        $this->addSql('ALTER TABLE category RENAME TO blog_category');
        $this->addSql('ALTER TABLE post RENAME TO blog_post');
        $this->addSql('ALTER TABLE project RENAME TO portfolio_project');
        $this->addSql('ALTER TABLE short_url RENAME TO tool_short_url');

        // Rename sequences
        $this->addSql('ALTER SEQUENCE admin_id_seq RENAME TO user_admin_id_seq');
        $this->addSql('ALTER SEQUENCE category_id_seq RENAME TO blog_category_id_seq');
        $this->addSql('ALTER SEQUENCE post_id_seq RENAME TO blog_post_id_seq');
        $this->addSql('ALTER SEQUENCE project_id_seq RENAME TO portfolio_project_id_seq');
        $this->addSql('ALTER SEQUENCE short_url_id_seq RENAME TO tool_short_url_id_seq');

        // Rename user_admin indexes
        $this->addSql('ALTER INDEX uniq_identifier_email RENAME TO uniq_user_admin_email');
        $this->addSql('ALTER INDEX idx_admin_active RENAME TO idx_user_admin_active');

        // Rename blog_category indexes
        $this->addSql('ALTER INDEX uniq_bc_slug RENAME TO uniq_blog_category_slug');

        // Rename blog_post indexes
        $this->addSql('ALTER INDEX uniq_bp_slug RENAME TO uniq_blog_post_slug');
        $this->addSql('DROP INDEX idx_bp_enabled_slug');
        $this->addSql('CREATE INDEX idx_blog_post_enabled_slug ON blog_post (enabled, slug)');

        // Rename tool_short_url indexes
        $this->addSql('ALTER INDEX uniq_su_slug RENAME TO uniq_tool_short_url_slug');
        $this->addSql('DROP INDEX idx_su_enabled_slug');
        $this->addSql('CREATE INDEX idx_tool_short_url_enabled_slug ON tool_short_url (enabled, slug)');

        // Rename auto-generated FK indexes (Doctrine generates these based on table names)
        $this->addSql('ALTER INDEX idx_5a8a6c8d12469de2 RENAME TO IDX_BA5AE01D12469DE2');
        $this->addSql('ALTER INDEX idx_83360531b03a8386 RENAME TO IDX_26D3712EB03A8386');
    }

    public function down(Schema $schema): void
    {
        // Rename tables back
        $this->addSql('ALTER TABLE user_admin RENAME TO admin');
        $this->addSql('ALTER TABLE blog_category RENAME TO category');
        $this->addSql('ALTER TABLE blog_post RENAME TO post');
        $this->addSql('ALTER TABLE portfolio_project RENAME TO project');
        $this->addSql('ALTER TABLE tool_short_url RENAME TO short_url');

        // Rename sequences back
        $this->addSql('ALTER SEQUENCE user_admin_id_seq RENAME TO admin_id_seq');
        $this->addSql('ALTER SEQUENCE blog_category_id_seq RENAME TO category_id_seq');
        $this->addSql('ALTER SEQUENCE blog_post_id_seq RENAME TO post_id_seq');
        $this->addSql('ALTER SEQUENCE portfolio_project_id_seq RENAME TO project_id_seq');
        $this->addSql('ALTER SEQUENCE tool_short_url_id_seq RENAME TO short_url_id_seq');

        // Rename admin indexes back
        $this->addSql('ALTER INDEX uniq_user_admin_email RENAME TO uniq_identifier_email');
        $this->addSql('ALTER INDEX idx_user_admin_active RENAME TO idx_admin_active');

        // Rename category indexes back
        $this->addSql('ALTER INDEX uniq_blog_category_slug RENAME TO uniq_bc_slug');

        // Rename post indexes back
        $this->addSql('ALTER INDEX uniq_blog_post_slug RENAME TO uniq_bp_slug');
        $this->addSql('DROP INDEX idx_blog_post_enabled_slug');
        $this->addSql('CREATE INDEX idx_bp_enabled_slug ON post (slug, enabled)');

        // Rename short_url indexes back
        $this->addSql('ALTER INDEX uniq_tool_short_url_slug RENAME TO uniq_su_slug');
        $this->addSql('DROP INDEX idx_tool_short_url_enabled_slug');
        $this->addSql('CREATE INDEX idx_su_enabled_slug ON short_url (slug, enabled)');

        // Recreate content_block table if needed
        $this->addSql('CREATE SEQUENCE content_block_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE content_block (id SERIAL NOT NULL, owner_type VARCHAR(32) NOT NULL, owner_id INT NOT NULL, "position" SMALLINT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(32) NOT NULL, content TEXT DEFAULT NULL, code TEXT DEFAULT NULL, language VARCHAR(32) DEFAULT \'plaintext\', filename VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_content_block_owner ON content_block (owner_type, owner_id)');
        $this->addSql('CREATE INDEX idx_content_block_owner_position ON content_block (owner_type, owner_id, "position")');
    }
}
