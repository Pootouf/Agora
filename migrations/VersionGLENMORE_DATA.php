<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Game\Glenmore\GlenmoreParameters;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class VersionGLENMORE_DATA extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Insertion of resources

        $this->connection->insert('resource_glm',
            ['id' => 1, 'help_id' => null, 'type' => GlenmoreParameters::$PRODUCTION_RESOURCE,
                'color' => GlenmoreParameters::$COLOR_GREEN]);
        $this->connection->insert('resource_glm',
            ['id' => 2, 'help_id' => null, 'type' => GlenmoreParameters::$PRODUCTION_RESOURCE,
                'color' => GlenmoreParameters::$COLOR_GREY]);
        $this->connection->insert('resource_glm',
            ['id' => 3, 'help_id' => null, 'type' => GlenmoreParameters::$PRODUCTION_RESOURCE,
                'color' => GlenmoreParameters::$COLOR_WHITE]);
        $this->connection->insert('resource_glm',
            ['id' => 4, 'help_id' => null, 'type' => GlenmoreParameters::$PRODUCTION_RESOURCE,
                'color' => GlenmoreParameters::$COLOR_BROWN]);
        $this->connection->insert('resource_glm',
            ['id' => 5, 'help_id' => null, 'type' => GlenmoreParameters::$PRODUCTION_RESOURCE,
                'color' => GlenmoreParameters::$COLOR_YELLOW]);
        $this->connection->insert('resource_glm',
            ['id' => 6, 'help_id' => null, 'type' => GlenmoreParameters::$WHISKY_RESOURCE,
                'color' => GlenmoreParameters::$COLOR_YELLOW]);
        $this->connection->insert('resource_glm',
            ['id' => 7, 'help_id' => null, 'type' => GlenmoreParameters::$HAT_RESOURCE,
                'color' => GlenmoreParameters::$COLOR_BROWN]);
        $this->connection->insert('resource_glm',
            ['id' => 8, 'help_id' => null, 'type' => GlenmoreParameters::$MOVEMENT_RESOURCE,
                'color' => GlenmoreParameters::$COLOR_WHITE]);
        $this->connection->insert('resource_glm',
            ['id' => 9, 'help_id' => null, 'type' => GlenmoreParameters::$VILLAGER_RESOURCE,
                'color' => GlenmoreParameters::$COLOR_BLACK]);

        // Insertion of cards

        // Insertion of card 1

        $this->connection->insert('tile_bonus_glm',
            ['id' => 1, 'help_id' => null, 'bonus' => 1]
        );

        $this->connection->insert('tile_bonus_glm_resource_glm',
            ['tile_bonus_glm_id' => 1, 'resource_glm_id' => 7]
        );

        $this->connection->insert('card_glm',
            ['id' => 1, 'help_id' => null, 'bonus_id' => 1,
                'value' => null, 'name' => GlenmoreParameters::$CARD_CASTLE_OF_MEY]
        );

        // Insertion of card 2

        $this->connection->insert('card_glm',
            ['id' => 2, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::$CARD_LOCH_LOCHY]
        );

        // Insertion of card 3

        $this->connection->insert('tile_bonus_glm',
            ['id' => 3, 'help_id' => null, 'bonus' => 1]
        );

        $this->connection->insert('tile_bonus_glm_resource_glm',
            ['tile_bonus_glm_id' => 3, 'resource_glm_id' => 7]
        );

        $this->connection->insert('card_glm',
            ['id' => 3, 'help_id' => null, 'bonus_id' => 3,
                'value' => null, 'name' => GlenmoreParameters::$CARD_CASTLE_STALKER]
        );

        // Insertion of card 4

        $this->connection->insert('tile_bonus_glm',
            ['id' => 2, 'help_id' => null, 'bonus' => 3]
        );

        $this->connection->insert('tile_bonus_glm_resource_glm',
            ['tile_bonus_glm_id' => 2, 'resource_glm_id' => 7]
        );

        $this->connection->insert('card_glm',
            ['id' => 4, 'help_id' => null, 'bonus_id' => 2,
                'value' => null, 'name' => GlenmoreParameters::$CARD_CAWDOR_CASTLE]
        );

        // Insertion of card 5

        $this->connection->insert('card_glm',
            ['id' => 5, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::$CARD_LOCH_MORAR]
        );

        // Insertion of card 6

        $this->connection->insert('card_glm',
            ['id' => 6, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::$CARD_LOCH_NESS]
        );

        // Insertion of card 7

        $this->connection->insert('card_glm',
            ['id' => 7, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::$CARD_IONA_ABBEY]
        );

        // Insertion of card 8

        $this->connection->insert('card_glm',
            ['id' => 8, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::$CARD_DUART_CASTLE]
        );

        // Insertion of card 9

        $this->connection->insert('tile_bonus_glm',
            ['id' => 4, 'help_id' => null, 'bonus' => 1]
        );

        $this->connection->insert('tile_bonus_glm_resource_glm',
            ['tile_bonus_glm_id' => 4, 'resource_glm_id' => 7]
        );

        $this->connection->insert('card_glm',
            ['id' => 9, 'help_id' => null, 'bonus_id' => 4,
                'value' => null, 'name' => GlenmoreParameters::$CARD_CASTLE_MOIL]
        );

        // Insertion of card 10

        $this->connection->insert('card_glm',
            ['id' => 10, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::$CARD_LOCH_SHIEL]
        );

        // Insertion of card 11

        $this->connection->insert('tile_bonus_glm',
            ['id' => 5, 'help_id' => null, 'bonus' => 1]
        );

        $this->connection->insert('tile_bonus_glm_resource_glm',
            ['tile_bonus_glm_id' => 5, 'resource_glm_id' => 7]
        );

        $this->connection->insert('card_glm',
            ['id' => 11, 'help_id' => null, 'bonus_id' => 5,
                'value' => null, 'name' => GlenmoreParameters::$CARD_DONAN_CASTLE]
        );

        // Insertion of card 12

        $this->connection->insert('card_glm',
            ['id' => 12, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::$CARD_LOCH_OICH]
        );

        // Insertion of card 13

        $this->connection->insert('tile_bonus_glm',
            ['id' => 6, 'help_id' => null, 'bonus' => 1]
        );

        $this->connection->insert('tile_bonus_glm_resource_glm',
            ['tile_bonus_glm_id' => 6, 'resource_glm_id' => 7]
        );

        $this->connection->insert('card_glm',
            ['id' => 13, 'help_id' => null, 'bonus_id' => 6,
                'value' => null, 'name' => GlenmoreParameters::$CARD_ARMADALE_CASTLE]
        );

        // Insertion of tiles


    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

}