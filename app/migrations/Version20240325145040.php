<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240325145040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pheromon_tile_myr ADD main_board_id INT NOT NULL');
        $this->addSql('ALTER TABLE pheromon_tile_myr ADD CONSTRAINT FK_28612FF93ECE46F0 FOREIGN KEY (main_board_id) REFERENCES main_board_myr (id)');
        $this->addSql('CREATE INDEX IDX_28612FF93ECE46F0 ON pheromon_tile_myr (main_board_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pheromon_tile_myr DROP FOREIGN KEY FK_28612FF93ECE46F0');
        $this->addSql('DROP INDEX IDX_28612FF93ECE46F0 ON pheromon_tile_myr');
        $this->addSql('ALTER TABLE pheromon_tile_myr DROP main_board_id');
    }
}
