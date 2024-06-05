<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240321160808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pheromon_myr (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, type_id INT NOT NULL, harvested TINYINT(1) NOT NULL, INDEX IDX_F38E6A3899E6F5DF (player_id), INDEX IDX_F38E6A38C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pheromon_tile_myr (id INT AUTO_INCREMENT NOT NULL, tile_id INT NOT NULL, resource_id INT DEFAULT NULL, pheromon_myr_id INT NOT NULL, INDEX IDX_28612FF9638AF48B (tile_id), INDEX IDX_28612FF989329D25 (resource_id), INDEX IDX_28612FF9D92B5CE1 (pheromon_myr_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pheromon_myr ADD CONSTRAINT FK_F38E6A3899E6F5DF FOREIGN KEY (player_id) REFERENCES player_myr (id)');
        $this->addSql('ALTER TABLE pheromon_myr ADD CONSTRAINT FK_F38E6A38C54C8C93 FOREIGN KEY (type_id) REFERENCES tile_type_myr (id)');
        $this->addSql('ALTER TABLE pheromon_tile_myr ADD CONSTRAINT FK_28612FF9638AF48B FOREIGN KEY (tile_id) REFERENCES tile_myr (id)');
        $this->addSql('ALTER TABLE pheromon_tile_myr ADD CONSTRAINT FK_28612FF989329D25 FOREIGN KEY (resource_id) REFERENCES resource_myr (id)');
        $this->addSql('ALTER TABLE pheromon_tile_myr ADD CONSTRAINT FK_28612FF9D92B5CE1 FOREIGN KEY (pheromon_myr_id) REFERENCES pheromon_myr (id)');
        $this->addSql('ALTER TABLE tile_myr ADD coord_x INT NOT NULL, ADD coord_y INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pheromon_myr DROP FOREIGN KEY FK_F38E6A3899E6F5DF');
        $this->addSql('ALTER TABLE pheromon_myr DROP FOREIGN KEY FK_F38E6A38C54C8C93');
        $this->addSql('ALTER TABLE pheromon_tile_myr DROP FOREIGN KEY FK_28612FF9638AF48B');
        $this->addSql('ALTER TABLE pheromon_tile_myr DROP FOREIGN KEY FK_28612FF989329D25');
        $this->addSql('ALTER TABLE pheromon_tile_myr DROP FOREIGN KEY FK_28612FF9D92B5CE1');
        $this->addSql('DROP TABLE pheromon_myr');
        $this->addSql('DROP TABLE pheromon_tile_myr');
        $this->addSql('ALTER TABLE tile_myr DROP coord_x, DROP coord_y');
    }
}
