<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304083017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player_card_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, personal_board_id INT NOT NULL, card_id INT NOT NULL, UNIQUE INDEX UNIQ_1EB5F8D8D3F165E7 (help_id), INDEX IDX_1EB5F8D84BD389CC (personal_board_id), INDEX IDX_1EB5F8D84ACC9A20 (card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse_resource_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, warehouse_id INT NOT NULL, resource_id INT NOT NULL, UNIQUE INDEX UNIQ_48560FE2D3F165E7 (help_id), INDEX IDX_48560FE25080ECDE (warehouse_id), INDEX IDX_48560FE289329D25 (resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_card_glm ADD CONSTRAINT FK_1EB5F8D8D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE player_card_glm ADD CONSTRAINT FK_1EB5F8D84BD389CC FOREIGN KEY (personal_board_id) REFERENCES personal_board_glm (id)');
        $this->addSql('ALTER TABLE player_card_glm ADD CONSTRAINT FK_1EB5F8D84ACC9A20 FOREIGN KEY (card_id) REFERENCES card_glm (id)');
        $this->addSql('ALTER TABLE warehouse_resource_glm ADD CONSTRAINT FK_48560FE2D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE warehouse_resource_glm ADD CONSTRAINT FK_48560FE25080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse_glm (id)');
        $this->addSql('ALTER TABLE warehouse_resource_glm ADD CONSTRAINT FK_48560FE289329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE personal_board_glm_card_glm DROP FOREIGN KEY FK_2221355CDA826485');
        $this->addSql('ALTER TABLE personal_board_glm_card_glm DROP FOREIGN KEY FK_2221355CE216B924');
        $this->addSql('DROP TABLE personal_board_glm_card_glm');
        $this->addSql('ALTER TABLE player_tile_resource_glm ADD help_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE player_tile_resource_glm ADD CONSTRAINT FK_6F049CCED3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6F049CCED3F165E7 ON player_tile_resource_glm (help_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE personal_board_glm_card_glm (personal_board_glm_id INT NOT NULL, card_glm_id INT NOT NULL, INDEX IDX_2221355CE216B924 (personal_board_glm_id), INDEX IDX_2221355CDA826485 (card_glm_id), PRIMARY KEY(personal_board_glm_id, card_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE personal_board_glm_card_glm ADD CONSTRAINT FK_2221355CDA826485 FOREIGN KEY (card_glm_id) REFERENCES card_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personal_board_glm_card_glm ADD CONSTRAINT FK_2221355CE216B924 FOREIGN KEY (personal_board_glm_id) REFERENCES personal_board_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_card_glm DROP FOREIGN KEY FK_1EB5F8D8D3F165E7');
        $this->addSql('ALTER TABLE player_card_glm DROP FOREIGN KEY FK_1EB5F8D84BD389CC');
        $this->addSql('ALTER TABLE player_card_glm DROP FOREIGN KEY FK_1EB5F8D84ACC9A20');
        $this->addSql('ALTER TABLE warehouse_resource_glm DROP FOREIGN KEY FK_48560FE2D3F165E7');
        $this->addSql('ALTER TABLE warehouse_resource_glm DROP FOREIGN KEY FK_48560FE25080ECDE');
        $this->addSql('ALTER TABLE warehouse_resource_glm DROP FOREIGN KEY FK_48560FE289329D25');
        $this->addSql('DROP TABLE player_card_glm');
        $this->addSql('DROP TABLE warehouse_resource_glm');
        $this->addSql('ALTER TABLE player_tile_resource_glm DROP FOREIGN KEY FK_6F049CCED3F165E7');
        $this->addSql('DROP INDEX UNIQ_6F049CCED3F165E7 ON player_tile_resource_glm');
        $this->addSql('ALTER TABLE player_tile_resource_glm DROP help_id');
    }
}
