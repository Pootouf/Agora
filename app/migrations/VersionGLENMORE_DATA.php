<?php

declare(strict_types = 1);

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
        $this->connection->insert('resource_glm',
            ['id' => 10, 'help_id' => null, 'type' => GlenmoreParameters::$POINT_RESOURCE,
                'color' => GlenmoreParameters::$COLOR_GREEN]);

        // Insertion of cards

        // Insertion of card 1

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 1, 'help_id' => null, 'amount' => 1, 'resource_id' => 7]
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

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 3, 'help_id' => null, 'amount' => 1, 'resource_id' => 7]
        );


        $this->connection->insert('card_glm',
            ['id' => 3, 'help_id' => null, 'bonus_id' => 3,
                'value' => null, 'name' => GlenmoreParameters::$CARD_CASTLE_STALKER]
        );

        // Insertion of card 4

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 2, 'help_id' => null, 'amount' => 3, 'resource_id' => 7]
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

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 4, 'help_id' => null, 'amount' => 1, 'resource_id' => 7]
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

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 5, 'help_id' => null, 'amount' => 1, 'resource_id' => 7]
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

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 6, 'help_id' => null, 'amount' => 1, 'resource_id' => 7]
        );

        $this->connection->insert('card_glm',
            ['id' => 13, 'help_id' => null, 'bonus_id' => 6,
                'value' => null, 'name' => GlenmoreParameters::$CARD_ARMADALE_CASTLE]
        );

        // Insertion of tiles

        // Insertion of tiles level 0

        // Insertion of tile 1

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 1, "help_id" => null, 'amount' => 1, 'resource_id' => 2]
        );

        $this->connection->insert('tile_glm',
            ['id' => 1, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::$TILE_NAME_QUARRY, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 1, 'tile_activation_bonus_glm_id' => 1]);

        // Insertion of tile 2

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 2, "help_id" => null, 'amount' => 1, 'resource_id' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 2, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::$TILE_NAME_FOREST, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 2, 'tile_activation_bonus_glm_id' => 2]);

        // Insertion of tile 3

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 3, "help_id" => null, 'amount' => 1, 'resource_id' => 3]
        );

        $this->connection->insert('tile_glm',
            ['id' => 3, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::$TILE_NAME_PASTURE, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 3, 'tile_activation_bonus_glm_id' => 3]);

        // Insertion of tile 4

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 4, "help_id" => null, 'amount' => 1, 'resource_id' => 8]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 7, "help_id" => null, 'amount' => 1, 'resource_id' => 9]
        );

        $this->connection->insert('tile_glm',
            ['id' => 4, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::$TILE_NAME_VILLAGE, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 4, 'tile_activation_bonus_glm_id' => 4]);

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 4, 'tile_buy_bonus_glm_id' => 7]);

        // Insertion of tile 5

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 5, "help_id" => null, 'amount' => 1, 'resource_id' => 6]
        );

        $this->connection->insert('tile_glm',
            ['id' => 5, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::$TILE_NAME_FIELD, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 5, 'tile_activation_bonus_glm_id' => 5]);

        // Insertion of tile 6


        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 6, "help_id" => null, 'amount' => 1, 'resource_id' => 4]
        );

        $this->connection->insert('tile_glm',
            ['id' => 6, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::$TILE_NAME_CATTLE, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 6, 'tile_activation_bonus_glm_id' => 6]);

        // Insertion of tile 7

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 7, "help_id" => null, 'amount' => 1, 'resource_id' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 7, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::$TILE_NAME_QUARRY, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 7, 'tile_activation_bonus_glm_id' => 7]);

        // Insertion of tile 8

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 8, "help_id" => null, 'amount' => 1, 'resource_id' => 1]
        );


        $this->connection->insert('tile_glm',
            ['id' => 8, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::$TILE_NAME_FOREST, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 8, 'tile_activation_bonus_glm_id' => 8]);

        // Insertion of tiles level 1

        // Insertion of tile 9

        $this->connection->insert('tile_glm',
            ['id' => 9, 'help_id' => null,
                'card_id' => 2, 'type' => GlenmoreParameters::$TILE_TYPE_BLUE,
                'name' => GlenmoreParameters::$CARD_LOCH_LOCHY, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 1]);

        // Insertion of tile 10

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 1, "help_id" => null, 'price' => 1, 'resource_id' => 3]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 2, "help_id" => null, 'price' => 2, 'resource_id' => 3]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 9, "help_id" => null, 'amount' => 2, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 10, "help_id" => null, 'amount' => 4, 'resource_id' => 10]
        );

        $this->connection->insert('tile_glm',
            ['id' => 10, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::$TILE_NAME_BUTCHER, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 10, 'tile_activation_cost_glm_id' => 1]
        );
        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 10, 'tile_activation_cost_glm_id' => 2]
        );
        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 10, 'tile_activation_bonus_glm_id' => 9]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 10, 'tile_activation_bonus_glm_id' => 10]
        );

        // Insertion of tile 11

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 3, "help_id" => null, 'price' => 1, 'resource_id' => 5]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 11, "help_id" => null, 'amount' => 1, 'resource_id' => 6]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 8, 'help_id' => null, 'resource_id' => 6, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 1, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 11, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::$TILE_NAME_DISTILLERY, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 11, 'tile_buy_cost_glm_id' => 1]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 11, 'tile_buy_bonus_glm_id' => 8]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 11, 'tile_activation_cost_glm_id' => 3]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 11, 'tile_activation_bonus_glm_id' => 11]
        );

        // Insertion of tile 12

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 12, "help_id" => null, 'amount' => 1, 'resource_id' => 3]
        );

        $this->connection->insert('tile_glm',
            ['id' => 12, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::$TILE_NAME_PASTURE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 12, 'tile_activation_bonus_glm_id' => 12]
        );

        // Insertion of tile 13

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 13, "help_id" => null, 'amount' => 1, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 14, "help_id" => null, 'amount' => 3, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 15, "help_id" => null, 'amount' => 5, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 4, "help_id" => null, 'price' => 1, 'resource_id' => null]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 5, "help_id" => null, 'price' => 2, 'resource_id' => null]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 6, "help_id" => null, 'price' => 3, 'resource_id' => null]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 2, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 13, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::$TILE_NAME_FAIR, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 13, 'tile_activation_bonus_glm_id' => 13]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 13, 'tile_activation_bonus_glm_id' => 14]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 13, 'tile_activation_bonus_glm_id' => 15]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 13, 'tile_activation_cost_glm_id' => 4]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 13, 'tile_activation_cost_glm_id' => 5]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 13, 'tile_activation_cost_glm_id' => 6]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 13, 'tile_buy_cost_glm_id' => 2]
        );

        // Insertion of tile 14

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 3, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 9, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 16, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 14, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::$TILE_NAME_VILLAGE, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 14, 'tile_buy_cost_glm_id' => 3]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 14, 'tile_buy_bonus_glm_id' => 9]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 14, 'tile_activation_bonus_glm_id' => 16]
        );

        // Insertion of tile 15

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 7, "help_id" => null, 'price' => 1, 'resource_id' => 5]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 17, "help_id" => null, 'amount' => 1, 'resource_id' => 6]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 10, 'help_id' => null, 'resource_id' => 6, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 4, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 15, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::$TILE_NAME_DISTILLERY, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 15, 'tile_buy_cost_glm_id' => 4]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 15, 'tile_buy_bonus_glm_id' => 10]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 15, 'tile_activation_cost_glm_id' => 7]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 15, 'tile_activation_bonus_glm_id' => 17]
        );

        // Insertion of tile 16

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 5, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 11, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 18, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 16, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::$TILE_NAME_VILLAGE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 16, 'tile_buy_cost_glm_id' => 5]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 16, 'tile_buy_bonus_glm_id' => 11]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 16, 'tile_activation_bonus_glm_id' => 18]
        );

        // Insertion of tile 17

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 19, "help_id" => null, 'amount' => 1, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 20, "help_id" => null, 'amount' => 3, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 21, "help_id" => null, 'amount' => 5, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 8, "help_id" => null, 'price' => 1, 'resource_id' => null]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 9, "help_id" => null, 'price' => 2, 'resource_id' => null]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 10, "help_id" => null, 'price' => 3, 'resource_id' => null]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 6, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 17, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::$TILE_NAME_FAIR, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 17, 'tile_activation_bonus_glm_id' => 19]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 17, 'tile_activation_bonus_glm_id' => 20]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 17, 'tile_activation_bonus_glm_id' => 21]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 17, 'tile_activation_cost_glm_id' => 8]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 17, 'tile_activation_cost_glm_id' => 9]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 17, 'tile_activation_cost_glm_id' => 10]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 17, 'tile_buy_cost_glm_id' => 6]
        );

        // Insertion of tile 18

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 22, "help_id" => null, 'amount' => 1, 'resource_id' => 5]
        );

        $this->connection->insert('tile_glm',
            ['id' => 18, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::$TILE_NAME_FIELD, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 18, 'tile_activation_bonus_glm_id' => 22]
        );

        // Insertion of tile 19

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 7, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 12, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 23, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 19, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::$TILE_NAME_VILLAGE, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 19, 'tile_buy_cost_glm_id' => 7]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 19, 'tile_buy_bonus_glm_id' => 12]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 19, 'tile_activation_bonus_glm_id' => 23]
        );

        // Insertion of tile 20

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 8, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 13, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 24, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 20, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::$TILE_NAME_VILLAGE, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 20, 'tile_buy_cost_glm_id' => 8]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 20, 'tile_buy_bonus_glm_id' => 13]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 20, 'tile_activation_bonus_glm_id' => 24]
        );

        // Insertion of tile 21

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 25, 'help_id' => null, 'resource_id' => 4, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 21, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::$TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::$TILE_NAME_CATTLE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 21, 'tile_activation_bonus_glm_id' => 25]
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

}