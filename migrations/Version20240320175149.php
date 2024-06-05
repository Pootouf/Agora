<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320175149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE garden_tile_myr ADD prey_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE garden_tile_myr ADD CONSTRAINT FK_26C30D8FE90BA896 FOREIGN KEY (prey_id) REFERENCES prey_myr (id)');
        $this->addSql('CREATE INDEX IDX_26C30D8FE90BA896 ON garden_tile_myr (prey_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE garden_tile_myr DROP FOREIGN KEY FK_26C30D8FE90BA896');
        $this->addSql('DROP INDEX IDX_26C30D8FE90BA896 ON garden_tile_myr');
        $this->addSql('ALTER TABLE garden_tile_myr DROP prey_id');
    }
}
