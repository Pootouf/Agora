<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213214023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card_cost_spl (id INT AUTO_INCREMENT NOT NULL, color VARCHAR(255) NOT NULL, price INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE development_cards_spl (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, value INT, prestige_points INT NOT NULL, color VARCHAR(255) NOT NULL, level INT NOT NULL, UNIQUE INDEX UNIQ_EBA2C637D3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE development_cards_spl_card_cost_spl (development_cards_spl_id INT NOT NULL, card_cost_spl_id INT NOT NULL, INDEX IDX_9C1FE5F9E21D84C2 (development_cards_spl_id), INDEX IDX_9C1FE5F9CDB473AF (card_cost_spl_id), PRIMARY KEY(development_cards_spl_id, card_cost_spl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE draw_cards_spl (id INT AUTO_INCREMENT NOT NULL, main_board_spl_id INT NOT NULL, level INT NOT NULL, INDEX IDX_333E9C8598F09B84 (main_board_spl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE draw_cards_spl_development_cards_spl (draw_cards_spl_id INT NOT NULL, development_cards_spl_id INT NOT NULL, INDEX IDX_82C68CE99D2AC4D5 (draw_cards_spl_id), INDEX IDX_82C68CE9E21D84C2 (development_cards_spl_id), PRIMARY KEY(draw_cards_spl_id, development_cards_spl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_spl (id INT AUTO_INCREMENT NOT NULL, main_board_id INT NOT NULL, launched TINYINT(1) NOT NULL, game_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D997485B3ECE46F0 (main_board_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE main_board_spl (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE main_board_spl_token_spl (main_board_spl_id INT NOT NULL, token_spl_id INT NOT NULL, INDEX IDX_FFD905DB98F09B84 (main_board_spl_id), INDEX IDX_FFD905DB2C2EC598 (token_spl_id), PRIMARY KEY(main_board_spl_id, token_spl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE main_board_spl_noble_tile_spl (main_board_spl_id INT NOT NULL, noble_tile_spl_id INT NOT NULL, INDEX IDX_B39716FF98F09B84 (main_board_spl_id), INDEX IDX_B39716FF557A721D (noble_tile_spl_id), PRIMARY KEY(main_board_spl_id, noble_tile_spl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE noble_tile_spl (id INT AUTO_INCREMENT NOT NULL, prestige_points INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE noble_tile_spl_card_cost_spl (noble_tile_spl_id INT NOT NULL, card_cost_spl_id INT NOT NULL, INDEX IDX_F9DB3689557A721D (noble_tile_spl_id), INDEX IDX_F9DB3689CDB473AF (card_cost_spl_id), PRIMARY KEY(noble_tile_spl_id, card_cost_spl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personal_board_spl (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personal_board_spl_token_spl (personal_board_spl_id INT NOT NULL, token_spl_id INT NOT NULL, INDEX IDX_ED22B3FE67FDE44F (personal_board_spl_id), INDEX IDX_ED22B3FE2C2EC598 (token_spl_id), PRIMARY KEY(personal_board_spl_id, token_spl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personal_board_spl_noble_tile_spl (personal_board_spl_id INT NOT NULL, noble_tile_spl_id INT NOT NULL, INDEX IDX_43CA4B2F67FDE44F (personal_board_spl_id), INDEX IDX_43CA4B2F557A721D (noble_tile_spl_id), PRIMARY KEY(personal_board_spl_id, noble_tile_spl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_card_spl (id INT AUTO_INCREMENT NOT NULL, development_card_id INT NOT NULL, personal_board_spl_id INT NOT NULL, is_reserved TINYINT(1) NOT NULL, INDEX IDX_94EA9EBF128A2002 (development_card_id), INDEX IDX_94EA9EBF67FDE44F (personal_board_spl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_spl (id INT AUTO_INCREMENT NOT NULL, personal_board_id INT NOT NULL, game_spl_id INT NOT NULL, username VARCHAR(255) NOT NULL, turn_of_player TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_BFEC1ABD4BD389CC (personal_board_id), INDEX IDX_BFEC1ABD5063C752 (game_spl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE row_spl (id INT AUTO_INCREMENT NOT NULL, main_board_spl_id INT NOT NULL, level INT NOT NULL, INDEX IDX_FA29025498F09B84 (main_board_spl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE row_spl_development_cards_spl (row_spl_id INT NOT NULL, development_cards_spl_id INT NOT NULL, INDEX IDX_1BFAE78C7D70B948 (row_spl_id), INDEX IDX_1BFAE78CE21D84C2 (development_cards_spl_id), PRIMARY KEY(row_spl_id, development_cards_spl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE token_spl (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1BE5E6C2D3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE development_cards_spl ADD CONSTRAINT FK_EBA2C637D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE development_cards_spl_card_cost_spl ADD CONSTRAINT FK_9C1FE5F9E21D84C2 FOREIGN KEY (development_cards_spl_id) REFERENCES development_cards_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE development_cards_spl_card_cost_spl ADD CONSTRAINT FK_9C1FE5F9CDB473AF FOREIGN KEY (card_cost_spl_id) REFERENCES card_cost_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE draw_cards_spl ADD CONSTRAINT FK_333E9C8598F09B84 FOREIGN KEY (main_board_spl_id) REFERENCES main_board_spl (id)');
        $this->addSql('ALTER TABLE draw_cards_spl_development_cards_spl ADD CONSTRAINT FK_82C68CE99D2AC4D5 FOREIGN KEY (draw_cards_spl_id) REFERENCES draw_cards_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE draw_cards_spl_development_cards_spl ADD CONSTRAINT FK_82C68CE9E21D84C2 FOREIGN KEY (development_cards_spl_id) REFERENCES development_cards_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_spl ADD CONSTRAINT FK_D997485B3ECE46F0 FOREIGN KEY (main_board_id) REFERENCES main_board_spl (id)');
        $this->addSql('ALTER TABLE main_board_spl_token_spl ADD CONSTRAINT FK_FFD905DB98F09B84 FOREIGN KEY (main_board_spl_id) REFERENCES main_board_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE main_board_spl_token_spl ADD CONSTRAINT FK_FFD905DB2C2EC598 FOREIGN KEY (token_spl_id) REFERENCES token_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE main_board_spl_noble_tile_spl ADD CONSTRAINT FK_B39716FF98F09B84 FOREIGN KEY (main_board_spl_id) REFERENCES main_board_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE main_board_spl_noble_tile_spl ADD CONSTRAINT FK_B39716FF557A721D FOREIGN KEY (noble_tile_spl_id) REFERENCES noble_tile_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE noble_tile_spl_card_cost_spl ADD CONSTRAINT FK_F9DB3689557A721D FOREIGN KEY (noble_tile_spl_id) REFERENCES noble_tile_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE noble_tile_spl_card_cost_spl ADD CONSTRAINT FK_F9DB3689CDB473AF FOREIGN KEY (card_cost_spl_id) REFERENCES card_cost_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personal_board_spl_token_spl ADD CONSTRAINT FK_ED22B3FE67FDE44F FOREIGN KEY (personal_board_spl_id) REFERENCES personal_board_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personal_board_spl_token_spl ADD CONSTRAINT FK_ED22B3FE2C2EC598 FOREIGN KEY (token_spl_id) REFERENCES token_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personal_board_spl_noble_tile_spl ADD CONSTRAINT FK_43CA4B2F67FDE44F FOREIGN KEY (personal_board_spl_id) REFERENCES personal_board_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personal_board_spl_noble_tile_spl ADD CONSTRAINT FK_43CA4B2F557A721D FOREIGN KEY (noble_tile_spl_id) REFERENCES noble_tile_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_card_spl ADD CONSTRAINT FK_94EA9EBF128A2002 FOREIGN KEY (development_card_id) REFERENCES development_cards_spl (id)');
        $this->addSql('ALTER TABLE player_card_spl ADD CONSTRAINT FK_94EA9EBF67FDE44F FOREIGN KEY (personal_board_spl_id) REFERENCES personal_board_spl (id)');
        $this->addSql('ALTER TABLE player_spl ADD CONSTRAINT FK_BFEC1ABD4BD389CC FOREIGN KEY (personal_board_id) REFERENCES personal_board_spl (id)');
        $this->addSql('ALTER TABLE player_spl ADD CONSTRAINT FK_BFEC1ABD5063C752 FOREIGN KEY (game_spl_id) REFERENCES game_spl (id)');
        $this->addSql('ALTER TABLE row_spl ADD CONSTRAINT FK_FA29025498F09B84 FOREIGN KEY (main_board_spl_id) REFERENCES main_board_spl (id)');
        $this->addSql('ALTER TABLE row_spl_development_cards_spl ADD CONSTRAINT FK_1BFAE78C7D70B948 FOREIGN KEY (row_spl_id) REFERENCES row_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE row_spl_development_cards_spl ADD CONSTRAINT FK_1BFAE78CE21D84C2 FOREIGN KEY (development_cards_spl_id) REFERENCES development_cards_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE token_spl ADD CONSTRAINT FK_1BE5E6C2D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE development_cards_spl DROP FOREIGN KEY FK_EBA2C637D3F165E7');
        $this->addSql('ALTER TABLE development_cards_spl_card_cost_spl DROP FOREIGN KEY FK_9C1FE5F9E21D84C2');
        $this->addSql('ALTER TABLE development_cards_spl_card_cost_spl DROP FOREIGN KEY FK_9C1FE5F9CDB473AF');
        $this->addSql('ALTER TABLE draw_cards_spl DROP FOREIGN KEY FK_333E9C8598F09B84');
        $this->addSql('ALTER TABLE draw_cards_spl_development_cards_spl DROP FOREIGN KEY FK_82C68CE99D2AC4D5');
        $this->addSql('ALTER TABLE draw_cards_spl_development_cards_spl DROP FOREIGN KEY FK_82C68CE9E21D84C2');
        $this->addSql('ALTER TABLE game_spl DROP FOREIGN KEY FK_D997485B3ECE46F0');
        $this->addSql('ALTER TABLE main_board_spl_token_spl DROP FOREIGN KEY FK_FFD905DB98F09B84');
        $this->addSql('ALTER TABLE main_board_spl_token_spl DROP FOREIGN KEY FK_FFD905DB2C2EC598');
        $this->addSql('ALTER TABLE main_board_spl_noble_tile_spl DROP FOREIGN KEY FK_B39716FF98F09B84');
        $this->addSql('ALTER TABLE main_board_spl_noble_tile_spl DROP FOREIGN KEY FK_B39716FF557A721D');
        $this->addSql('ALTER TABLE noble_tile_spl_card_cost_spl DROP FOREIGN KEY FK_F9DB3689557A721D');
        $this->addSql('ALTER TABLE noble_tile_spl_card_cost_spl DROP FOREIGN KEY FK_F9DB3689CDB473AF');
        $this->addSql('ALTER TABLE personal_board_spl_token_spl DROP FOREIGN KEY FK_ED22B3FE67FDE44F');
        $this->addSql('ALTER TABLE personal_board_spl_token_spl DROP FOREIGN KEY FK_ED22B3FE2C2EC598');
        $this->addSql('ALTER TABLE personal_board_spl_noble_tile_spl DROP FOREIGN KEY FK_43CA4B2F67FDE44F');
        $this->addSql('ALTER TABLE personal_board_spl_noble_tile_spl DROP FOREIGN KEY FK_43CA4B2F557A721D');
        $this->addSql('ALTER TABLE player_card_spl DROP FOREIGN KEY FK_94EA9EBF128A2002');
        $this->addSql('ALTER TABLE player_card_spl DROP FOREIGN KEY FK_94EA9EBF67FDE44F');
        $this->addSql('ALTER TABLE player_spl DROP FOREIGN KEY FK_BFEC1ABD4BD389CC');
        $this->addSql('ALTER TABLE player_spl DROP FOREIGN KEY FK_BFEC1ABD5063C752');
        $this->addSql('ALTER TABLE row_spl DROP FOREIGN KEY FK_FA29025498F09B84');
        $this->addSql('ALTER TABLE row_spl_development_cards_spl DROP FOREIGN KEY FK_1BFAE78C7D70B948');
        $this->addSql('ALTER TABLE row_spl_development_cards_spl DROP FOREIGN KEY FK_1BFAE78CE21D84C2');
        $this->addSql('ALTER TABLE token_spl DROP FOREIGN KEY FK_1BE5E6C2D3F165E7');
        $this->addSql('DROP TABLE card_cost_spl');
        $this->addSql('DROP TABLE development_cards_spl');
        $this->addSql('DROP TABLE development_cards_spl_card_cost_spl');
        $this->addSql('DROP TABLE draw_cards_spl');
        $this->addSql('DROP TABLE draw_cards_spl_development_cards_spl');
        $this->addSql('DROP TABLE game_spl');
        $this->addSql('DROP TABLE main_board_spl');
        $this->addSql('DROP TABLE main_board_spl_token_spl');
        $this->addSql('DROP TABLE main_board_spl_noble_tile_spl');
        $this->addSql('DROP TABLE noble_tile_spl');
        $this->addSql('DROP TABLE noble_tile_spl_card_cost_spl');
        $this->addSql('DROP TABLE personal_board_spl');
        $this->addSql('DROP TABLE personal_board_spl_token_spl');
        $this->addSql('DROP TABLE personal_board_spl_noble_tile_spl');
        $this->addSql('DROP TABLE player_card_spl');
        $this->addSql('DROP TABLE player_spl');
        $this->addSql('DROP TABLE row_spl');
        $this->addSql('DROP TABLE row_spl_development_cards_spl');
        $this->addSql('DROP TABLE token_spl');
    }
}
