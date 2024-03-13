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
        $this->addSql('CREATE TABLE created_resource_glm (id INT AUTO_INCREMENT NOT NULL, resource_id INT NOT NULL, personal_board_glm_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_A60B273889329D25 (resource_id), INDEX IDX_A60B2738E216B924 (personal_board_glm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE created_resource_glm ADD CONSTRAINT FK_A60B273889329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE created_resource_glm ADD CONSTRAINT FK_A60B2738E216B924 FOREIGN KEY (personal_board_glm_id) REFERENCES personal_board_glm (id)');
        $this->addSql('ALTER TABLE pawn_glm ADD dice TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE player_glm ADD bot TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE selected_resource_glm CHANGE player_tile_id player_tile_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE created_resource_glm DROP FOREIGN KEY FK_A60B273889329D25');
        $this->addSql('ALTER TABLE created_resource_glm DROP FOREIGN KEY FK_A60B2738E216B924');
        $this->addSql('DROP TABLE created_resource_glm');
        $this->addSql('ALTER TABLE player_glm DROP bot');
        $this->addSql('ALTER TABLE selected_resource_glm CHANGE player_tile_id player_tile_id INT NOT NULL');
        $this->addSql('ALTER TABLE pawn_glm DROP dice');
    }
}
