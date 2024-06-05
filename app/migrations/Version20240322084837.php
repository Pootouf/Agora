<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240322084837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personal_board_glm ADD resource_to_sell_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personal_board_glm ADD CONSTRAINT FK_B1DF087842673F87 FOREIGN KEY (resource_to_sell_id) REFERENCES resource_glm (id)');
        $this->addSql('CREATE INDEX IDX_B1DF087842673F87 ON personal_board_glm (resource_to_sell_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personal_board_glm DROP FOREIGN KEY FK_B1DF087842673F87');
        $this->addSql('DROP INDEX IDX_B1DF087842673F87 ON personal_board_glm');
        $this->addSql('ALTER TABLE personal_board_glm DROP resource_to_sell_id');
    }
}
