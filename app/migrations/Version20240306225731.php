<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306225731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_cost_spl ADD help_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card_cost_spl ADD CONSTRAINT FK_D08F2EA6D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D08F2EA6D3F165E7 ON card_cost_spl (help_id)');
        $this->addSql('ALTER TABLE draw_cards_spl ADD help_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE draw_cards_spl ADD CONSTRAINT FK_333E9C85D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_333E9C85D3F165E7 ON draw_cards_spl (help_id)');
        $this->addSql('ALTER TABLE noble_tile_spl ADD help_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE noble_tile_spl ADD CONSTRAINT FK_DCE27C6D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DCE27C6D3F165E7 ON noble_tile_spl (help_id)');
        $this->addSql('ALTER TABLE player_card_spl ADD help_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE player_card_spl ADD CONSTRAINT FK_94EA9EBFD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_94EA9EBFD3F165E7 ON player_card_spl (help_id)');
        $this->addSql('ALTER TABLE row_spl ADD help_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE row_spl ADD CONSTRAINT FK_FA290254D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FA290254D3F165E7 ON row_spl (help_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE draw_cards_spl DROP FOREIGN KEY FK_333E9C85D3F165E7');
        $this->addSql('DROP INDEX UNIQ_333E9C85D3F165E7 ON draw_cards_spl');
        $this->addSql('ALTER TABLE draw_cards_spl DROP help_id');
        $this->addSql('ALTER TABLE card_cost_spl DROP FOREIGN KEY FK_D08F2EA6D3F165E7');
        $this->addSql('DROP INDEX UNIQ_D08F2EA6D3F165E7 ON card_cost_spl');
        $this->addSql('ALTER TABLE card_cost_spl DROP help_id');
        $this->addSql('ALTER TABLE row_spl DROP FOREIGN KEY FK_FA290254D3F165E7');
        $this->addSql('DROP INDEX UNIQ_FA290254D3F165E7 ON row_spl');
        $this->addSql('ALTER TABLE row_spl DROP help_id');
        $this->addSql('ALTER TABLE noble_tile_spl DROP FOREIGN KEY FK_DCE27C6D3F165E7');
        $this->addSql('DROP INDEX UNIQ_DCE27C6D3F165E7 ON noble_tile_spl');
        $this->addSql('ALTER TABLE noble_tile_spl DROP help_id');
        $this->addSql('ALTER TABLE player_card_spl DROP FOREIGN KEY FK_94EA9EBFD3F165E7');
        $this->addSql('DROP INDEX UNIQ_94EA9EBFD3F165E7 ON player_card_spl');
        $this->addSql('ALTER TABLE player_card_spl DROP help_id');
    }
}
