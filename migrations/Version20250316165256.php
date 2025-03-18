<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250316165256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE attribute_value ADD value VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE attribute_value ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE tsvector');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute_value DROP value');
        $this->addSql('ALTER TABLE attribute_value DROP slug');
        $this->addSql('ALTER TABLE attribute DROP slug');
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE TEXT');
    }
}
