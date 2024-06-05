<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304201440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE warehouse_line_glm (id INT AUTO_INCREMENT NOT NULL, resource_id INT NOT NULL, warehouse_glm_id INT NOT NULL, quantity INT NOT NULL, coin_number INT NOT NULL, INDEX IDX_9B916C0589329D25 (resource_id), INDEX IDX_9B916C054DE9E880 (warehouse_glm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE warehouse_line_glm ADD CONSTRAINT FK_9B916C0589329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE warehouse_line_glm ADD CONSTRAINT FK_9B916C054DE9E880 FOREIGN KEY (warehouse_glm_id) REFERENCES warehouse_glm (id)');
        $this->addSql('ALTER TABLE warehouse_glm_resource_glm DROP FOREIGN KEY FK_7BCAAE8B4DE9E880');
        $this->addSql('ALTER TABLE warehouse_glm_resource_glm DROP FOREIGN KEY FK_7BCAAE8B6041DD0B');
        $this->addSql('ALTER TABLE warehouse_resource_glm DROP FOREIGN KEY FK_48560FE2D3F165E7');
        $this->addSql('ALTER TABLE warehouse_resource_glm DROP FOREIGN KEY FK_48560FE25080ECDE');
        $this->addSql('ALTER TABLE warehouse_resource_glm DROP FOREIGN KEY FK_48560FE289329D25');
        $this->addSql('DROP TABLE warehouse_glm_resource_glm');
        $this->addSql('DROP TABLE warehouse_resource_glm');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE warehouse_glm_resource_glm (warehouse_glm_id INT NOT NULL, resource_glm_id INT NOT NULL, INDEX IDX_7BCAAE8B4DE9E880 (warehouse_glm_id), INDEX IDX_7BCAAE8B6041DD0B (resource_glm_id), PRIMARY KEY(warehouse_glm_id, resource_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE warehouse_resource_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, warehouse_id INT NOT NULL, resource_id INT NOT NULL, INDEX IDX_48560FE289329D25 (resource_id), UNIQUE INDEX UNIQ_48560FE2D3F165E7 (help_id), INDEX IDX_48560FE25080ECDE (warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE warehouse_glm_resource_glm ADD CONSTRAINT FK_7BCAAE8B4DE9E880 FOREIGN KEY (warehouse_glm_id) REFERENCES warehouse_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_glm_resource_glm ADD CONSTRAINT FK_7BCAAE8B6041DD0B FOREIGN KEY (resource_glm_id) REFERENCES resource_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_resource_glm ADD CONSTRAINT FK_48560FE2D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE warehouse_resource_glm ADD CONSTRAINT FK_48560FE25080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse_glm (id)');
        $this->addSql('ALTER TABLE warehouse_resource_glm ADD CONSTRAINT FK_48560FE289329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE warehouse_line_glm DROP FOREIGN KEY FK_9B916C0589329D25');
        $this->addSql('ALTER TABLE warehouse_line_glm DROP FOREIGN KEY FK_9B916C054DE9E880');
        $this->addSql('DROP TABLE warehouse_line_glm');
    }
}
