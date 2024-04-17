<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240416152445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_glm ADD points INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card_six_qp CHANGE points points INT DEFAULT NULL');
        $this->addSql('ALTER TABLE development_cards_spl ADD points INT DEFAULT NULL, DROP prestige_points');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_six_qp CHANGE points points INT NOT NULL');
        $this->addSql('ALTER TABLE development_cards_spl ADD prestige_points INT NOT NULL, DROP points');
        $this->addSql('ALTER TABLE card_glm DROP points');
    }
}
