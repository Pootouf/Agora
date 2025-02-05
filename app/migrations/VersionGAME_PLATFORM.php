<?php


declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Platform\Game;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version6QPPLATFORM extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
            $this->connection->insert('game', [
                'name' => 'Six Qui Prend',
                'descr_rule' => 'C\'est le six qui prend' ,
                'img_url' => 'images/6qp.jpg',
                'label' => '6QP',
                'is_active' => true,
                'min_players' => 2,
                'max_players' => 10
            ]);

        $this->connection->insert('game', [
            'name' => 'Splendor',
            'descr_rule' => 'C\'est le splendor' ,
            'img_url' => 'images/splendor.jpg',
            'label' => 'SPL',
            'is_active' => true,
            'min_players' => 2,
            'max_players' => 4
        ]);

        $this->connection->insert('game', [
            'name' => 'Glenmore',
            'descr_rule' => 'C\'est le glenmore' ,
            'img_url' => 'images/glenmore.jpg',
            'label' => 'GLM',
            'is_active' => true,
            'min_players' => 2,
            'max_players' => 5
        ]);

        $this->connection->insert('game', [
            'name' => 'Myrmes',
            'descr_rule' => 'C\'est le myrmes' ,
            'img_url' => 'images/myrmes.jpg',
            'label' => 'MYR',
            'is_active' => true,
            'min_players' => 2,
            'max_players' => 4
        ]);


    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
