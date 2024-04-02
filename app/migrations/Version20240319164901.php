<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240319164901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE anthill_hole_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, tile_id INT NOT NULL, player_id INT NOT NULL, UNIQUE INDEX UNIQ_396A22B1D3F165E7 (help_id), INDEX IDX_396A22B1638AF48B (tile_id), INDEX IDX_396A22B199E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE anthill_worker_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, player_id INT NOT NULL, personal_board_myr_id INT NOT NULL, work_floor INT NOT NULL, UNIQUE INDEX UNIQ_13489531D3F165E7 (help_id), INDEX IDX_1348953199E6F5DF (player_id), INDEX IDX_13489531E3F7C579 (personal_board_myr_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_goal_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, goal_id INT NOT NULL, UNIQUE INDEX UNIQ_654B667AD3F165E7 (help_id), INDEX IDX_654B667A667D1AFE (goal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_goal_myr_player_myr (game_goal_myr_id INT NOT NULL, player_myr_id INT NOT NULL, INDEX IDX_E56AAFD0ED8E7CEA (game_goal_myr_id), INDEX IDX_E56AAFD0365CDD72 (player_myr_id), PRIMARY KEY(game_goal_myr_id, player_myr_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_myr (id INT AUTO_INCREMENT NOT NULL, first_player_id INT NOT NULL, launched TINYINT(1) NOT NULL, game_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E4E2400B65EB6591 (first_player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE garden_tile_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, type_id INT NOT NULL, resource_id INT NOT NULL, player_id INT NOT NULL, harvested TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_26C30D8FD3F165E7 (help_id), UNIQUE INDEX UNIQ_26C30D8FC54C8C93 (type_id), INDEX IDX_26C30D8F89329D25 (resource_id), INDEX IDX_26C30D8F99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE garden_tile_myr_tile_myr (garden_tile_myr_id INT NOT NULL, tile_myr_id INT NOT NULL, INDEX IDX_CE4C17133BEB74EC (garden_tile_myr_id), INDEX IDX_CE4C1713E7D11019 (tile_myr_id), PRIMARY KEY(garden_tile_myr_id, tile_myr_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE garden_worker_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, player_id INT NOT NULL, tile_id INT NOT NULL, main_board_myr_id INT NOT NULL, shifts_count INT NOT NULL, UNIQUE INDEX UNIQ_B7D4B450D3F165E7 (help_id), INDEX IDX_B7D4B45099E6F5DF (player_id), INDEX IDX_B7D4B450638AF48B (tile_id), INDEX IDX_B7D4B4501CFABAB2 (main_board_myr_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE goal_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, difficulty INT NOT NULL, UNIQUE INDEX UNIQ_CE0F995CD3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE main_board_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, game_id INT NOT NULL, year_num INT NOT NULL, UNIQUE INDEX UNIQ_29EF0707D3F165E7 (help_id), UNIQUE INDEX UNIQ_29EF0707E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nurse_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, player_id INT NOT NULL, personal_board_myr_id INT NOT NULL, position INT NOT NULL, available TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_A21C0214D3F165E7 (help_id), INDEX IDX_A21C021499E6F5DF (player_id), INDEX IDX_A21C0214E3F7C579 (personal_board_myr_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personal_board_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, player_id INT NOT NULL, anthill_level INT NOT NULL, larva_count INT NOT NULL, warriors_count INT NOT NULL, bonus INT NOT NULL, hunted_prey_count INT NOT NULL, UNIQUE INDEX UNIQ_6F5664FD3F165E7 (help_id), UNIQUE INDEX UNIQ_6F5664F99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_myr (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, turn_of_player TINYINT(1) DEFAULT NULL, score INT NOT NULL, goal_level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_resource_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, resource_id INT NOT NULL, personal_board_id INT NOT NULL, quantity INT NOT NULL, UNIQUE INDEX UNIQ_5FDC8DA5D3F165E7 (help_id), INDEX IDX_5FDC8DA589329D25 (resource_id), INDEX IDX_5FDC8DA54BD389CC (personal_board_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prey_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, tile_id INT NOT NULL, game_id INT NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8DE90684D3F165E7 (help_id), INDEX IDX_8DE90684638AF48B (tile_id), INDEX IDX_8DE90684E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_DFE1DD87D3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, dice_result INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_65F6191ED3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, type_id INT NOT NULL, x_min_coord INT NOT NULL, x_max_coord INT NOT NULL, y_coord INT NOT NULL, UNIQUE INDEX UNIQ_EE65EB01D3F165E7 (help_id), INDEX IDX_EE65EB01C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_type_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, orientation INT NOT NULL, type INT NOT NULL, UNIQUE INDEX UNIQ_98A42923D3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE anthill_hole_myr ADD CONSTRAINT FK_396A22B1D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE anthill_hole_myr ADD CONSTRAINT FK_396A22B1638AF48B FOREIGN KEY (tile_id) REFERENCES tile_myr (id)');
        $this->addSql('ALTER TABLE anthill_hole_myr ADD CONSTRAINT FK_396A22B199E6F5DF FOREIGN KEY (player_id) REFERENCES player_myr (id)');
        $this->addSql('ALTER TABLE anthill_worker_myr ADD CONSTRAINT FK_13489531D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE anthill_worker_myr ADD CONSTRAINT FK_1348953199E6F5DF FOREIGN KEY (player_id) REFERENCES player_myr (id)');
        $this->addSql('ALTER TABLE anthill_worker_myr ADD CONSTRAINT FK_13489531E3F7C579 FOREIGN KEY (personal_board_myr_id) REFERENCES personal_board_myr (id)');
        $this->addSql('ALTER TABLE game_goal_myr ADD CONSTRAINT FK_654B667AD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE game_goal_myr ADD CONSTRAINT FK_654B667A667D1AFE FOREIGN KEY (goal_id) REFERENCES goal_myr (id)');
        $this->addSql('ALTER TABLE game_goal_myr_player_myr ADD CONSTRAINT FK_E56AAFD0ED8E7CEA FOREIGN KEY (game_goal_myr_id) REFERENCES game_goal_myr (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_goal_myr_player_myr ADD CONSTRAINT FK_E56AAFD0365CDD72 FOREIGN KEY (player_myr_id) REFERENCES player_myr (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_myr ADD CONSTRAINT FK_E4E2400B65EB6591 FOREIGN KEY (first_player_id) REFERENCES player_myr (id)');
        $this->addSql('ALTER TABLE garden_tile_myr ADD CONSTRAINT FK_26C30D8FD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE garden_tile_myr ADD CONSTRAINT FK_26C30D8FC54C8C93 FOREIGN KEY (type_id) REFERENCES tile_type_myr (id)');
        $this->addSql('ALTER TABLE garden_tile_myr ADD CONSTRAINT FK_26C30D8F89329D25 FOREIGN KEY (resource_id) REFERENCES resource_myr (id)');
        $this->addSql('ALTER TABLE garden_tile_myr ADD CONSTRAINT FK_26C30D8F99E6F5DF FOREIGN KEY (player_id) REFERENCES player_myr (id)');
        $this->addSql('ALTER TABLE garden_tile_myr_tile_myr ADD CONSTRAINT FK_CE4C17133BEB74EC FOREIGN KEY (garden_tile_myr_id) REFERENCES garden_tile_myr (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE garden_tile_myr_tile_myr ADD CONSTRAINT FK_CE4C1713E7D11019 FOREIGN KEY (tile_myr_id) REFERENCES tile_myr (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE garden_worker_myr ADD CONSTRAINT FK_B7D4B450D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE garden_worker_myr ADD CONSTRAINT FK_B7D4B45099E6F5DF FOREIGN KEY (player_id) REFERENCES player_myr (id)');
        $this->addSql('ALTER TABLE garden_worker_myr ADD CONSTRAINT FK_B7D4B450638AF48B FOREIGN KEY (tile_id) REFERENCES tile_myr (id)');
        $this->addSql('ALTER TABLE garden_worker_myr ADD CONSTRAINT FK_B7D4B4501CFABAB2 FOREIGN KEY (main_board_myr_id) REFERENCES main_board_myr (id)');
        $this->addSql('ALTER TABLE goal_myr ADD CONSTRAINT FK_CE0F995CD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE main_board_myr ADD CONSTRAINT FK_29EF0707D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        //$this->addSql('ALTER TABLE main_board_myr ADD CONSTRAINT FK_29EF070775266732 FOREIGN KEY (actual_season_id) REFERENCES season_myr (id)');
        $this->addSql('ALTER TABLE main_board_myr ADD CONSTRAINT FK_29EF0707E48FD905 FOREIGN KEY (game_id) REFERENCES game_myr (id)');
        $this->addSql('ALTER TABLE nurse_myr ADD CONSTRAINT FK_A21C0214D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE nurse_myr ADD CONSTRAINT FK_A21C021499E6F5DF FOREIGN KEY (player_id) REFERENCES player_myr (id)');
        $this->addSql('ALTER TABLE nurse_myr ADD CONSTRAINT FK_A21C0214E3F7C579 FOREIGN KEY (personal_board_myr_id) REFERENCES personal_board_myr (id)');
        $this->addSql('ALTER TABLE personal_board_myr ADD CONSTRAINT FK_6F5664FD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE personal_board_myr ADD CONSTRAINT FK_6F5664F99E6F5DF FOREIGN KEY (player_id) REFERENCES player_myr (id)');
        $this->addSql('ALTER TABLE player_resource_myr ADD CONSTRAINT FK_5FDC8DA5D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE player_resource_myr ADD CONSTRAINT FK_5FDC8DA589329D25 FOREIGN KEY (resource_id) REFERENCES resource_myr (id)');
        $this->addSql('ALTER TABLE player_resource_myr ADD CONSTRAINT FK_5FDC8DA54BD389CC FOREIGN KEY (personal_board_id) REFERENCES personal_board_myr (id)');
        $this->addSql('ALTER TABLE prey_myr ADD CONSTRAINT FK_8DE90684D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE prey_myr ADD CONSTRAINT FK_8DE90684638AF48B FOREIGN KEY (tile_id) REFERENCES tile_myr (id)');
        $this->addSql('ALTER TABLE prey_myr ADD CONSTRAINT FK_8DE90684E48FD905 FOREIGN KEY (game_id) REFERENCES game_myr (id)');
        $this->addSql('ALTER TABLE resource_myr ADD CONSTRAINT FK_DFE1DD87D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE season_myr ADD CONSTRAINT FK_65F6191ED3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_myr ADD CONSTRAINT FK_EE65EB01D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_myr ADD CONSTRAINT FK_EE65EB01C54C8C93 FOREIGN KEY (type_id) REFERENCES tile_type_myr (id)');
        $this->addSql('ALTER TABLE tile_type_myr ADD CONSTRAINT FK_98A42923D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anthill_hole_myr DROP FOREIGN KEY FK_396A22B1D3F165E7');
        $this->addSql('ALTER TABLE anthill_hole_myr DROP FOREIGN KEY FK_396A22B1638AF48B');
        $this->addSql('ALTER TABLE anthill_hole_myr DROP FOREIGN KEY FK_396A22B199E6F5DF');
        $this->addSql('ALTER TABLE anthill_worker_myr DROP FOREIGN KEY FK_13489531D3F165E7');
        $this->addSql('ALTER TABLE anthill_worker_myr DROP FOREIGN KEY FK_1348953199E6F5DF');
        $this->addSql('ALTER TABLE anthill_worker_myr DROP FOREIGN KEY FK_13489531E3F7C579');
        $this->addSql('ALTER TABLE game_goal_myr DROP FOREIGN KEY FK_654B667AD3F165E7');
        $this->addSql('ALTER TABLE game_goal_myr DROP FOREIGN KEY FK_654B667A667D1AFE');
        $this->addSql('ALTER TABLE game_goal_myr_player_myr DROP FOREIGN KEY FK_E56AAFD0ED8E7CEA');
        $this->addSql('ALTER TABLE game_goal_myr_player_myr DROP FOREIGN KEY FK_E56AAFD0365CDD72');
        $this->addSql('ALTER TABLE game_myr DROP FOREIGN KEY FK_E4E2400B65EB6591');
        $this->addSql('ALTER TABLE garden_tile_myr DROP FOREIGN KEY FK_26C30D8FD3F165E7');
        $this->addSql('ALTER TABLE garden_tile_myr DROP FOREIGN KEY FK_26C30D8FC54C8C93');
        $this->addSql('ALTER TABLE garden_tile_myr DROP FOREIGN KEY FK_26C30D8F89329D25');
        $this->addSql('ALTER TABLE garden_tile_myr DROP FOREIGN KEY FK_26C30D8F99E6F5DF');
        $this->addSql('ALTER TABLE garden_tile_myr_tile_myr DROP FOREIGN KEY FK_CE4C17133BEB74EC');
        $this->addSql('ALTER TABLE garden_tile_myr_tile_myr DROP FOREIGN KEY FK_CE4C1713E7D11019');
        $this->addSql('ALTER TABLE garden_worker_myr DROP FOREIGN KEY FK_B7D4B450D3F165E7');
        $this->addSql('ALTER TABLE garden_worker_myr DROP FOREIGN KEY FK_B7D4B45099E6F5DF');
        $this->addSql('ALTER TABLE garden_worker_myr DROP FOREIGN KEY FK_B7D4B450638AF48B');
        $this->addSql('ALTER TABLE garden_worker_myr DROP FOREIGN KEY FK_B7D4B4501CFABAB2');
        $this->addSql('ALTER TABLE goal_myr DROP FOREIGN KEY FK_CE0F995CD3F165E7');
        $this->addSql('ALTER TABLE main_board_myr DROP FOREIGN KEY FK_29EF0707D3F165E7');
        $this->addSql('ALTER TABLE main_board_myr DROP FOREIGN KEY FK_29EF070775266732');
        $this->addSql('ALTER TABLE main_board_myr DROP FOREIGN KEY FK_29EF0707E48FD905');
        $this->addSql('ALTER TABLE nurse_myr DROP FOREIGN KEY FK_A21C0214D3F165E7');
        $this->addSql('ALTER TABLE nurse_myr DROP FOREIGN KEY FK_A21C021499E6F5DF');
        $this->addSql('ALTER TABLE nurse_myr DROP FOREIGN KEY FK_A21C0214E3F7C579');
        $this->addSql('ALTER TABLE personal_board_myr DROP FOREIGN KEY FK_6F5664FD3F165E7');
        $this->addSql('ALTER TABLE personal_board_myr DROP FOREIGN KEY FK_6F5664F99E6F5DF');
        $this->addSql('ALTER TABLE player_resource_myr DROP FOREIGN KEY FK_5FDC8DA5D3F165E7');
        $this->addSql('ALTER TABLE player_resource_myr DROP FOREIGN KEY FK_5FDC8DA589329D25');
        $this->addSql('ALTER TABLE player_resource_myr DROP FOREIGN KEY FK_5FDC8DA54BD389CC');
        $this->addSql('ALTER TABLE prey_myr DROP FOREIGN KEY FK_8DE90684D3F165E7');
        $this->addSql('ALTER TABLE prey_myr DROP FOREIGN KEY FK_8DE90684638AF48B');
        $this->addSql('ALTER TABLE prey_myr DROP FOREIGN KEY FK_8DE90684E48FD905');
        $this->addSql('ALTER TABLE resource_myr DROP FOREIGN KEY FK_DFE1DD87D3F165E7');
        $this->addSql('ALTER TABLE season_myr DROP FOREIGN KEY FK_65F6191ED3F165E7');
        $this->addSql('ALTER TABLE tile_myr DROP FOREIGN KEY FK_EE65EB01D3F165E7');
        $this->addSql('ALTER TABLE tile_myr DROP FOREIGN KEY FK_EE65EB01C54C8C93');
        $this->addSql('ALTER TABLE tile_type_myr DROP FOREIGN KEY FK_98A42923D3F165E7');
        $this->addSql('DROP TABLE anthill_hole_myr');
        $this->addSql('DROP TABLE anthill_worker_myr');
        $this->addSql('DROP TABLE game_goal_myr');
        $this->addSql('DROP TABLE game_goal_myr_player_myr');
        $this->addSql('DROP TABLE game_myr');
        $this->addSql('DROP TABLE garden_tile_myr');
        $this->addSql('DROP TABLE garden_tile_myr_tile_myr');
        $this->addSql('DROP TABLE garden_worker_myr');
        $this->addSql('DROP TABLE goal_myr');
        $this->addSql('DROP TABLE main_board_myr');
        $this->addSql('DROP TABLE nurse_myr');
        $this->addSql('DROP TABLE personal_board_myr');
        $this->addSql('DROP TABLE player_myr');
        $this->addSql('DROP TABLE player_resource_myr');
        $this->addSql('DROP TABLE prey_myr');
        $this->addSql('DROP TABLE resource_myr');
        $this->addSql('DROP TABLE season_myr');
        $this->addSql('DROP TABLE tile_myr');
        $this->addSql('DROP TABLE tile_type_myr');
    }
}
