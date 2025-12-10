<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209234621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE portfolio_project (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tool_short_url (id SERIAL NOT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, url VARCHAR(255) NOT NULL, slug VARCHAR(180) NOT NULL, enabled BOOLEAN DEFAULT true NOT NULL, used INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_26D3712EB03A8386 ON tool_short_url (created_by_id)');
        $this->addSql('CREATE INDEX idx_tool_short_url_enabled_slug ON tool_short_url (enabled, slug)');
        $this->addSql('CREATE UNIQUE INDEX uniq_tool_short_url_slug ON tool_short_url (slug)');
        $this->addSql('COMMENT ON COLUMN tool_short_url.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tool_short_url.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_admin (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, active BOOLEAN DEFAULT true NOT NULL, failed_login_attempts SMALLINT DEFAULT 0 NOT NULL, last_failed_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, locked_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_login_ip VARCHAR(45) DEFAULT NULL, reset_password_token VARCHAR(100) DEFAULT NULL, reset_password_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_user_admin_email_active_locked_until ON user_admin (email, active, locked_until)');
        $this->addSql('CREATE UNIQUE INDEX uniq_user_admin_email ON user_admin (email)');
        $this->addSql('COMMENT ON COLUMN user_admin.last_failed_login_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_admin.locked_until IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_admin.last_login_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_admin.reset_password_expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_admin.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_admin.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE rememberme_token (series VARCHAR(88) NOT NULL, value VARCHAR(88) NOT NULL, lastUsed TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, class VARCHAR(100) NOT NULL, username VARCHAR(200) NOT NULL, PRIMARY KEY(series))');
        $this->addSql('COMMENT ON COLUMN rememberme_token.lastUsed IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE tool_short_url ADD CONSTRAINT FK_26D3712EB03A8386 FOREIGN KEY (created_by_id) REFERENCES user_admin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tool_short_url DROP CONSTRAINT FK_26D3712EB03A8386');
        $this->addSql('DROP TABLE portfolio_project');
        $this->addSql('DROP TABLE tool_short_url');
        $this->addSql('DROP TABLE user_admin');
        $this->addSql('DROP TABLE rememberme_token');
    }
}
