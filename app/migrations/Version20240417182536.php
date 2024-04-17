<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240417182536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_spl DROP FOREIGN KEY FK_BFEC1ABD5063C752');
        $this->addSql('DROP INDEX IDX_BFEC1ABD5063C752 ON player_spl');
        $this->addSql('ALTER TABLE player_spl CHANGE game_spl_id game_id INT NOT NULL');
        $this->addSql('ALTER TABLE player_spl ADD CONSTRAINT FK_BFEC1ABDE48FD905 FOREIGN KEY (game_id) REFERENCES game_spl (id)');
        $this->addSql('CREATE INDEX IDX_BFEC1ABDE48FD905 ON player_spl (game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_spl DROP FOREIGN KEY FK_BFEC1ABDE48FD905');
        $this->addSql('DROP INDEX IDX_BFEC1ABDE48FD905 ON player_spl');
        $this->addSql('ALTER TABLE player_spl CHANGE game_id game_spl_id INT NOT NULL');
        $this->addSql('ALTER TABLE player_spl ADD CONSTRAINT FK_BFEC1ABD5063C752 FOREIGN KEY (game_spl_id) REFERENCES game_spl (id)');
        $this->addSql('CREATE INDEX IDX_BFEC1ABD5063C752 ON player_spl (game_spl_id)');
    }
}
