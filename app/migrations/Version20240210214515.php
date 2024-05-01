<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240210214515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE board_user (board_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_57058F6AE7EC5785 (board_id), INDEX IDX_57058F6AA76ED395 (user_id), PRIMARY KEY(board_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE board_user ADD CONSTRAINT FK_57058F6AE7EC5785 FOREIGN KEY (board_id) REFERENCES board (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE board_user ADD CONSTRAINT FK_57058F6AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE board_user DROP FOREIGN KEY FK_57058F6AE7EC5785');
        $this->addSql('ALTER TABLE board_user DROP FOREIGN KEY FK_57058F6AA76ED395');
        $this->addSql('DROP TABLE board_user');
    }
}
