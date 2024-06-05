<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version6QP_DATA extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        for ($i = 1; $i <= 104; $i++) {
            $points_value = 1;
            if ($i % 5 == 0) {
                $points_value = 2;
            }
            if ($i % 10 == 0) {
                $points_value = 3;
            }
            if ($i % 11 == 0) {
                $points_value = 5;
            }
            if ($i % 55 == 0) {
                $points_value = 7;
            }
            $this->connection->insert('card_six_qp', ['id' => $i, 'help_id' => null, 'value' => $i, 'points' => $points_value]);
        }

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
