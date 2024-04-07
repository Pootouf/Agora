<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331071801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE season_myr ADD main_board_id INT NOT NULL');
        $this->addSql('ALTER TABLE season_myr ADD CONSTRAINT FK_65F6191E3ECE46F0 FOREIGN KEY (main_board_id) REFERENCES main_board_myr (id)');
        $this->addSql('CREATE INDEX IDX_65F6191E3ECE46F0 ON season_myr (main_board_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE season_myr DROP FOREIGN KEY FK_65F6191E3ECE46F0');
        $this->addSql('DROP INDEX IDX_65F6191E3ECE46F0 ON season_myr');
        $this->addSql('ALTER TABLE season_myr DROP main_board_id');
    }
}
