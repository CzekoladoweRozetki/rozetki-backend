<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250322112302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE tsvector');
        $this->addSql('CREATE INDEX catalog_product_attribute_idx ON catalog_product (data)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX catalog_product_attribute_idx');
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE TEXT');
    }
}
