<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251122155113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create ShortUrl entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE short_url (id SERIAL NOT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, url VARCHAR(255) NOT NULL, slug VARCHAR(180) NOT NULL, enabled BOOLEAN DEFAULT true NOT NULL, used INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_83360531B03A8386 ON short_url (created_by_id)');
        $this->addSql('CREATE INDEX IDX_ENABLED_SLUG ON short_url (slug, enabled)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_SLUG ON short_url (slug)');
        $this->addSql('COMMENT ON COLUMN short_url.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN short_url.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE short_url ADD CONSTRAINT FK_83360531B03A8386 FOREIGN KEY (created_by_id) REFERENCES admin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE short_url DROP CONSTRAINT FK_83360531B03A8386');
        $this->addSql('DROP TABLE short_url');
    }
}
