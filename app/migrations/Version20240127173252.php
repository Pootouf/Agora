<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240127173252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6686BA65F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_six_qp DROP FOREIGN KEY FK_7A29BE4DD3F165E7');
        $this->addSql('DROP INDEX UNIQ_7A29BE4DD3F165E7 ON game_six_qp');
        $this->addSql('ALTER TABLE game_six_qp DROP help_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE game_user');
        $this->addSql('ALTER TABLE game_six_qp ADD help_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game_six_qp ADD CONSTRAINT FK_7A29BE4DD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7A29BE4DD3F165E7 ON game_six_qp (help_id)');
    }
}
