<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302203930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player_tile_resource_glm (id INT AUTO_INCREMENT NOT NULL, resource_id INT NOT NULL, player_tile_glm_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_6F049CCE89329D25 (resource_id), INDEX IDX_6F049CCEBE0785D1 (player_tile_glm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_tile_resource_glm ADD CONSTRAINT FK_6F049CCE89329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE player_tile_resource_glm ADD CONSTRAINT FK_6F049CCEBE0785D1 FOREIGN KEY (player_tile_glm_id) REFERENCES player_tile_glm (id)');
        $this->addSql('ALTER TABLE player_tile_glm_resource_glm DROP FOREIGN KEY FK_43FC3219BE0785D1');
        $this->addSql('ALTER TABLE player_tile_glm_resource_glm DROP FOREIGN KEY FK_43FC32196041DD0B');
        $this->addSql('DROP TABLE player_tile_glm_resource_glm');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player_tile_glm_resource_glm (player_tile_glm_id INT NOT NULL, resource_glm_id INT NOT NULL, INDEX IDX_43FC3219BE0785D1 (player_tile_glm_id), INDEX IDX_43FC32196041DD0B (resource_glm_id), PRIMARY KEY(player_tile_glm_id, resource_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE player_tile_glm_resource_glm ADD CONSTRAINT FK_43FC3219BE0785D1 FOREIGN KEY (player_tile_glm_id) REFERENCES player_tile_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_tile_glm_resource_glm ADD CONSTRAINT FK_43FC32196041DD0B FOREIGN KEY (resource_glm_id) REFERENCES resource_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_tile_resource_glm DROP FOREIGN KEY FK_6F049CCE89329D25');
        $this->addSql('ALTER TABLE player_tile_resource_glm DROP FOREIGN KEY FK_6F049CCEBE0785D1');
        $this->addSql('DROP TABLE player_tile_resource_glm');
    }
}
