<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260103162759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory DROP CONSTRAINT fk_b12d4a367e3c61f9');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A367E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE item DROP CONSTRAINT fk_1f1b251e9eea759');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E9EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE item_field DROP CONSTRAINT fk_71525e889eea759');
        $this->addSql('ALTER TABLE item_field ADD CONSTRAINT FK_71525E889EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory DROP CONSTRAINT FK_B12D4A367E3C61F9');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT fk_b12d4a367e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item DROP CONSTRAINT FK_1F1B251E9EEA759');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT fk_1f1b251e9eea759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item_field DROP CONSTRAINT FK_71525E889EEA759');
        $this->addSql('ALTER TABLE item_field ADD CONSTRAINT fk_71525e889eea759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
