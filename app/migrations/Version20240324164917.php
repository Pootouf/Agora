<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324164917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anthill_hole_myr ADD main_board_myr_id INT NOT NULL');
        $this->addSql('ALTER TABLE anthill_hole_myr ADD CONSTRAINT FK_396A22B11CFABAB2 FOREIGN KEY (main_board_myr_id) REFERENCES main_board_myr (id)');
        $this->addSql('CREATE INDEX IDX_396A22B11CFABAB2 ON anthill_hole_myr (main_board_myr_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anthill_hole_myr DROP FOREIGN KEY FK_396A22B11CFABAB2');
        $this->addSql('DROP INDEX IDX_396A22B11CFABAB2 ON anthill_hole_myr');
        $this->addSql('ALTER TABLE anthill_hole_myr DROP main_board_myr_id');
    }
}
