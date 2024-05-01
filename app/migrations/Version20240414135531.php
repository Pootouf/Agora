<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240414135531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_goal_player_myr_already_done (game_goal_myr_id INT NOT NULL, player_myr_id INT NOT NULL, INDEX IDX_6762AA46ED8E7CEA (game_goal_myr_id), INDEX IDX_6762AA46365CDD72 (player_myr_id), PRIMARY KEY(game_goal_myr_id, player_myr_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_goal_player_myr_already_done ADD CONSTRAINT FK_6762AA46ED8E7CEA FOREIGN KEY (game_goal_myr_id) REFERENCES game_goal_myr (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_goal_player_myr_already_done ADD CONSTRAINT FK_6762AA46365CDD72 FOREIGN KEY (player_myr_id) REFERENCES player_myr (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_goal_player_myr_already_done DROP FOREIGN KEY FK_6762AA46ED8E7CEA');
        $this->addSql('ALTER TABLE game_goal_player_myr_already_done DROP FOREIGN KEY FK_6762AA46365CDD72');
        $this->addSql('DROP TABLE game_goal_player_myr_already_done');
    }
}
