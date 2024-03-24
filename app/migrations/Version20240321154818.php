<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240321154818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE garden_tile_myr DROP FOREIGN KEY FK_26C30D8F89329D25');
        $this->addSql('ALTER TABLE garden_tile_myr DROP FOREIGN KEY FK_26C30D8FD3F165E7');
        $this->addSql('ALTER TABLE garden_tile_myr DROP FOREIGN KEY FK_26C30D8F99E6F5DF');
        $this->addSql('ALTER TABLE garden_tile_myr DROP FOREIGN KEY FK_26C30D8FE90BA896');
        $this->addSql('ALTER TABLE garden_tile_myr DROP FOREIGN KEY FK_26C30D8FC54C8C93');
        $this->addSql('ALTER TABLE garden_tile_myr_tile_myr DROP FOREIGN KEY FK_CE4C1713E7D11019');
        $this->addSql('ALTER TABLE garden_tile_myr_tile_myr DROP FOREIGN KEY FK_CE4C17133BEB74EC');
        $this->addSql('DROP TABLE garden_tile_myr');
        $this->addSql('DROP TABLE garden_tile_myr_tile_myr');
        $this->addSql('ALTER TABLE tile_myr DROP FOREIGN KEY FK_EE65EB01C54C8C93');
        $this->addSql('DROP INDEX IDX_EE65EB01C54C8C93 ON tile_myr');
        $this->addSql('ALTER TABLE tile_myr DROP type_id, DROP x_min_coord, DROP x_max_coord, DROP y_coord');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE garden_tile_myr (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, type_id INT NOT NULL, resource_id INT NOT NULL, player_id INT NOT NULL, prey_id INT DEFAULT NULL, harvested TINYINT(1) NOT NULL, INDEX IDX_26C30D8F99E6F5DF (player_id), UNIQUE INDEX UNIQ_26C30D8FC54C8C93 (type_id), INDEX IDX_26C30D8FE90BA896 (prey_id), UNIQUE INDEX UNIQ_26C30D8FD3F165E7 (help_id), INDEX IDX_26C30D8F89329D25 (resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE garden_tile_myr_tile_myr (garden_tile_myr_id INT NOT NULL, tile_myr_id INT NOT NULL, INDEX IDX_CE4C17133BEB74EC (garden_tile_myr_id), INDEX IDX_CE4C1713E7D11019 (tile_myr_id), PRIMARY KEY(garden_tile_myr_id, tile_myr_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE garden_tile_myr ADD CONSTRAINT FK_26C30D8F89329D25 FOREIGN KEY (resource_id) REFERENCES resource_myr (id)');
        $this->addSql('ALTER TABLE garden_tile_myr ADD CONSTRAINT FK_26C30D8FD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE garden_tile_myr ADD CONSTRAINT FK_26C30D8F99E6F5DF FOREIGN KEY (player_id) REFERENCES player_myr (id)');
        $this->addSql('ALTER TABLE garden_tile_myr ADD CONSTRAINT FK_26C30D8FE90BA896 FOREIGN KEY (prey_id) REFERENCES prey_myr (id)');
        $this->addSql('ALTER TABLE garden_tile_myr ADD CONSTRAINT FK_26C30D8FC54C8C93 FOREIGN KEY (type_id) REFERENCES tile_type_myr (id)');
        $this->addSql('ALTER TABLE garden_tile_myr_tile_myr ADD CONSTRAINT FK_CE4C1713E7D11019 FOREIGN KEY (tile_myr_id) REFERENCES tile_myr (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE garden_tile_myr_tile_myr ADD CONSTRAINT FK_CE4C17133BEB74EC FOREIGN KEY (garden_tile_myr_id) REFERENCES garden_tile_myr (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_myr ADD type_id INT NOT NULL, ADD x_min_coord INT NOT NULL, ADD x_max_coord INT NOT NULL, ADD y_coord INT NOT NULL');
        $this->addSql('ALTER TABLE tile_myr ADD CONSTRAINT FK_EE65EB01C54C8C93 FOREIGN KEY (type_id) REFERENCES tile_type_myr (id)');
        $this->addSql('CREATE INDEX IDX_EE65EB01C54C8C93 ON tile_myr (type_id)');
    }
}
