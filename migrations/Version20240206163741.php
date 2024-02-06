<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240206163741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE development_cards_spl ADD help_id INT DEFAULT NULL, ADD value INT NOT NULL');
        $this->addSql('ALTER TABLE development_cards_spl ADD CONSTRAINT FK_EBA2C637D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EBA2C637D3F165E7 ON development_cards_spl (help_id)');
        $this->addSql('ALTER TABLE draw_cards_spl ADD help_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE draw_cards_spl ADD CONSTRAINT FK_333E9C85D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_333E9C85D3F165E7 ON draw_cards_spl (help_id)');
        $this->addSql('ALTER TABLE game_spl ADD launched TINYINT(1) NOT NULL, ADD game_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE main_board_spl ADD help_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE main_board_spl ADD CONSTRAINT FK_149A0F57D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_149A0F57D3F165E7 ON main_board_spl (help_id)');
        $this->addSql('ALTER TABLE noble_tile_spl ADD help_id INT DEFAULT NULL, ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE noble_tile_spl ADD CONSTRAINT FK_DCE27C6D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DCE27C6D3F165E7 ON noble_tile_spl (help_id)');
        $this->addSql('ALTER TABLE personal_board_spl ADD help_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personal_board_spl ADD CONSTRAINT FK_3B806E1FD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3B806E1FD3F165E7 ON personal_board_spl (help_id)');
        $this->addSql('ALTER TABLE player_spl ADD username VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE row_spl ADD help_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE row_spl ADD CONSTRAINT FK_FA290254D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FA290254D3F165E7 ON row_spl (help_id)');
        $this->addSql('ALTER TABLE token_spl ADD help_id INT DEFAULT NULL, ADD temp_personal_board_spl_id INT DEFAULT NULL, ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE token_spl ADD CONSTRAINT FK_1BE5E6C2D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE token_spl ADD CONSTRAINT FK_1BE5E6C24CB3AB39 FOREIGN KEY (temp_personal_board_spl_id) REFERENCES personal_board_spl (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1BE5E6C2D3F165E7 ON token_spl (help_id)');
        $this->addSql('CREATE INDEX IDX_1BE5E6C24CB3AB39 ON token_spl (temp_personal_board_spl_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE development_cards_spl DROP FOREIGN KEY FK_EBA2C637D3F165E7');
        $this->addSql('DROP INDEX UNIQ_EBA2C637D3F165E7 ON development_cards_spl');
        $this->addSql('ALTER TABLE development_cards_spl DROP help_id, DROP value');
        $this->addSql('ALTER TABLE player_spl DROP username');
        $this->addSql('ALTER TABLE token_spl DROP FOREIGN KEY FK_1BE5E6C2D3F165E7');
        $this->addSql('ALTER TABLE token_spl DROP FOREIGN KEY FK_1BE5E6C24CB3AB39');
        $this->addSql('DROP INDEX UNIQ_1BE5E6C2D3F165E7 ON token_spl');
        $this->addSql('DROP INDEX IDX_1BE5E6C24CB3AB39 ON token_spl');
        $this->addSql('ALTER TABLE token_spl DROP help_id, DROP temp_personal_board_spl_id, DROP type');
        $this->addSql('ALTER TABLE draw_cards_spl DROP FOREIGN KEY FK_333E9C85D3F165E7');
        $this->addSql('DROP INDEX UNIQ_333E9C85D3F165E7 ON draw_cards_spl');
        $this->addSql('ALTER TABLE draw_cards_spl DROP help_id');
        $this->addSql('ALTER TABLE row_spl DROP FOREIGN KEY FK_FA290254D3F165E7');
        $this->addSql('DROP INDEX UNIQ_FA290254D3F165E7 ON row_spl');
        $this->addSql('ALTER TABLE row_spl DROP help_id');
        $this->addSql('ALTER TABLE personal_board_spl DROP FOREIGN KEY FK_3B806E1FD3F165E7');
        $this->addSql('DROP INDEX UNIQ_3B806E1FD3F165E7 ON personal_board_spl');
        $this->addSql('ALTER TABLE personal_board_spl DROP help_id');
        $this->addSql('ALTER TABLE game_spl DROP launched, DROP game_name');
        $this->addSql('ALTER TABLE noble_tile_spl DROP FOREIGN KEY FK_DCE27C6D3F165E7');
        $this->addSql('DROP INDEX UNIQ_DCE27C6D3F165E7 ON noble_tile_spl');
        $this->addSql('ALTER TABLE noble_tile_spl DROP help_id, DROP type');
        $this->addSql('ALTER TABLE main_board_spl DROP FOREIGN KEY FK_149A0F57D3F165E7');
        $this->addSql('DROP INDEX UNIQ_149A0F57D3F165E7 ON main_board_spl');
        $this->addSql('ALTER TABLE main_board_spl DROP help_id');
    }
}
