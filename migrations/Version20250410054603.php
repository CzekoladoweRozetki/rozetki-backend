<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250410054603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE price (events JSONB NOT NULL, id UUID NOT NULL, product_id VARCHAR(255) NOT NULL, price_list_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CAC822D95688DED7 ON price (price_list_id)');
        $this->addSql('CREATE TABLE price_list (id UUID NOT NULL, name VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D95688DED7 FOREIGN KEY (price_list_id) REFERENCES price_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE tsvector');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price DROP CONSTRAINT FK_CAC822D95688DED7');
        $this->addSql('DROP TABLE price');
        $this->addSql('DROP TABLE price_list');
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE TEXT');
    }
}
