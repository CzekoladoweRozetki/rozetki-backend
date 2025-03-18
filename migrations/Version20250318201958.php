<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318201958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_attribute (id UUID NOT NULL, attribute_value_id UUID NOT NULL, product_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_94DA59764584665A ON product_attribute (product_id)');
        $this->addSql('CREATE TABLE product_variant_attribute (id UUID NOT NULL, attribute_value_id UUID NOT NULL, product_variant_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AD0306FBA80EF684 ON product_variant_attribute (product_variant_id)');
        $this->addSql('ALTER TABLE product_attribute ADD CONSTRAINT FK_94DA59764584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_variant_attribute ADD CONSTRAINT FK_AD0306FBA80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE tsvector');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_attribute DROP CONSTRAINT FK_94DA59764584665A');
        $this->addSql('ALTER TABLE product_variant_attribute DROP CONSTRAINT FK_AD0306FBA80EF684');
        $this->addSql('DROP TABLE product_attribute');
        $this->addSql('DROP TABLE product_variant_attribute');
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE TEXT');
    }
}
