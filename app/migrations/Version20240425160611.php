<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425160611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_invited (board_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D8A0F1A1E7EC5785 (board_id), INDEX IDX_D8A0F1A1A76ED395 (user_id), PRIMARY KEY(board_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_invited ADD CONSTRAINT FK_D8A0F1A1E7EC5785 FOREIGN KEY (board_id) REFERENCES board (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_invited ADD CONSTRAINT FK_D8A0F1A1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_invited DROP FOREIGN KEY FK_D8A0F1A1E7EC5785');
        $this->addSql('ALTER TABLE user_invited DROP FOREIGN KEY FK_D8A0F1A1A76ED395');
        $this->addSql('DROP TABLE user_invited');
    }
}
