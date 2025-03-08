<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308185757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute_value ADD parent_value_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE attribute_value ADD CONSTRAINT FK_FE4FBB8266ED1773 FOREIGN KEY (parent_value_id) REFERENCES attribute_value (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FE4FBB8266ED1773 ON attribute_value (parent_value_id)');
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE tsvector');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute_value DROP CONSTRAINT FK_FE4FBB8266ED1773');
        $this->addSql('DROP INDEX IDX_FE4FBB8266ED1773');
        $this->addSql('ALTER TABLE attribute_value DROP parent_value_id');
        $this->addSql('ALTER TABLE catalog_product ALTER search_vector TYPE TEXT');
    }
}
