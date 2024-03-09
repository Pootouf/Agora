<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240309081544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE selected_resource_glm (id INT AUTO_INCREMENT NOT NULL, resource_id INT NOT NULL, personal_board_glm_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_6C29A19689329D25 (resource_id), INDEX IDX_6C29A196E216B924 (personal_board_glm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE selected_resource_glm ADD CONSTRAINT FK_6C29A19689329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE selected_resource_glm ADD CONSTRAINT FK_6C29A196E216B924 FOREIGN KEY (personal_board_glm_id) REFERENCES personal_board_glm (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE selected_resource_glm DROP FOREIGN KEY FK_6C29A19689329D25');
        $this->addSql('ALTER TABLE selected_resource_glm DROP FOREIGN KEY FK_6C29A196E216B924');
        $this->addSql('DROP TABLE selected_resource_glm');
    }
}
