<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240321161730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personal_board_myr DROP hunted_prey_count');
        $this->addSql('ALTER TABLE prey_myr ADD tile_id INT DEFAULT NULL, ADD player_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prey_myr ADD CONSTRAINT FK_8DE90684638AF48B FOREIGN KEY (tile_id) REFERENCES tile_myr (id)');
        $this->addSql('ALTER TABLE prey_myr ADD CONSTRAINT FK_8DE9068499E6F5DF FOREIGN KEY (player_id) REFERENCES player_myr (id)');
        $this->addSql('CREATE INDEX IDX_8DE90684638AF48B ON prey_myr (tile_id)');
        $this->addSql('CREATE INDEX IDX_8DE9068499E6F5DF ON prey_myr (player_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personal_board_myr ADD hunted_prey_count INT NOT NULL');
        $this->addSql('ALTER TABLE prey_myr DROP FOREIGN KEY FK_8DE90684638AF48B');
        $this->addSql('ALTER TABLE prey_myr DROP FOREIGN KEY FK_8DE9068499E6F5DF');
        $this->addSql('DROP INDEX IDX_8DE90684638AF48B ON prey_myr');
        $this->addSql('DROP INDEX IDX_8DE9068499E6F5DF ON prey_myr');
        $this->addSql('ALTER TABLE prey_myr DROP tile_id, DROP player_id');
    }
}
