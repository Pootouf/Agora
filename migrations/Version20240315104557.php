<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240315104557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE buying_tile_glm (id INT AUTO_INCREMENT NOT NULL, board_tile_id INT NOT NULL, coord_x INT NOT NULL, coord_y INT NOT NULL, UNIQUE INDEX UNIQ_7B227C872B94DC84 (board_tile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE buying_tile_glm ADD CONSTRAINT FK_7B227C872B94DC84 FOREIGN KEY (board_tile_id) REFERENCES board_tile_glm (id)');
        $this->addSql('ALTER TABLE personal_board_glm DROP FOREIGN KEY FK_B1DF0878C65FC3D');
        $this->addSql('ALTER TABLE personal_board_glm ADD CONSTRAINT FK_B1DF0878C65FC3D FOREIGN KEY (buying_tile_id) REFERENCES buying_tile_glm (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personal_board_glm DROP FOREIGN KEY FK_B1DF0878C65FC3D');
        $this->addSql('ALTER TABLE buying_tile_glm DROP FOREIGN KEY FK_7B227C872B94DC84');
        $this->addSql('DROP TABLE buying_tile_glm');
        $this->addSql('ALTER TABLE personal_board_glm DROP FOREIGN KEY FK_B1DF0878C65FC3D');
        $this->addSql('ALTER TABLE personal_board_glm ADD CONSTRAINT FK_B1DF0878C65FC3D FOREIGN KEY (buying_tile_id) REFERENCES board_tile_glm (id)');
    }
}
