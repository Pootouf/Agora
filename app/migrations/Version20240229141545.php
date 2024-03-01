<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229141545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE board_tile_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, tile_id INT NOT NULL, main_board_glm_id INT NOT NULL, position INT NOT NULL, UNIQUE INDEX UNIQ_C9B8868FD3F165E7 (help_id), INDEX IDX_C9B8868F638AF48B (tile_id), INDEX IDX_C9B8868F1D1BC6EF (main_board_glm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, bonus_id INT DEFAULT NULL, value INT DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1862B938D3F165E7 (help_id), UNIQUE INDEX UNIQ_1862B93869545666 (bonus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE draw_tiles_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, main_board_glm_id INT NOT NULL, level INT NOT NULL, UNIQUE INDEX UNIQ_5E98FAD9D3F165E7 (help_id), INDEX IDX_5E98FAD91D1BC6EF (main_board_glm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE draw_tiles_glm_tile_glm (draw_tiles_glm_id INT NOT NULL, tile_glm_id INT NOT NULL, INDEX IDX_4B236E6AF49ED990 (draw_tiles_glm_id), INDEX IDX_4B236E6AE6306C44 (tile_glm_id), PRIMARY KEY(draw_tiles_glm_id, tile_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_glm (id INT AUTO_INCREMENT NOT NULL, main_board_id INT NOT NULL, launched TINYINT(1) NOT NULL, game_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_53C82E3C3ECE46F0 (main_board_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE main_board_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, warehouse_id INT NOT NULL, UNIQUE INDEX UNIQ_9EC56930D3F165E7 (help_id), UNIQUE INDEX UNIQ_9EC569305080ECDE (warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pawn_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, main_board_glm_id INT NOT NULL, color VARCHAR(255) NOT NULL, position INT NOT NULL, UNIQUE INDEX UNIQ_F02B06E2D3F165E7 (help_id), INDEX IDX_F02B06E21D1BC6EF (main_board_glm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personal_board_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, leader_count INT NOT NULL, money INT NOT NULL, UNIQUE INDEX UNIQ_B1DF0878D3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personal_board_glm_card_glm (personal_board_glm_id INT NOT NULL, card_glm_id INT NOT NULL, INDEX IDX_2221355CE216B924 (personal_board_glm_id), INDEX IDX_2221355CDA826485 (card_glm_id), PRIMARY KEY(personal_board_glm_id, card_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_glm (id INT AUTO_INCREMENT NOT NULL, personal_board_id INT NOT NULL, pawn_id INT NOT NULL, game_glm_id INT NOT NULL, username VARCHAR(255) NOT NULL, turn_of_player TINYINT(1) DEFAULT NULL, points INT NOT NULL, UNIQUE INDEX UNIQ_35B37CDA4BD389CC (personal_board_id), UNIQUE INDEX UNIQ_35B37CDABBA77367 (pawn_id), INDEX IDX_35B37CDAD5889A39 (game_glm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_tile_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, personal_board_id INT NOT NULL, tile_id INT NOT NULL, UNIQUE INDEX UNIQ_5F98C4D6D3F165E7 (help_id), INDEX IDX_5F98C4D64BD389CC (personal_board_id), INDEX IDX_5F98C4D6638AF48B (tile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_tile_glm_resource_glm (player_tile_glm_id INT NOT NULL, resource_glm_id INT NOT NULL, INDEX IDX_43FC3219BE0785D1 (player_tile_glm_id), INDEX IDX_43FC32196041DD0B (resource_glm_id), PRIMARY KEY(player_tile_glm_id, resource_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_tile_glm_player_tile_glm (player_tile_glm_source INT NOT NULL, player_tile_glm_target INT NOT NULL, INDEX IDX_1314AE1796CB8108 (player_tile_glm_source), INDEX IDX_1314AE178F2ED187 (player_tile_glm_target), PRIMARY KEY(player_tile_glm_source, player_tile_glm_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_68CBB3B0D3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_bonus_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, bonus INT NOT NULL, UNIQUE INDEX UNIQ_8E2D82E6D3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_bonus_glm_resource_glm (tile_bonus_glm_id INT NOT NULL, resource_glm_id INT NOT NULL, INDEX IDX_5D4FB908744CF10 (tile_bonus_glm_id), INDEX IDX_5D4FB906041DD0B (resource_glm_id), PRIMARY KEY(tile_bonus_glm_id, resource_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_cost_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, tile_glm_id INT NOT NULL, tile_bonus_glm_id INT DEFAULT NULL, price INT NOT NULL, UNIQUE INDEX UNIQ_86EA1A4ED3F165E7 (help_id), INDEX IDX_86EA1A4EE6306C44 (tile_glm_id), INDEX IDX_86EA1A4E8744CF10 (tile_bonus_glm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_cost_glm_resource_glm (tile_cost_glm_id INT NOT NULL, resource_glm_id INT NOT NULL, INDEX IDX_BD55CBFAECB794C1 (tile_cost_glm_id), INDEX IDX_BD55CBFA6041DD0B (resource_glm_id), PRIMARY KEY(tile_cost_glm_id, resource_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, buy_bonus_id INT DEFAULT NULL, activation_bonus_id INT NOT NULL, card_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, containing_river TINYINT(1) NOT NULL, containing_road TINYINT(1) NOT NULL, level INT NOT NULL, UNIQUE INDEX UNIQ_594F8536D3F165E7 (help_id), UNIQUE INDEX UNIQ_594F85369B0863B3 (buy_bonus_id), UNIQUE INDEX UNIQ_594F8536CFF0DDC3 (activation_bonus_id), UNIQUE INDEX UNIQ_594F85364ACC9A20 (card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_427D2A55D3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse_glm_resource_glm (warehouse_glm_id INT NOT NULL, resource_glm_id INT NOT NULL, INDEX IDX_7BCAAE8B4DE9E880 (warehouse_glm_id), INDEX IDX_7BCAAE8B6041DD0B (resource_glm_id), PRIMARY KEY(warehouse_glm_id, resource_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE board_tile_glm ADD CONSTRAINT FK_C9B8868FD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE board_tile_glm ADD CONSTRAINT FK_C9B8868F638AF48B FOREIGN KEY (tile_id) REFERENCES tile_glm (id)');
        $this->addSql('ALTER TABLE board_tile_glm ADD CONSTRAINT FK_C9B8868F1D1BC6EF FOREIGN KEY (main_board_glm_id) REFERENCES main_board_glm (id)');
        $this->addSql('ALTER TABLE card_glm ADD CONSTRAINT FK_1862B938D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE card_glm ADD CONSTRAINT FK_1862B93869545666 FOREIGN KEY (bonus_id) REFERENCES tile_bonus_glm (id)');
        $this->addSql('ALTER TABLE draw_tiles_glm ADD CONSTRAINT FK_5E98FAD9D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE draw_tiles_glm ADD CONSTRAINT FK_5E98FAD91D1BC6EF FOREIGN KEY (main_board_glm_id) REFERENCES main_board_glm (id)');
        $this->addSql('ALTER TABLE draw_tiles_glm_tile_glm ADD CONSTRAINT FK_4B236E6AF49ED990 FOREIGN KEY (draw_tiles_glm_id) REFERENCES draw_tiles_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE draw_tiles_glm_tile_glm ADD CONSTRAINT FK_4B236E6AE6306C44 FOREIGN KEY (tile_glm_id) REFERENCES tile_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_glm ADD CONSTRAINT FK_53C82E3C3ECE46F0 FOREIGN KEY (main_board_id) REFERENCES main_board_glm (id)');
        $this->addSql('ALTER TABLE main_board_glm ADD CONSTRAINT FK_9EC56930D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE main_board_glm ADD CONSTRAINT FK_9EC569305080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse_glm (id)');
        $this->addSql('ALTER TABLE pawn_glm ADD CONSTRAINT FK_F02B06E2D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE pawn_glm ADD CONSTRAINT FK_F02B06E21D1BC6EF FOREIGN KEY (main_board_glm_id) REFERENCES main_board_glm (id)');
        $this->addSql('ALTER TABLE personal_board_glm ADD CONSTRAINT FK_B1DF0878D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE personal_board_glm_card_glm ADD CONSTRAINT FK_2221355CE216B924 FOREIGN KEY (personal_board_glm_id) REFERENCES personal_board_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personal_board_glm_card_glm ADD CONSTRAINT FK_2221355CDA826485 FOREIGN KEY (card_glm_id) REFERENCES card_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_glm ADD CONSTRAINT FK_35B37CDA4BD389CC FOREIGN KEY (personal_board_id) REFERENCES personal_board_glm (id)');
        $this->addSql('ALTER TABLE player_glm ADD CONSTRAINT FK_35B37CDABBA77367 FOREIGN KEY (pawn_id) REFERENCES pawn_glm (id)');
        $this->addSql('ALTER TABLE player_glm ADD CONSTRAINT FK_35B37CDAD5889A39 FOREIGN KEY (game_glm_id) REFERENCES game_glm (id)');
        $this->addSql('ALTER TABLE player_tile_glm ADD CONSTRAINT FK_5F98C4D6D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE player_tile_glm ADD CONSTRAINT FK_5F98C4D64BD389CC FOREIGN KEY (personal_board_id) REFERENCES personal_board_glm (id)');
        $this->addSql('ALTER TABLE player_tile_glm ADD CONSTRAINT FK_5F98C4D6638AF48B FOREIGN KEY (tile_id) REFERENCES tile_glm (id)');
        $this->addSql('ALTER TABLE player_tile_glm_resource_glm ADD CONSTRAINT FK_43FC3219BE0785D1 FOREIGN KEY (player_tile_glm_id) REFERENCES player_tile_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_tile_glm_resource_glm ADD CONSTRAINT FK_43FC32196041DD0B FOREIGN KEY (resource_glm_id) REFERENCES resource_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_tile_glm_player_tile_glm ADD CONSTRAINT FK_1314AE1796CB8108 FOREIGN KEY (player_tile_glm_source) REFERENCES player_tile_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_tile_glm_player_tile_glm ADD CONSTRAINT FK_1314AE178F2ED187 FOREIGN KEY (player_tile_glm_target) REFERENCES player_tile_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resource_glm ADD CONSTRAINT FK_68CBB3B0D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_bonus_glm ADD CONSTRAINT FK_8E2D82E6D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_bonus_glm_resource_glm ADD CONSTRAINT FK_5D4FB908744CF10 FOREIGN KEY (tile_bonus_glm_id) REFERENCES tile_bonus_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_bonus_glm_resource_glm ADD CONSTRAINT FK_5D4FB906041DD0B FOREIGN KEY (resource_glm_id) REFERENCES resource_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_cost_glm ADD CONSTRAINT FK_86EA1A4ED3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_cost_glm ADD CONSTRAINT FK_86EA1A4EE6306C44 FOREIGN KEY (tile_glm_id) REFERENCES tile_glm (id)');
        $this->addSql('ALTER TABLE tile_cost_glm ADD CONSTRAINT FK_86EA1A4E8744CF10 FOREIGN KEY (tile_bonus_glm_id) REFERENCES tile_glm (id)');
        $this->addSql('ALTER TABLE tile_cost_glm_resource_glm ADD CONSTRAINT FK_BD55CBFAECB794C1 FOREIGN KEY (tile_cost_glm_id) REFERENCES tile_cost_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_cost_glm_resource_glm ADD CONSTRAINT FK_BD55CBFA6041DD0B FOREIGN KEY (resource_glm_id) REFERENCES resource_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_glm ADD CONSTRAINT FK_594F8536D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_glm ADD CONSTRAINT FK_594F85369B0863B3 FOREIGN KEY (buy_bonus_id) REFERENCES tile_bonus_glm (id)');
        $this->addSql('ALTER TABLE tile_glm ADD CONSTRAINT FK_594F8536CFF0DDC3 FOREIGN KEY (activation_bonus_id) REFERENCES tile_bonus_glm (id)');
        $this->addSql('ALTER TABLE tile_glm ADD CONSTRAINT FK_594F85364ACC9A20 FOREIGN KEY (card_id) REFERENCES card_glm (id)');
        $this->addSql('ALTER TABLE warehouse_glm ADD CONSTRAINT FK_427D2A55D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE warehouse_glm_resource_glm ADD CONSTRAINT FK_7BCAAE8B4DE9E880 FOREIGN KEY (warehouse_glm_id) REFERENCES warehouse_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_glm_resource_glm ADD CONSTRAINT FK_7BCAAE8B6041DD0B FOREIGN KEY (resource_glm_id) REFERENCES resource_glm (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE board_tile_glm DROP FOREIGN KEY FK_C9B8868FD3F165E7');
        $this->addSql('ALTER TABLE board_tile_glm DROP FOREIGN KEY FK_C9B8868F638AF48B');
        $this->addSql('ALTER TABLE board_tile_glm DROP FOREIGN KEY FK_C9B8868F1D1BC6EF');
        $this->addSql('ALTER TABLE card_glm DROP FOREIGN KEY FK_1862B938D3F165E7');
        $this->addSql('ALTER TABLE card_glm DROP FOREIGN KEY FK_1862B93869545666');
        $this->addSql('ALTER TABLE draw_tiles_glm DROP FOREIGN KEY FK_5E98FAD9D3F165E7');
        $this->addSql('ALTER TABLE draw_tiles_glm DROP FOREIGN KEY FK_5E98FAD91D1BC6EF');
        $this->addSql('ALTER TABLE draw_tiles_glm_tile_glm DROP FOREIGN KEY FK_4B236E6AF49ED990');
        $this->addSql('ALTER TABLE draw_tiles_glm_tile_glm DROP FOREIGN KEY FK_4B236E6AE6306C44');
        $this->addSql('ALTER TABLE game_glm DROP FOREIGN KEY FK_53C82E3C3ECE46F0');
        $this->addSql('ALTER TABLE main_board_glm DROP FOREIGN KEY FK_9EC56930D3F165E7');
        $this->addSql('ALTER TABLE main_board_glm DROP FOREIGN KEY FK_9EC569305080ECDE');
        $this->addSql('ALTER TABLE pawn_glm DROP FOREIGN KEY FK_F02B06E2D3F165E7');
        $this->addSql('ALTER TABLE pawn_glm DROP FOREIGN KEY FK_F02B06E21D1BC6EF');
        $this->addSql('ALTER TABLE personal_board_glm DROP FOREIGN KEY FK_B1DF0878D3F165E7');
        $this->addSql('ALTER TABLE personal_board_glm_card_glm DROP FOREIGN KEY FK_2221355CE216B924');
        $this->addSql('ALTER TABLE personal_board_glm_card_glm DROP FOREIGN KEY FK_2221355CDA826485');
        $this->addSql('ALTER TABLE player_glm DROP FOREIGN KEY FK_35B37CDA4BD389CC');
        $this->addSql('ALTER TABLE player_glm DROP FOREIGN KEY FK_35B37CDABBA77367');
        $this->addSql('ALTER TABLE player_glm DROP FOREIGN KEY FK_35B37CDAD5889A39');
        $this->addSql('ALTER TABLE player_tile_glm DROP FOREIGN KEY FK_5F98C4D6D3F165E7');
        $this->addSql('ALTER TABLE player_tile_glm DROP FOREIGN KEY FK_5F98C4D64BD389CC');
        $this->addSql('ALTER TABLE player_tile_glm DROP FOREIGN KEY FK_5F98C4D6638AF48B');
        $this->addSql('ALTER TABLE player_tile_glm_resource_glm DROP FOREIGN KEY FK_43FC3219BE0785D1');
        $this->addSql('ALTER TABLE player_tile_glm_resource_glm DROP FOREIGN KEY FK_43FC32196041DD0B');
        $this->addSql('ALTER TABLE player_tile_glm_player_tile_glm DROP FOREIGN KEY FK_1314AE1796CB8108');
        $this->addSql('ALTER TABLE player_tile_glm_player_tile_glm DROP FOREIGN KEY FK_1314AE178F2ED187');
        $this->addSql('ALTER TABLE resource_glm DROP FOREIGN KEY FK_68CBB3B0D3F165E7');
        $this->addSql('ALTER TABLE tile_bonus_glm DROP FOREIGN KEY FK_8E2D82E6D3F165E7');
        $this->addSql('ALTER TABLE tile_bonus_glm_resource_glm DROP FOREIGN KEY FK_5D4FB908744CF10');
        $this->addSql('ALTER TABLE tile_bonus_glm_resource_glm DROP FOREIGN KEY FK_5D4FB906041DD0B');
        $this->addSql('ALTER TABLE tile_cost_glm DROP FOREIGN KEY FK_86EA1A4ED3F165E7');
        $this->addSql('ALTER TABLE tile_cost_glm DROP FOREIGN KEY FK_86EA1A4EE6306C44');
        $this->addSql('ALTER TABLE tile_cost_glm DROP FOREIGN KEY FK_86EA1A4E8744CF10');
        $this->addSql('ALTER TABLE tile_cost_glm_resource_glm DROP FOREIGN KEY FK_BD55CBFAECB794C1');
        $this->addSql('ALTER TABLE tile_cost_glm_resource_glm DROP FOREIGN KEY FK_BD55CBFA6041DD0B');
        $this->addSql('ALTER TABLE tile_glm DROP FOREIGN KEY FK_594F8536D3F165E7');
        $this->addSql('ALTER TABLE tile_glm DROP FOREIGN KEY FK_594F85369B0863B3');
        $this->addSql('ALTER TABLE tile_glm DROP FOREIGN KEY FK_594F8536CFF0DDC3');
        $this->addSql('ALTER TABLE tile_glm DROP FOREIGN KEY FK_594F85364ACC9A20');
        $this->addSql('ALTER TABLE warehouse_glm DROP FOREIGN KEY FK_427D2A55D3F165E7');
        $this->addSql('ALTER TABLE warehouse_glm_resource_glm DROP FOREIGN KEY FK_7BCAAE8B4DE9E880');
        $this->addSql('ALTER TABLE warehouse_glm_resource_glm DROP FOREIGN KEY FK_7BCAAE8B6041DD0B');
        $this->addSql('DROP TABLE board_tile_glm');
        $this->addSql('DROP TABLE card_glm');
        $this->addSql('DROP TABLE draw_tiles_glm');
        $this->addSql('DROP TABLE draw_tiles_glm_tile_glm');
        $this->addSql('DROP TABLE game_glm');
        $this->addSql('DROP TABLE main_board_glm');
        $this->addSql('DROP TABLE pawn_glm');
        $this->addSql('DROP TABLE personal_board_glm');
        $this->addSql('DROP TABLE personal_board_glm_card_glm');
        $this->addSql('DROP TABLE player_glm');
        $this->addSql('DROP TABLE player_tile_glm');
        $this->addSql('DROP TABLE player_tile_glm_resource_glm');
        $this->addSql('DROP TABLE player_tile_glm_player_tile_glm');
        $this->addSql('DROP TABLE resource_glm');
        $this->addSql('DROP TABLE tile_bonus_glm');
        $this->addSql('DROP TABLE tile_bonus_glm_resource_glm');
        $this->addSql('DROP TABLE tile_cost_glm');
        $this->addSql('DROP TABLE tile_cost_glm_resource_glm');
        $this->addSql('DROP TABLE tile_glm');
        $this->addSql('DROP TABLE warehouse_glm');
        $this->addSql('DROP TABLE warehouse_glm_resource_glm');
    }
}
