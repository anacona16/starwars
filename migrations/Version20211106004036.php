<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211106004036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE specie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE specie (id INT NOT NULL, name VARCHAR(255) NOT NULL, classification VARCHAR(255) DEFAULT NULL, designation VARCHAR(255) DEFAULT NULL, skin_colors VARCHAR(255) DEFAULT NULL, hair_colors VARCHAR(255) DEFAULT NULL, eye_colors VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE specie_character (specie_id INT NOT NULL, character_id INT NOT NULL, PRIMARY KEY(specie_id, character_id))');
        $this->addSql('CREATE INDEX IDX_E1AD47AD5436AB7 ON specie_character (specie_id)');
        $this->addSql('CREATE INDEX IDX_E1AD47A1136BE75 ON specie_character (character_id)');
        $this->addSql('ALTER TABLE specie_character ADD CONSTRAINT FK_E1AD47AD5436AB7 FOREIGN KEY (specie_id) REFERENCES specie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE specie_character ADD CONSTRAINT FK_E1AD47A1136BE75 FOREIGN KEY (character_id) REFERENCES character (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE specie_character DROP CONSTRAINT FK_E1AD47AD5436AB7');
        $this->addSql('DROP SEQUENCE specie_id_seq CASCADE');
        $this->addSql('DROP TABLE specie');
        $this->addSql('DROP TABLE specie_character');
    }
}
