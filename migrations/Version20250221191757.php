<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221191757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE catalog_product ADD search_vector tsvector DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_DCF8F981C28C2744 ON catalog_product (search_vector)');
        $this->addSql('CREATE INDEX IDX_DCF8F981989D9B62 ON catalog_product (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_DCF8F981C28C2744');
        $this->addSql('DROP INDEX IDX_DCF8F981989D9B62');
        $this->addSql('ALTER TABLE catalog_product DROP search_vector');
    }
}
