<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211108190703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_937AB034F47645AE ON "character" (url)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8244BE22F47645AE ON film (url)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A280972F47645AE ON specie (url)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_937AB034F47645AE');
        $this->addSql('DROP INDEX UNIQ_8244BE22F47645AE');
        $this->addSql('DROP INDEX UNIQ_A280972F47645AE');
    }
}
