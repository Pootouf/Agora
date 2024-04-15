<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411173630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_glm ADD excluded TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE player_myr ADD excluded TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE player_six_qp ADD excluded TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE player_spl ADD excluded TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_spl DROP excluded');
        $this->addSql('ALTER TABLE player_glm DROP excluded');
        $this->addSql('ALTER TABLE player_myr DROP excluded');
        $this->addSql('ALTER TABLE player_six_qp DROP excluded');
    }
}
