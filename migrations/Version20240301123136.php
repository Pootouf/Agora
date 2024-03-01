<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301123136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tile_bonus_glm ADD tile_glm_id INT DEFAULT NULL, ADD tile_bonus_glm_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tile_bonus_glm ADD CONSTRAINT FK_8E2D82E6E6306C44 FOREIGN KEY (tile_glm_id) REFERENCES tile_glm (id)');
        $this->addSql('ALTER TABLE tile_bonus_glm ADD CONSTRAINT FK_8E2D82E68744CF10 FOREIGN KEY (tile_bonus_glm_id) REFERENCES tile_glm (id)');
        $this->addSql('CREATE INDEX IDX_8E2D82E6E6306C44 ON tile_bonus_glm (tile_glm_id)');
        $this->addSql('CREATE INDEX IDX_8E2D82E68744CF10 ON tile_bonus_glm (tile_bonus_glm_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tile_bonus_glm DROP FOREIGN KEY FK_8E2D82E6E6306C44');
        $this->addSql('ALTER TABLE tile_bonus_glm DROP FOREIGN KEY FK_8E2D82E68744CF10');
        $this->addSql('DROP INDEX IDX_8E2D82E6E6306C44 ON tile_bonus_glm');
        $this->addSql('DROP INDEX IDX_8E2D82E68744CF10 ON tile_bonus_glm');
        $this->addSql('ALTER TABLE tile_bonus_glm DROP tile_glm_id, DROP tile_bonus_glm_id');
    }
}
