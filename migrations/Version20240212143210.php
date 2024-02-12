<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240212143210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card_cost_spl (id INT AUTO_INCREMENT NOT NULL, color VARCHAR(255) NOT NULL, price INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE development_cards_spl_card_cost_spl (development_cards_spl_id INT NOT NULL, card_cost_spl_id INT NOT NULL, INDEX IDX_9C1FE5F9E21D84C2 (development_cards_spl_id), INDEX IDX_9C1FE5F9CDB473AF (card_cost_spl_id), PRIMARY KEY(development_cards_spl_id, card_cost_spl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE development_cards_spl_card_cost_spl ADD CONSTRAINT FK_9C1FE5F9E21D84C2 FOREIGN KEY (development_cards_spl_id) REFERENCES development_cards_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE development_cards_spl_card_cost_spl ADD CONSTRAINT FK_9C1FE5F9CDB473AF FOREIGN KEY (card_cost_spl_id) REFERENCES card_cost_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE development_cards_spl DROP cost_tokens_color, DROP count_cost_tokens_color');
        $this->addSql('ALTER TABLE player_card_spl ADD help_id INT DEFAULT NULL, ADD personal_board_spl_id INT NOT NULL');
        $this->addSql('ALTER TABLE player_card_spl ADD CONSTRAINT FK_94EA9EBFD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE player_card_spl ADD CONSTRAINT FK_94EA9EBF67FDE44F FOREIGN KEY (personal_board_spl_id) REFERENCES personal_board_spl (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_94EA9EBFD3F165E7 ON player_card_spl (help_id)');
        $this->addSql('CREATE INDEX IDX_94EA9EBF67FDE44F ON player_card_spl (personal_board_spl_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE development_cards_spl_card_cost_spl DROP FOREIGN KEY FK_9C1FE5F9E21D84C2');
        $this->addSql('ALTER TABLE development_cards_spl_card_cost_spl DROP FOREIGN KEY FK_9C1FE5F9CDB473AF');
        $this->addSql('DROP TABLE card_cost_spl');
        $this->addSql('DROP TABLE development_cards_spl_card_cost_spl');
        $this->addSql('ALTER TABLE development_cards_spl ADD cost_tokens_color LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD count_cost_tokens_color LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE player_card_spl DROP FOREIGN KEY FK_94EA9EBFD3F165E7');
        $this->addSql('ALTER TABLE player_card_spl DROP FOREIGN KEY FK_94EA9EBF67FDE44F');
        $this->addSql('DROP INDEX UNIQ_94EA9EBFD3F165E7 ON player_card_spl');
        $this->addSql('DROP INDEX IDX_94EA9EBF67FDE44F ON player_card_spl');
        $this->addSql('ALTER TABLE player_card_spl DROP help_id, DROP personal_board_spl_id');
    }
}
