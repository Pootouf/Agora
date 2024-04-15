<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411184102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_glm CHANGE username username VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE player_myr CHANGE username username VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE player_six_qp CHANGE username username VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE player_spl CHANGE username username VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_spl CHANGE username username VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE player_glm CHANGE username username VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE player_myr CHANGE username username VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE player_six_qp CHANGE username username VARCHAR(255) NOT NULL');
    }
}
