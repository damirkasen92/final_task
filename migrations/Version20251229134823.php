<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251229134823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_b12d4a3612469de2');
        $this->addSql('CREATE INDEX IDX_B12D4A3612469DE2 ON inventory (category_id)');
        $this->addSql('ALTER TABLE item ADD integer1 INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD integer2 INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD integer3 INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD string1 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD string2 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD string3 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD text1 TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD text2 TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD text3 TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD bool1 BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD bool2 BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD bool3 BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD link1 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD link2 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD link3 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE item_field ADD slot VARCHAR(40) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_B12D4A3612469DE2');
        $this->addSql('CREATE UNIQUE INDEX uniq_b12d4a3612469de2 ON inventory (category_id)');
        $this->addSql('ALTER TABLE item DROP integer1');
        $this->addSql('ALTER TABLE item DROP integer2');
        $this->addSql('ALTER TABLE item DROP integer3');
        $this->addSql('ALTER TABLE item DROP string1');
        $this->addSql('ALTER TABLE item DROP string2');
        $this->addSql('ALTER TABLE item DROP string3');
        $this->addSql('ALTER TABLE item DROP text1');
        $this->addSql('ALTER TABLE item DROP text2');
        $this->addSql('ALTER TABLE item DROP text3');
        $this->addSql('ALTER TABLE item DROP bool1');
        $this->addSql('ALTER TABLE item DROP bool2');
        $this->addSql('ALTER TABLE item DROP bool3');
        $this->addSql('ALTER TABLE item DROP link1');
        $this->addSql('ALTER TABLE item DROP link2');
        $this->addSql('ALTER TABLE item DROP link3');
        $this->addSql('ALTER TABLE item_field DROP slot');
    }
}
