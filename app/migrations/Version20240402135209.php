<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402135209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_goal_myr ADD main_board_level_one_id INT DEFAULT NULL, ADD main_board_level_two_id INT DEFAULT NULL, ADD main_board_level_three_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game_goal_myr ADD CONSTRAINT FK_654B667A6531CF96 FOREIGN KEY (main_board_level_one_id) REFERENCES main_board_myr (id)');
        $this->addSql('ALTER TABLE game_goal_myr ADD CONSTRAINT FK_654B667AE6D2859 FOREIGN KEY (main_board_level_two_id) REFERENCES main_board_myr (id)');
        $this->addSql('ALTER TABLE game_goal_myr ADD CONSTRAINT FK_654B667A4652CF56 FOREIGN KEY (main_board_level_three_id) REFERENCES main_board_myr (id)');
        $this->addSql('CREATE INDEX IDX_654B667A6531CF96 ON game_goal_myr (main_board_level_one_id)');
        $this->addSql('CREATE INDEX IDX_654B667AE6D2859 ON game_goal_myr (main_board_level_two_id)');
        $this->addSql('CREATE INDEX IDX_654B667A4652CF56 ON game_goal_myr (main_board_level_three_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_goal_myr DROP FOREIGN KEY FK_654B667A6531CF96');
        $this->addSql('ALTER TABLE game_goal_myr DROP FOREIGN KEY FK_654B667AE6D2859');
        $this->addSql('ALTER TABLE game_goal_myr DROP FOREIGN KEY FK_654B667A4652CF56');
        $this->addSql('DROP INDEX IDX_654B667A6531CF96 ON game_goal_myr');
        $this->addSql('DROP INDEX IDX_654B667AE6D2859 ON game_goal_myr');
        $this->addSql('DROP INDEX IDX_654B667A4652CF56 ON game_goal_myr');
        $this->addSql('ALTER TABLE game_goal_myr DROP main_board_level_one_id, DROP main_board_level_two_id, DROP main_board_level_three_id');
    }
}
