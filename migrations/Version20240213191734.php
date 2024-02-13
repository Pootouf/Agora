<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213191734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE development_cards_spl DROP FOREIGN KEY FK_EBA2C6377D70B948');
        $this->addSql('ALTER TABLE development_cards_spl DROP FOREIGN KEY FK_EBA2C6379D2AC4D5');
        $this->addSql('DROP INDEX IDX_EBA2C6377D70B948 ON development_cards_spl');
        $this->addSql('DROP INDEX IDX_EBA2C6379D2AC4D5 ON development_cards_spl');
        $this->addSql('ALTER TABLE development_cards_spl DROP draw_cards_spl_id, DROP row_spl_id');
        $this->addSql('ALTER TABLE draw_cards_spl DROP FOREIGN KEY FK_333E9C8598F09B84');
        $this->addSql('DROP INDEX IDX_333E9C8598F09B84 ON draw_cards_spl');
        $this->addSql('ALTER TABLE draw_cards_spl DROP main_board_spl_id');
        $this->addSql('ALTER TABLE game_spl ADD main_board_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_spl ADD CONSTRAINT FK_D997485B3ECE46F0 FOREIGN KEY (main_board_id) REFERENCES main_board_spl (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D997485B3ECE46F0 ON game_spl (main_board_id)');
        $this->addSql('ALTER TABLE main_board_spl DROP FOREIGN KEY FK_149A0F57E48FD905');
        $this->addSql('DROP INDEX UNIQ_149A0F57E48FD905 ON main_board_spl');
        $this->addSql('ALTER TABLE main_board_spl DROP game_id');
        $this->addSql('ALTER TABLE token_spl DROP FOREIGN KEY FK_1BE5E6C24CB3AB39');
        $this->addSql('ALTER TABLE token_spl DROP FOREIGN KEY FK_1BE5E6C267FDE44F');
        $this->addSql('ALTER TABLE token_spl DROP FOREIGN KEY FK_1BE5E6C298F09B84');
        $this->addSql('DROP INDEX IDX_1BE5E6C267FDE44F ON token_spl');
        $this->addSql('DROP INDEX IDX_1BE5E6C298F09B84 ON token_spl');
        $this->addSql('DROP INDEX IDX_1BE5E6C24CB3AB39 ON token_spl');
        $this->addSql('ALTER TABLE token_spl DROP personal_board_spl_id, DROP main_board_spl_id, DROP temp_personal_board_spl_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_spl DROP FOREIGN KEY FK_D997485B3ECE46F0');
        $this->addSql('DROP INDEX UNIQ_D997485B3ECE46F0 ON game_spl');
        $this->addSql('ALTER TABLE game_spl DROP main_board_id');
        $this->addSql('ALTER TABLE development_cards_spl ADD draw_cards_spl_id INT DEFAULT NULL, ADD row_spl_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE development_cards_spl ADD CONSTRAINT FK_EBA2C6377D70B948 FOREIGN KEY (row_spl_id) REFERENCES row_spl (id)');
        $this->addSql('ALTER TABLE development_cards_spl ADD CONSTRAINT FK_EBA2C6379D2AC4D5 FOREIGN KEY (draw_cards_spl_id) REFERENCES draw_cards_spl (id)');
        $this->addSql('CREATE INDEX IDX_EBA2C6377D70B948 ON development_cards_spl (row_spl_id)');
        $this->addSql('CREATE INDEX IDX_EBA2C6379D2AC4D5 ON development_cards_spl (draw_cards_spl_id)');
        $this->addSql('ALTER TABLE token_spl ADD personal_board_spl_id INT DEFAULT NULL, ADD main_board_spl_id INT DEFAULT NULL, ADD temp_personal_board_spl_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE token_spl ADD CONSTRAINT FK_1BE5E6C24CB3AB39 FOREIGN KEY (temp_personal_board_spl_id) REFERENCES personal_board_spl (id)');
        $this->addSql('ALTER TABLE token_spl ADD CONSTRAINT FK_1BE5E6C267FDE44F FOREIGN KEY (personal_board_spl_id) REFERENCES personal_board_spl (id)');
        $this->addSql('ALTER TABLE token_spl ADD CONSTRAINT FK_1BE5E6C298F09B84 FOREIGN KEY (main_board_spl_id) REFERENCES main_board_spl (id)');
        $this->addSql('CREATE INDEX IDX_1BE5E6C267FDE44F ON token_spl (personal_board_spl_id)');
        $this->addSql('CREATE INDEX IDX_1BE5E6C298F09B84 ON token_spl (main_board_spl_id)');
        $this->addSql('CREATE INDEX IDX_1BE5E6C24CB3AB39 ON token_spl (temp_personal_board_spl_id)');
        $this->addSql('ALTER TABLE draw_cards_spl ADD main_board_spl_id INT NOT NULL');
        $this->addSql('ALTER TABLE draw_cards_spl ADD CONSTRAINT FK_333E9C8598F09B84 FOREIGN KEY (main_board_spl_id) REFERENCES main_board_spl (id)');
        $this->addSql('CREATE INDEX IDX_333E9C8598F09B84 ON draw_cards_spl (main_board_spl_id)');
        $this->addSql('ALTER TABLE main_board_spl ADD game_id INT NOT NULL');
        $this->addSql('ALTER TABLE main_board_spl ADD CONSTRAINT FK_149A0F57E48FD905 FOREIGN KEY (game_id) REFERENCES game_spl (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_149A0F57E48FD905 ON main_board_spl (game_id)');
    }
}
