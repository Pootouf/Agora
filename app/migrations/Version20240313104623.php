<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240313104623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pawn_glm ADD dice TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE player_glm ADD bot TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE selected_resource_glm CHANGE player_tile_id player_tile_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_glm DROP bot');
        $this->addSql('ALTER TABLE selected_resource_glm CHANGE player_tile_id player_tile_id INT NOT NULL');
        $this->addSql('ALTER TABLE pawn_glm DROP dice');
    }
}
