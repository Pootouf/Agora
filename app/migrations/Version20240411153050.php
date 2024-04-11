<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411153050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE private_message (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, recepient_id INT NOT NULL, content LONGTEXT NOT NULL, message_time DATETIME NOT NULL, title VARCHAR(255) DEFAULT NULL, INDEX IDX_4744FC9BF624B39D (sender_id), INDEX IDX_4744FC9BF1B7C6C (recepient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9BF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9BF1B7C6C FOREIGN KEY (recepient_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9BF624B39D');
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9BF1B7C6C');
        $this->addSql('DROP TABLE private_message');
    }
}
