<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218113739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_reset_token DROP CONSTRAINT password_reset_token_pkey');
        $this->addSql('ALTER TABLE password_reset_token RENAME COLUMN token TO id');
        $this->addSql('ALTER TABLE password_reset_token ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE password_reset_token ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX password_reset_token_pkey');
        $this->addSql('ALTER TABLE password_reset_token RENAME COLUMN id TO token');
        $this->addSql('ALTER TABLE password_reset_token ALTER token TYPE UUID');
        $this->addSql('ALTER TABLE password_reset_token ADD PRIMARY KEY (token)');
    }
}
