<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251228161221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE inventory DROP category');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A3612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B12D4A3612469DE2 ON inventory (category_id)');
        $this->addSql('ALTER TABLE item ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE item DROP created_by');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EB03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_1F1B251EB03A8386 ON item (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory DROP CONSTRAINT FK_B12D4A3612469DE2');
        $this->addSql('DROP INDEX UNIQ_B12D4A3612469DE2');
        $this->addSql('ALTER TABLE inventory ADD category VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE inventory DROP category_id');
        $this->addSql('ALTER TABLE item DROP CONSTRAINT FK_1F1B251EB03A8386');
        $this->addSql('DROP INDEX IDX_1F1B251EB03A8386');
        $this->addSql('ALTER TABLE item ADD created_by VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE item DROP created_by_id');
    }
}
