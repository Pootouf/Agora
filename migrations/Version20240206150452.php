<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240206150452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE development_cards_spl (id INT AUTO_INCREMENT NOT NULL, draw_cards_spl_id INT DEFAULT NULL, row_spl_id INT DEFAULT NULL, prestige_points INT NOT NULL, color VARCHAR(255) NOT NULL, cost_tokens_color LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', count_cost_tokens_color LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_EBA2C6379D2AC4D5 (draw_cards_spl_id), INDEX IDX_EBA2C6377D70B948 (row_spl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE draw_cards_spl (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, main_board_spl_id INT NOT NULL, card_level INT NOT NULL, INDEX IDX_333E9C85E48FD905 (game_id), INDEX IDX_333E9C8598F09B84 (main_board_spl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_spl (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE main_board_spl (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, UNIQUE INDEX UNIQ_149A0F57E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE noble_tile_spl (id INT AUTO_INCREMENT NOT NULL, personal_board_spl_id INT DEFAULT NULL, main_board_spl_id INT DEFAULT NULL, prestige_points INT NOT NULL, cost_cards_color LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', count_cost_cards_color LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_DCE27C667FDE44F (personal_board_spl_id), INDEX IDX_DCE27C698F09B84 (main_board_spl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personal_board_spl (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, INDEX IDX_3B806E1FE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_spl (id INT AUTO_INCREMENT NOT NULL, personal_board_id INT NOT NULL, game_spl_id INT NOT NULL, UNIQUE INDEX UNIQ_BFEC1ABD4BD389CC (personal_board_id), INDEX IDX_BFEC1ABD5063C752 (game_spl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE row_spl (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, main_board_spl_id INT NOT NULL, card_level INT NOT NULL, INDEX IDX_FA290254E48FD905 (game_id), INDEX IDX_FA29025498F09B84 (main_board_spl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE token_spl (id INT AUTO_INCREMENT NOT NULL, personal_board_spl_id INT DEFAULT NULL, main_board_spl_id INT DEFAULT NULL, color VARCHAR(255) NOT NULL, INDEX IDX_1BE5E6C267FDE44F (personal_board_spl_id), INDEX IDX_1BE5E6C298F09B84 (main_board_spl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE development_cards_spl ADD CONSTRAINT FK_EBA2C6379D2AC4D5 FOREIGN KEY (draw_cards_spl_id) REFERENCES draw_cards_spl (id)');
        $this->addSql('ALTER TABLE development_cards_spl ADD CONSTRAINT FK_EBA2C6377D70B948 FOREIGN KEY (row_spl_id) REFERENCES row_spl (id)');
        $this->addSql('ALTER TABLE draw_cards_spl ADD CONSTRAINT FK_333E9C85E48FD905 FOREIGN KEY (game_id) REFERENCES game_spl (id)');
        $this->addSql('ALTER TABLE draw_cards_spl ADD CONSTRAINT FK_333E9C8598F09B84 FOREIGN KEY (main_board_spl_id) REFERENCES main_board_spl (id)');
        $this->addSql('ALTER TABLE main_board_spl ADD CONSTRAINT FK_149A0F57E48FD905 FOREIGN KEY (game_id) REFERENCES game_spl (id)');
        $this->addSql('ALTER TABLE noble_tile_spl ADD CONSTRAINT FK_DCE27C667FDE44F FOREIGN KEY (personal_board_spl_id) REFERENCES personal_board_spl (id)');
        $this->addSql('ALTER TABLE noble_tile_spl ADD CONSTRAINT FK_DCE27C698F09B84 FOREIGN KEY (main_board_spl_id) REFERENCES main_board_spl (id)');
        $this->addSql('ALTER TABLE personal_board_spl ADD CONSTRAINT FK_3B806E1FE48FD905 FOREIGN KEY (game_id) REFERENCES game_spl (id)');
        $this->addSql('ALTER TABLE player_spl ADD CONSTRAINT FK_BFEC1ABD4BD389CC FOREIGN KEY (personal_board_id) REFERENCES personal_board_spl (id)');
        $this->addSql('ALTER TABLE player_spl ADD CONSTRAINT FK_BFEC1ABD5063C752 FOREIGN KEY (game_spl_id) REFERENCES game_spl (id)');
        $this->addSql('ALTER TABLE row_spl ADD CONSTRAINT FK_FA290254E48FD905 FOREIGN KEY (game_id) REFERENCES game_spl (id)');
        $this->addSql('ALTER TABLE row_spl ADD CONSTRAINT FK_FA29025498F09B84 FOREIGN KEY (main_board_spl_id) REFERENCES main_board_spl (id)');
        $this->addSql('ALTER TABLE token_spl ADD CONSTRAINT FK_1BE5E6C267FDE44F FOREIGN KEY (personal_board_spl_id) REFERENCES personal_board_spl (id)');
        $this->addSql('ALTER TABLE token_spl ADD CONSTRAINT FK_1BE5E6C298F09B84 FOREIGN KEY (main_board_spl_id) REFERENCES main_board_spl (id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE development_cards_spl DROP FOREIGN KEY FK_EBA2C6379D2AC4D5');
        $this->addSql('ALTER TABLE development_cards_spl DROP FOREIGN KEY FK_EBA2C6377D70B948');
        $this->addSql('ALTER TABLE draw_cards_spl DROP FOREIGN KEY FK_333E9C85E48FD905');
        $this->addSql('ALTER TABLE draw_cards_spl DROP FOREIGN KEY FK_333E9C8598F09B84');
        $this->addSql('ALTER TABLE main_board_spl DROP FOREIGN KEY FK_149A0F57E48FD905');
        $this->addSql('ALTER TABLE noble_tile_spl DROP FOREIGN KEY FK_DCE27C667FDE44F');
        $this->addSql('ALTER TABLE noble_tile_spl DROP FOREIGN KEY FK_DCE27C698F09B84');
        $this->addSql('ALTER TABLE personal_board_spl DROP FOREIGN KEY FK_3B806E1FE48FD905');
        $this->addSql('ALTER TABLE player_spl DROP FOREIGN KEY FK_BFEC1ABD4BD389CC');
        $this->addSql('ALTER TABLE player_spl DROP FOREIGN KEY FK_BFEC1ABD5063C752');
        $this->addSql('ALTER TABLE row_spl DROP FOREIGN KEY FK_FA290254E48FD905');
        $this->addSql('ALTER TABLE row_spl DROP FOREIGN KEY FK_FA29025498F09B84');
        $this->addSql('ALTER TABLE token_spl DROP FOREIGN KEY FK_1BE5E6C267FDE44F');
        $this->addSql('ALTER TABLE token_spl DROP FOREIGN KEY FK_1BE5E6C298F09B84');
        $this->addSql('DROP TABLE development_cards_spl');
        $this->addSql('DROP TABLE draw_cards_spl');
        $this->addSql('DROP TABLE game_spl');
        $this->addSql('DROP TABLE main_board_spl');
        $this->addSql('DROP TABLE noble_tile_spl');
        $this->addSql('DROP TABLE personal_board_spl');
        $this->addSql('DROP TABLE player_spl');
        $this->addSql('DROP TABLE row_spl');
        $this->addSql('DROP TABLE token_spl');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }
}
