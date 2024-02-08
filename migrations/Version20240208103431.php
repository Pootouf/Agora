<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208103431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE board (id INT AUTO_INCREMENT NOT NULL, nb_user_max INT NOT NULL, status VARCHAR(20) NOT NULL, creation_date DATE NOT NULL, invitation_timer TIME NOT NULL, inactivity_timer TIME NOT NULL, invitation_hash VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, descr_rule VARCHAR(255) NOT NULL, img_url VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, content VARCHAR(255) NOT NULL, date DATE NOT NULL, author_id INT NOT NULL, game_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(50) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE game_user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE board');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE game_user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
