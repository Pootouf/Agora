<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320141430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prey_myr DROP FOREIGN KEY FK_8DE90684638AF48B');
        $this->addSql('ALTER TABLE prey_myr DROP FOREIGN KEY FK_8DE90684E48FD905');
        $this->addSql('DROP INDEX IDX_8DE90684E48FD905 ON prey_myr');
        $this->addSql('DROP INDEX IDX_8DE90684638AF48B ON prey_myr');
        $this->addSql('ALTER TABLE prey_myr DROP tile_id, DROP game_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prey_myr ADD tile_id INT NOT NULL, ADD game_id INT NOT NULL');
        $this->addSql('ALTER TABLE prey_myr ADD CONSTRAINT FK_8DE90684638AF48B FOREIGN KEY (tile_id) REFERENCES tile_myr (id)');
        $this->addSql('ALTER TABLE prey_myr ADD CONSTRAINT FK_8DE90684E48FD905 FOREIGN KEY (game_id) REFERENCES game_myr (id)');
        $this->addSql('CREATE INDEX IDX_8DE90684E48FD905 ON prey_myr (game_id)');
        $this->addSql('CREATE INDEX IDX_8DE90684638AF48B ON prey_myr (tile_id)');
    }
}
