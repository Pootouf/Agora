<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207205652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player_card_spl (id INT AUTO_INCREMENT NOT NULL, development_card_id INT DEFAULT NULL, game_id INT NOT NULL, personal_board_id INT NOT NULL, reserved TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_94EA9EBF128A2002 (development_card_id), INDEX IDX_94EA9EBFE48FD905 (game_id), UNIQUE INDEX UNIQ_94EA9EBF4BD389CC (personal_board_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_card_spl ADD CONSTRAINT FK_94EA9EBF128A2002 FOREIGN KEY (development_card_id) REFERENCES development_cards_spl (id)');
        $this->addSql('ALTER TABLE player_card_spl ADD CONSTRAINT FK_94EA9EBFE48FD905 FOREIGN KEY (game_id) REFERENCES game_spl (id)');
        $this->addSql('ALTER TABLE player_card_spl ADD CONSTRAINT FK_94EA9EBF4BD389CC FOREIGN KEY (personal_board_id) REFERENCES personal_board_spl (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_card_spl DROP FOREIGN KEY FK_94EA9EBF128A2002');
        $this->addSql('ALTER TABLE player_card_spl DROP FOREIGN KEY FK_94EA9EBFE48FD905');
        $this->addSql('ALTER TABLE player_card_spl DROP FOREIGN KEY FK_94EA9EBF4BD389CC');
        $this->addSql('DROP TABLE player_card_spl');
    }
}
