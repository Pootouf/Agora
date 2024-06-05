<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240308151609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_tile_resource_glm ADD player_id INT NOT NULL');
        $this->addSql('ALTER TABLE player_tile_resource_glm ADD CONSTRAINT FK_6F049CCE99E6F5DF FOREIGN KEY (player_id) REFERENCES player_glm (id)');
        $this->addSql('CREATE INDEX IDX_6F049CCE99E6F5DF ON player_tile_resource_glm (player_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_tile_resource_glm DROP FOREIGN KEY FK_6F049CCE99E6F5DF');
        $this->addSql('DROP INDEX IDX_6F049CCE99E6F5DF ON player_tile_resource_glm');
        $this->addSql('ALTER TABLE player_tile_resource_glm DROP player_id');
    }
}
