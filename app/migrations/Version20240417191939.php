<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240417191939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_glm DROP FOREIGN KEY FK_35B37CDAD5889A39');
        $this->addSql('DROP INDEX IDX_35B37CDAD5889A39 ON player_glm');
        $this->addSql('ALTER TABLE player_glm CHANGE game_glm_id game_id INT NOT NULL');
        $this->addSql('ALTER TABLE player_glm ADD CONSTRAINT FK_35B37CDAE48FD905 FOREIGN KEY (game_id) REFERENCES game_glm (id)');
        $this->addSql('CREATE INDEX IDX_35B37CDAE48FD905 ON player_glm (game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_glm DROP FOREIGN KEY FK_35B37CDAE48FD905');
        $this->addSql('DROP INDEX IDX_35B37CDAE48FD905 ON player_glm');
        $this->addSql('ALTER TABLE player_glm CHANGE game_id game_glm_id INT NOT NULL');
        $this->addSql('ALTER TABLE player_glm ADD CONSTRAINT FK_35B37CDAD5889A39 FOREIGN KEY (game_glm_id) REFERENCES game_glm (id)');
        $this->addSql('CREATE INDEX IDX_35B37CDAD5889A39 ON player_glm (game_glm_id)');
    }
}
