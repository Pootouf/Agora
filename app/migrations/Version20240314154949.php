<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314154949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personal_board_glm DROP FOREIGN KEY FK_B1DF08788BCE8A15');
        $this->addSql('DROP INDEX UNIQ_B1DF08788BCE8A15 ON personal_board_glm');
        $this->addSql('ALTER TABLE personal_board_glm ADD activated_tile_id INT DEFAULT NULL, CHANGE selected_tile_id buying_tile_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personal_board_glm ADD CONSTRAINT FK_B1DF0878C65FC3D FOREIGN KEY (buying_tile_id) REFERENCES board_tile_glm (id)');
        $this->addSql('ALTER TABLE personal_board_glm ADD CONSTRAINT FK_B1DF08782032EC6F FOREIGN KEY (activated_tile_id) REFERENCES player_tile_glm (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1DF0878C65FC3D ON personal_board_glm (buying_tile_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1DF08782032EC6F ON personal_board_glm (activated_tile_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personal_board_glm DROP FOREIGN KEY FK_B1DF0878C65FC3D');
        $this->addSql('ALTER TABLE personal_board_glm DROP FOREIGN KEY FK_B1DF08782032EC6F');
        $this->addSql('DROP INDEX UNIQ_B1DF0878C65FC3D ON personal_board_glm');
        $this->addSql('DROP INDEX UNIQ_B1DF08782032EC6F ON personal_board_glm');
        $this->addSql('ALTER TABLE personal_board_glm ADD selected_tile_id INT DEFAULT NULL, DROP buying_tile_id, DROP activated_tile_id');
        $this->addSql('ALTER TABLE personal_board_glm ADD CONSTRAINT FK_B1DF08788BCE8A15 FOREIGN KEY (selected_tile_id) REFERENCES board_tile_glm (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1DF08788BCE8A15 ON personal_board_glm (selected_tile_id)');
    }
}
