<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324154905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE main_board_myr_tile_myr (main_board_myr_id INT NOT NULL, tile_myr_id INT NOT NULL, INDEX IDX_7B4A48B11CFABAB2 (main_board_myr_id), INDEX IDX_7B4A48B1E7D11019 (tile_myr_id), PRIMARY KEY(main_board_myr_id, tile_myr_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE main_board_myr_tile_myr ADD CONSTRAINT FK_7B4A48B11CFABAB2 FOREIGN KEY (main_board_myr_id) REFERENCES main_board_myr (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE main_board_myr_tile_myr ADD CONSTRAINT FK_7B4A48B1E7D11019 FOREIGN KEY (tile_myr_id) REFERENCES tile_myr (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prey_myr ADD main_board_myr_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prey_myr ADD CONSTRAINT FK_8DE906841CFABAB2 FOREIGN KEY (main_board_myr_id) REFERENCES main_board_myr (id)');
        $this->addSql('CREATE INDEX IDX_8DE906841CFABAB2 ON prey_myr (main_board_myr_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE main_board_myr_tile_myr DROP FOREIGN KEY FK_7B4A48B11CFABAB2');
        $this->addSql('ALTER TABLE main_board_myr_tile_myr DROP FOREIGN KEY FK_7B4A48B1E7D11019');
        $this->addSql('DROP TABLE main_board_myr_tile_myr');
        $this->addSql('ALTER TABLE prey_myr DROP FOREIGN KEY FK_8DE906841CFABAB2');
        $this->addSql('DROP INDEX IDX_8DE906841CFABAB2 ON prey_myr');
        $this->addSql('ALTER TABLE prey_myr DROP main_board_myr_id');
    }
}
