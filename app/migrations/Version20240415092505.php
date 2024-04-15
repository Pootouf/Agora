<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240415092505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anthill_worker_myr DROP FOREIGN KEY FK_1348953199E6F5DF');
        $this->addSql('DROP INDEX IDX_1348953199E6F5DF ON anthill_worker_myr');
        $this->addSql('ALTER TABLE anthill_worker_myr DROP player_id');
        $this->addSql('ALTER TABLE nurse_myr DROP FOREIGN KEY FK_A21C021499E6F5DF');
        $this->addSql('DROP INDEX IDX_A21C021499E6F5DF ON nurse_myr');
        $this->addSql('ALTER TABLE nurse_myr DROP player_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anthill_worker_myr ADD player_id INT NOT NULL');
        $this->addSql('ALTER TABLE anthill_worker_myr ADD CONSTRAINT FK_1348953199E6F5DF FOREIGN KEY (player_id) REFERENCES player_myr (id)');
        $this->addSql('CREATE INDEX IDX_1348953199E6F5DF ON anthill_worker_myr (player_id)');
        $this->addSql('ALTER TABLE nurse_myr ADD player_id INT NOT NULL');
        $this->addSql('ALTER TABLE nurse_myr ADD CONSTRAINT FK_A21C021499E6F5DF FOREIGN KEY (player_id) REFERENCES player_myr (id)');
        $this->addSql('CREATE INDEX IDX_A21C021499E6F5DF ON nurse_myr (player_id)');
    }
}
