<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306181113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute DROP CONSTRAINT FK_FA7AEFFB727ACA70');
        $this->addSql('ALTER TABLE attribute ADD CONSTRAINT FK_FA7AEFFB727ACA70 FOREIGN KEY (parent_id) REFERENCES attribute (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE tsvector');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE TEXT');
        $this->addSql('ALTER TABLE attribute DROP CONSTRAINT fk_fa7aeffb727aca70');
        $this->addSql('ALTER TABLE attribute ADD CONSTRAINT fk_fa7aeffb727aca70 FOREIGN KEY (parent_id) REFERENCES attribute (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
