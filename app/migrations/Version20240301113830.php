<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301113830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tile_bonus_glm_resource_glm DROP FOREIGN KEY FK_5D4FB906041DD0B');
        $this->addSql('ALTER TABLE tile_bonus_glm_resource_glm DROP FOREIGN KEY FK_5D4FB908744CF10');
        $this->addSql('ALTER TABLE tile_cost_glm_resource_glm DROP FOREIGN KEY FK_BD55CBFA6041DD0B');
        $this->addSql('ALTER TABLE tile_cost_glm_resource_glm DROP FOREIGN KEY FK_BD55CBFAECB794C1');
        $this->addSql('DROP TABLE tile_bonus_glm_resource_glm');
        $this->addSql('DROP TABLE tile_cost_glm_resource_glm');
        $this->addSql('ALTER TABLE tile_bonus_glm ADD resource_id INT DEFAULT NULL, CHANGE bonus amount INT NOT NULL');
        $this->addSql('ALTER TABLE tile_bonus_glm ADD CONSTRAINT FK_8E2D82E689329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('CREATE INDEX IDX_8E2D82E689329D25 ON tile_bonus_glm (resource_id)');
        $this->addSql('ALTER TABLE tile_cost_glm ADD resource_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tile_cost_glm ADD CONSTRAINT FK_86EA1A4E89329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('CREATE INDEX IDX_86EA1A4E89329D25 ON tile_cost_glm (resource_id)');
        $this->addSql('ALTER TABLE tile_glm DROP FOREIGN KEY FK_594F8536CFF0DDC3');
        $this->addSql('ALTER TABLE tile_glm DROP FOREIGN KEY FK_594F85369B0863B3');
        $this->addSql('DROP INDEX UNIQ_594F85369B0863B3 ON tile_glm');
        $this->addSql('DROP INDEX UNIQ_594F8536CFF0DDC3 ON tile_glm');
        $this->addSql('ALTER TABLE tile_glm DROP buy_bonus_id, DROP activation_bonus_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tile_bonus_glm_resource_glm (tile_bonus_glm_id INT NOT NULL, resource_glm_id INT NOT NULL, INDEX IDX_5D4FB908744CF10 (tile_bonus_glm_id), INDEX IDX_5D4FB906041DD0B (resource_glm_id), PRIMARY KEY(tile_bonus_glm_id, resource_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tile_cost_glm_resource_glm (tile_cost_glm_id INT NOT NULL, resource_glm_id INT NOT NULL, INDEX IDX_BD55CBFAECB794C1 (tile_cost_glm_id), INDEX IDX_BD55CBFA6041DD0B (resource_glm_id), PRIMARY KEY(tile_cost_glm_id, resource_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tile_bonus_glm_resource_glm ADD CONSTRAINT FK_5D4FB906041DD0B FOREIGN KEY (resource_glm_id) REFERENCES resource_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_bonus_glm_resource_glm ADD CONSTRAINT FK_5D4FB908744CF10 FOREIGN KEY (tile_bonus_glm_id) REFERENCES tile_bonus_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_cost_glm_resource_glm ADD CONSTRAINT FK_BD55CBFA6041DD0B FOREIGN KEY (resource_glm_id) REFERENCES resource_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_cost_glm_resource_glm ADD CONSTRAINT FK_BD55CBFAECB794C1 FOREIGN KEY (tile_cost_glm_id) REFERENCES tile_cost_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_bonus_glm DROP FOREIGN KEY FK_8E2D82E689329D25');
        $this->addSql('DROP INDEX IDX_8E2D82E689329D25 ON tile_bonus_glm');
        $this->addSql('ALTER TABLE tile_bonus_glm DROP resource_id, CHANGE amount bonus INT NOT NULL');
        $this->addSql('ALTER TABLE tile_cost_glm DROP FOREIGN KEY FK_86EA1A4E89329D25');
        $this->addSql('DROP INDEX IDX_86EA1A4E89329D25 ON tile_cost_glm');
        $this->addSql('ALTER TABLE tile_cost_glm DROP resource_id');
        $this->addSql('ALTER TABLE tile_glm ADD buy_bonus_id INT DEFAULT NULL, ADD activation_bonus_id INT NOT NULL');
        $this->addSql('ALTER TABLE tile_glm ADD CONSTRAINT FK_594F8536CFF0DDC3 FOREIGN KEY (activation_bonus_id) REFERENCES tile_bonus_glm (id)');
        $this->addSql('ALTER TABLE tile_glm ADD CONSTRAINT FK_594F85369B0863B3 FOREIGN KEY (buy_bonus_id) REFERENCES tile_bonus_glm (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_594F85369B0863B3 ON tile_glm (buy_bonus_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_594F8536CFF0DDC3 ON tile_glm (activation_bonus_id)');
    }
}
