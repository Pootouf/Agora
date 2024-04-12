<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411193339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_glm ADD paused TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE game_myr ADD paused TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE game_six_qp ADD paused TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE game_spl ADD paused TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE player_glm CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE player_myr CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE player_six_qp CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE player_spl CHANGE id id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_spl DROP paused');
        $this->addSql('ALTER TABLE game_six_qp DROP paused');
        $this->addSql('ALTER TABLE player_spl CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE player_glm CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE game_glm DROP paused');
        $this->addSql('ALTER TABLE player_myr CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE game_myr DROP paused');
        $this->addSql('ALTER TABLE player_six_qp CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
