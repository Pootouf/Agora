<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214081109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE selected_token_spl (id INT AUTO_INCREMENT NOT NULL, token_id INT NOT NULL, personal_board_spl_id INT NOT NULL, INDEX IDX_439AF52941DEE7B9 (token_id), INDEX IDX_439AF52967FDE44F (personal_board_spl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE selected_token_spl ADD CONSTRAINT FK_439AF52941DEE7B9 FOREIGN KEY (token_id) REFERENCES token_spl (id)');
        $this->addSql('ALTER TABLE selected_token_spl ADD CONSTRAINT FK_439AF52967FDE44F FOREIGN KEY (personal_board_spl_id) REFERENCES personal_board_spl (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE selected_token_spl DROP FOREIGN KEY FK_439AF52941DEE7B9');
        $this->addSql('ALTER TABLE selected_token_spl DROP FOREIGN KEY FK_439AF52967FDE44F');
        $this->addSql('DROP TABLE selected_token_spl');
    }
}
