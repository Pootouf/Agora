<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240212170550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE noble_tile_spl_card_cost_spl (noble_tile_spl_id INT NOT NULL, card_cost_spl_id INT NOT NULL, INDEX IDX_F9DB3689557A721D (noble_tile_spl_id), INDEX IDX_F9DB3689CDB473AF (card_cost_spl_id), PRIMARY KEY(noble_tile_spl_id, card_cost_spl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE noble_tile_spl_card_cost_spl ADD CONSTRAINT FK_F9DB3689557A721D FOREIGN KEY (noble_tile_spl_id) REFERENCES noble_tile_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE noble_tile_spl_card_cost_spl ADD CONSTRAINT FK_F9DB3689CDB473AF FOREIGN KEY (card_cost_spl_id) REFERENCES card_cost_spl (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE noble_tile_spl DROP cost_cards_color, DROP count_cost_cards_color');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE noble_tile_spl_card_cost_spl DROP FOREIGN KEY FK_F9DB3689557A721D');
        $this->addSql('ALTER TABLE noble_tile_spl_card_cost_spl DROP FOREIGN KEY FK_F9DB3689CDB473AF');
        $this->addSql('DROP TABLE noble_tile_spl_card_cost_spl');
        $this->addSql('ALTER TABLE noble_tile_spl ADD cost_cards_color LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD count_cost_cards_color LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }
}
