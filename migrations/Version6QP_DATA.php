<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version6QPDATA extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        for ($i = 1; $i <= 104; $i++) {
            if ($i % 5 == 0) {
                $this->connection->insert('card_six_qp', ['id' => $i, 'help_id' => null, 'value' => $i, 'points' => 2]);
                continue;
            }
            if ($i % 10 == 0) {
                $this->connection->insert('card_six_qp', ['id' => $i, 'help_id' => null, 'value' => $i, 'points' => 3]);
                continue;
            }
            if ($i % 11 == 0) {
                $this->connection->insert('card_six_qp', ['id' => $i, 'help_id' => null, 'value' => $i, 'points' => 5]);
                continue;
            }
            if ($i % 55 == 0) {
                $this->connection->insert('card_six_qp', ['id' => $i, 'help_id' => null, 'value' => $i, 'points' => 7]);
                continue;
            }
            $this->connection->insert('card_six_qp', ['id' => $i, 'help_id' => null, 'value' => $i, 'points' => 1]);
        }

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
