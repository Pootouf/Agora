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
            ['id' => 1, 'help_id' => null, 'type' => GlenmoreParameters::PRODUCTION_RESOURCE,
                'color' => GlenmoreParameters::COLOR_GREEN]);
        $this->connection->insert('resource_glm',
            ['id' => 2, 'help_id' => null, 'type' => GlenmoreParameters::PRODUCTION_RESOURCE,
                'color' => GlenmoreParameters::COLOR_GREY]);
        $this->connection->insert('resource_glm',
            ['id' => 3, 'help_id' => null, 'type' => GlenmoreParameters::PRODUCTION_RESOURCE,
                'color' => GlenmoreParameters::COLOR_WHITE]);
        $this->connection->insert('resource_glm',
            ['id' => 4, 'help_id' => null, 'type' => GlenmoreParameters::PRODUCTION_RESOURCE,
                'color' => GlenmoreParameters::COLOR_BROWN]);
        $this->connection->insert('resource_glm',
            ['id' => 5, 'help_id' => null, 'type' => GlenmoreParameters::PRODUCTION_RESOURCE,
                'color' => GlenmoreParameters::COLOR_YELLOW]);
        $this->connection->insert('resource_glm',
            ['id' => 6, 'help_id' => null, 'type' => GlenmoreParameters::WHISKY_RESOURCE,
                'color' => GlenmoreParameters::COLOR_YELLOW]);
        $this->connection->insert('resource_glm',
            ['id' => 7, 'help_id' => null, 'type' => GlenmoreParameters::HAT_RESOURCE,
                'color' => GlenmoreParameters::COLOR_BROWN]);
        $this->connection->insert('resource_glm',
            ['id' => 8, 'help_id' => null, 'type' => GlenmoreParameters::MOVEMENT_RESOURCE,
                'color' => GlenmoreParameters::COLOR_WHITE]);
        $this->connection->insert('resource_glm',
            ['id' => 9, 'help_id' => null, 'type' => GlenmoreParameters::VILLAGER_RESOURCE,
                'color' => GlenmoreParameters::COLOR_BLACK]);
        $this->connection->insert('resource_glm',
            ['id' => 10, 'help_id' => null, 'type' => GlenmoreParameters::POINT_RESOURCE,
                'color' => GlenmoreParameters::COLOR_GREEN]);

        // Insertion of cards

        // Insertion of card 1

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 1, 'help_id' => null, 'amount' => 1, 'resource_id' => 7]
        );


        $this->connection->insert('card_glm',
            ['id' => 1, 'help_id' => null, 'bonus_id' => 1,
                'value' => null, 'name' => GlenmoreParameters::CARD_CASTLE_OF_MEY]
        );

        // Insertion of card 2

        $this->connection->insert('card_glm',
            ['id' => 2, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::CARD_LOCH_LOCHY]
        );

        // Insertion of card 3

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 3, 'help_id' => null, 'amount' => 1, 'resource_id' => 7]
        );


        $this->connection->insert('card_glm',
            ['id' => 3, 'help_id' => null, 'bonus_id' => 3,
                'value' => null, 'name' => GlenmoreParameters::CARD_CASTLE_STALKER]
        );

        // Insertion of card 4

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 2, 'help_id' => null, 'amount' => 3, 'resource_id' => 7]
        );

        $this->connection->insert('card_glm',
            ['id' => 4, 'help_id' => null, 'bonus_id' => 2,
                'value' => null, 'name' => GlenmoreParameters::CARD_CAWDOR_CASTLE]
        );

        // Insertion of card 5

        $this->connection->insert('card_glm',
            ['id' => 5, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::CARD_LOCH_MORAR]
        );

        // Insertion of card 6

        $this->connection->insert('card_glm',
            ['id' => 6, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::CARD_LOCH_NESS]
        );

        // Insertion of card 7

        $this->connection->insert('card_glm',
            ['id' => 7, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::CARD_IONA_ABBEY]
        );

        // Insertion of card 8

        $this->connection->insert('card_glm',
            ['id' => 8, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::CARD_DUART_CASTLE]
        );

        // Insertion of card 9

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 4, 'help_id' => null, 'amount' => 1, 'resource_id' => 7]
        );

        $this->connection->insert('card_glm',
            ['id' => 9, 'help_id' => null, 'bonus_id' => 4,
                'value' => null, 'name' => GlenmoreParameters::CARD_CASTLE_MOIL]
        );

        // Insertion of card 10

        $this->connection->insert('card_glm',
            ['id' => 10, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::CARD_LOCH_SHIEL]
        );

        // Insertion of card 11

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 5, 'help_id' => null, 'amount' => 1, 'resource_id' => 7]
        );

        $this->connection->insert('card_glm',
            ['id' => 11, 'help_id' => null, 'bonus_id' => 5,
                'value' => null, 'name' => GlenmoreParameters::CARD_DONAN_CASTLE]
        );

        // Insertion of card 12

        $this->connection->insert('card_glm',
            ['id' => 12, 'help_id' => null, 'bonus_id' => null,
                'value' => null, 'name' => GlenmoreParameters::CARD_LOCH_OICH]
        );

        // Insertion of card 13

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 6, 'help_id' => null, 'amount' => 1, 'resource_id' => 7]
        );

        $this->connection->insert('card_glm',
            ['id' => 13, 'help_id' => null, 'bonus_id' => 6,
                'value' => null, 'name' => GlenmoreParameters::CARD_ARMADALE_CASTLE]
        );

        // Insertion of tiles

        // Insertion of tiles level 0

        // Insertion of tile 1

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 1, "help_id" => null, 'amount' => 1, 'resource_id' => 2]
        );

        $this->connection->insert('tile_glm',
            ['id' => 1, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_QUARRY, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 1, 'tile_activation_bonus_glm_id' => 1]);

        // Insertion of tile 2

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 2, "help_id" => null, 'amount' => 1, 'resource_id' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 2, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_FOREST, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 2, 'tile_activation_bonus_glm_id' => 2]);

        // Insertion of tile 3

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 3, "help_id" => null, 'amount' => 1, 'resource_id' => 3]
        );

        $this->connection->insert('tile_glm',
            ['id' => 3, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_PASTURE, 'containing_river' => 1,
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
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 4, 'tile_activation_bonus_glm_id' => 4]);

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 4, 'tile_buy_bonus_glm_id' => 7]);

        // Insertion of tile 5

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 5, "help_id" => null, 'amount' => 1, 'resource_id' => 5]
        );

        $this->connection->insert('tile_glm',
            ['id' => 5, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_FIELD, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 5, 'tile_activation_bonus_glm_id' => 5]);

        // Insertion of tile 6


        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 6, "help_id" => null, 'amount' => 1, 'resource_id' => 4]
        );

        $this->connection->insert('tile_glm',
            ['id' => 6, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_CATTLE, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 6, 'tile_activation_bonus_glm_id' => 6]);

        // Insertion of tile 7

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 7, "help_id" => null, 'amount' => 1, 'resource_id' => 2]
        );

        $this->connection->insert('tile_glm',
            ['id' => 7, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_QUARRY, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 7, 'tile_activation_bonus_glm_id' => 7]);

        // Insertion of tile 8

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 8, "help_id" => null, 'amount' => 1, 'resource_id' => 1]
        );


        $this->connection->insert('tile_glm',
            ['id' => 8, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_FOREST, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 0]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 8, 'tile_activation_bonus_glm_id' => 8]);

        // Insertion of tiles level 1

        // Insertion of tile 9

        $this->connection->insert('tile_glm',
            ['id' => 9, 'help_id' => null,
                'card_id' => 2, 'type' => GlenmoreParameters::TILE_TYPE_BLUE,
                'name' => GlenmoreParameters::CARD_LOCH_LOCHY, 'containing_river' => 1,
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
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_BUTCHER, 'containing_river' => 0,
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
            ['id' => 1, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 11, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_DISTILLERY, 'containing_river' => 0,
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
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_PASTURE, 'containing_river' => 0,
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
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_FAIR, 'containing_river' => 0,
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
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 1,
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
            ['id' => 4, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 15, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_DISTILLERY, 'containing_river' => 0,
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
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 0,
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
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_FAIR, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 1]);

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
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_FIELD, 'containing_river' => 0,
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
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 0,
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
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 1,
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
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_CATTLE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 21, 'tile_activation_bonus_glm_id' => 25]
        );

        // Insertion of tile 22

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 26, 'help_id' => null, 'resource_id' => 5, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 22, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_FIELD, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 22, 'tile_activation_bonus_glm_id' => 26]
        );

        // Insertion of tile 23

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 27, 'help_id' => null, 'resource_id' => 2, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 23, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_QUARRY, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 23, 'tile_activation_bonus_glm_id' => 27]
        );

        // Insertion of tile 24

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 28, 'help_id' => null, 'resource_id' => 1, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 24, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_FOREST, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 24, 'tile_activation_bonus_glm_id' => 28]
        );

        // Insertion of tile 25

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 29, 'help_id' => null, 'resource_id' => 2, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 25, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_QUARRY, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 25, 'tile_activation_bonus_glm_id' => 29]
        );

        // Insertion of tile 26

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 9, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 10, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 11, 'help_id' => null, 'resource_id' => 5, 'price' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 30, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 14, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 26, 'help_id' => null,
                'card_id' => 9, 'type' => GlenmoreParameters::TILE_TYPE_CASTLE,
                'name' => GlenmoreParameters::CARD_CASTLE_MOIL, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 26, 'tile_buy_cost_glm_id' => 9]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 26, 'tile_buy_cost_glm_id' => 10]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 26, 'tile_buy_cost_glm_id' => 11]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 26, 'tile_activation_bonus_glm_id' => 30]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 26, 'tile_buy_bonus_glm_id' => 14]
        );

        // Insertion of tile 27

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 12, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 13, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );


        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 31, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 15, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 27, 'help_id' => null,
                'card_id' => 3, 'type' => GlenmoreParameters::TILE_TYPE_CASTLE,
                'name' => GlenmoreParameters::CARD_CASTLE_STALKER, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 27, 'tile_buy_cost_glm_id' => 12]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 27, 'tile_buy_cost_glm_id' => 13]
        );


        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 27, 'tile_activation_bonus_glm_id' => 31]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 27, 'tile_buy_bonus_glm_id' => 15]
        );

        // Insertion of tile 28

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 32, 'help_id' => null, 'resource_id' => 1, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 28, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_FOREST, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 28, 'tile_activation_bonus_glm_id' => 32]
        );

        // Insertion of tile 29

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 14, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 16, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 33, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 29, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 29, 'tile_buy_cost_glm_id' => 14]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 29, 'tile_buy_bonus_glm_id' => 16]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 29, 'tile_activation_bonus_glm_id' => 33]
        );

        // Insertion of tile 30

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 15, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 16, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );


        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 34, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 17, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 30, 'help_id' => null,
                'card_id' => 13, 'type' => GlenmoreParameters::TILE_TYPE_CASTLE,
                'name' => GlenmoreParameters::CARD_ARMADALE_CASTLE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 30, 'tile_buy_cost_glm_id' => 15]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 30, 'tile_buy_cost_glm_id' => 16]
        );


        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 30, 'tile_activation_bonus_glm_id' => 34]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 30, 'tile_buy_bonus_glm_id' => 17]
        );

        // Insertion of tile 31

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 11, "help_id" => null, 'price' => 1, 'resource_id' => 4]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 12, "help_id" => null, 'price' => 2, 'resource_id' => 4]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 35, "help_id" => null, 'amount' => 2, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 36, "help_id" => null, 'amount' => 4, 'resource_id' => 10]
        );

        $this->connection->insert('tile_glm',
            ['id' => 31, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_BUTCHER, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 1]);

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 31, 'tile_activation_cost_glm_id' => 11]
        );
        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 31, 'tile_activation_cost_glm_id' => 12]
        );
        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 31, 'tile_activation_bonus_glm_id' => 35]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 31, 'tile_activation_bonus_glm_id' => 36]
        );

        // Insertion of tiles of level 2

        // Insertion of tile 32

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 37, 'help_id' => null, 'resource_id' => 2, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 32, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_QUARRY, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 32, 'tile_activation_bonus_glm_id' => 37]
        );

        // Insertion of tile 33

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 38, 'help_id' => null, 'resource_id' => 10, 'amount' => 3]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 17, 'help_id' => null, 'price' => 1, 'resource_id' => 6]
        );

        $this->connection->insert('tile_glm',
            ['id' => 33, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_TAVERN, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 33, 'tile_activation_bonus_glm_id' => 38]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 33, 'tile_buy_cost_glm_id' => 17]
        );

        // Insertion of tile 34

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 49, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 39, 'help_id' => null, 'resource_id' => 10, 'amount' => 5]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 13, 'help_id' => null, 'price' => 1, 'resource_id' => 3]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 14, 'help_id' => null, 'price' => 1, 'resource_id' => 4]
        );

        $this->connection->insert('tile_glm',
            ['id' => 34, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_BUTCHER, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 34, 'tile_activation_bonus_glm_id' => 39]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 34, 'tile_activation_cost_glm_id' => 13]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 34, 'tile_activation_cost_glm_id' => 14]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 34, 'tile_buy_cost_glm_id' => 49]
        );

        // Insertion of tile 35

        $this->connection->insert('tile_glm',
            ['id' => 35, 'help_id' => null,
                'card_id' => 5, 'type' => GlenmoreParameters::TILE_TYPE_BLUE,
                'name' => GlenmoreParameters::CARD_LOCH_MORAR, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 2]);

        // Insertion of tile 36

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 15, "help_id" => null, 'price' => 1, 'resource_id' => 5]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 40, "help_id" => null, 'amount' => 1, 'resource_id' => 6]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 18, 'help_id' => null, 'resource_id' => 6, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 18, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 36, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_DISTILLERY, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 36, 'tile_buy_cost_glm_id' => 18]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 36, 'tile_buy_bonus_glm_id' => 18]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 36, 'tile_activation_cost_glm_id' => 15]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 36, 'tile_activation_bonus_glm_id' => 40]
        );

        // Insertion of tile 37

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 19, 'help_id' => null, 'resource_id' => 9, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 37, 'help_id' => null,
                'card_id' => 6, 'type' => GlenmoreParameters::TILE_TYPE_BLUE,
                'name' => GlenmoreParameters::CARD_LOCH_NESS, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 37, 'tile_buy_cost_glm_id' => 19]
        );

        // Insertion of tile 38

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 20, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 21, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );


        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 41, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 22, 'help_id' => null, 'resource_id' => 4, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 19, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 38, 'help_id' => null,
                'card_id' => 1, 'type' => GlenmoreParameters::TILE_TYPE_CASTLE,
                'name' => GlenmoreParameters::CARD_CASTLE_OF_MEY, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 38, 'tile_buy_cost_glm_id' => 20]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 38, 'tile_buy_cost_glm_id' => 21]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 38, 'tile_buy_cost_glm_id' => 22]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 38, 'tile_activation_bonus_glm_id' => 41]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 38, 'tile_buy_bonus_glm_id' => 19]
        );

        // Insertion of tile 39

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 42, 'help_id' => null, 'resource_id' => 4, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 39, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_CATTLE, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 39, 'tile_activation_bonus_glm_id' => 42]
        );

        // Insertion of tile 40

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 43, 'help_id' => null, 'resource_id' => 1, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 40, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_FOREST, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 40, 'tile_activation_bonus_glm_id' => 43]
        );

        // Insertion of tile 41

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 44, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 23, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 20, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 41, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 41, 'tile_activation_bonus_glm_id' => 44]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 41, 'tile_buy_bonus_glm_id' => 20]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 41, 'tile_buy_cost_glm_id' => 23]
        );

        // Insertion of tile 42

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 45, 'help_id' => null, 'resource_id' => 10, 'amount' => 3]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 24, 'help_id' => null, 'price' => 1, 'resource_id' => 6]
        );

        $this->connection->insert('tile_glm',
            ['id' => 42, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_TAVERN, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 42, 'tile_activation_bonus_glm_id' => 45]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 42, 'tile_buy_cost_glm_id' => 24]
        );

        // Insertion of tile 43

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 25, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 26, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 27, 'help_id' => null, 'resource_id' => 3, 'price' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 46, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 21, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 43, 'help_id' => null,
                'card_id' => 8, 'type' => GlenmoreParameters::TILE_TYPE_CASTLE,
                'name' => GlenmoreParameters::CARD_DUART_CASTLE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 43, 'tile_buy_cost_glm_id' => 25]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 43, 'tile_buy_cost_glm_id' => 26]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 43, 'tile_buy_cost_glm_id' => 27]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 43, 'tile_activation_bonus_glm_id' => 46]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 43, 'tile_buy_bonus_glm_id' => 21]
        );

        // Insertion of tile 44

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 47, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 28, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 22, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 44, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 44, 'tile_activation_bonus_glm_id' => 47]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 44, 'tile_buy_bonus_glm_id' => 22]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 44, 'tile_buy_cost_glm_id' => 28]
        );

        // Insertion of tile 45

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 48, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 29, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 23, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 45, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 45, 'tile_activation_bonus_glm_id' => 48]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 45, 'tile_buy_bonus_glm_id' => 23]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 45, 'tile_buy_cost_glm_id' => 29]
        );

        // Insertion of tile 46

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 49, 'help_id' => null, 'resource_id' => 5, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 46, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_FIELD, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 46, 'tile_activation_bonus_glm_id' => 49]
        );

        // Insertion of tile 47

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 50, 'help_id' => null, 'resource_id' => 5, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 47, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_FIELD, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 47, 'tile_activation_bonus_glm_id' => 50]
        );

        // Insertion of tile 48

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 30, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 31, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 32, 'help_id' => null, 'resource_id' => 3, 'price' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 51, 'help_id' => null, 'resource_id' => null, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 48, 'help_id' => null,
                'card_id' => 7, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::CARD_IONA_ABBEY, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 48, 'tile_buy_cost_glm_id' => 30]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 48, 'tile_buy_cost_glm_id' => 31]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 48, 'tile_buy_cost_glm_id' => 32]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 48, 'tile_activation_bonus_glm_id' => 51]
        );

       // Insertion of tile 49

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 52, "help_id" => null, 'amount' => 1, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 53, "help_id" => null, 'amount' => 3, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 54, "help_id" => null, 'amount' => 5, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 55, "help_id" => null, 'amount' => 8, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 16, "help_id" => null, 'price' => 1, 'resource_id' => null]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 17, "help_id" => null, 'price' => 2, 'resource_id' => null]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 18, "help_id" => null, 'price' => 3, 'resource_id' => null]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 19, "help_id" => null, 'price' => 4, 'resource_id' => null]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 33, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 49, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_FAIR, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 49, 'tile_activation_bonus_glm_id' => 52]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 49, 'tile_activation_bonus_glm_id' => 53]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 49, 'tile_activation_bonus_glm_id' => 54]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 49, 'tile_activation_bonus_glm_id' => 55]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 49, 'tile_activation_cost_glm_id' => 16]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 49, 'tile_activation_cost_glm_id' => 17]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 49, 'tile_activation_cost_glm_id' => 18]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 49, 'tile_activation_cost_glm_id' => 19]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 49, 'tile_buy_cost_glm_id' => 33]
        );

        // Insertion of tile 50

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 56, 'help_id' => null, 'resource_id' => 3, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 50, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_PASTURE, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 2]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 50, 'tile_activation_bonus_glm_id' => 56]
        );

        // Insertion of tiles of level 3

        // Insertion of tile 51

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 34, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 35, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 36, 'help_id' => null, 'resource_id' => 4, 'price' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 57, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 24, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 51, 'help_id' => null,
                'card_id' => 4, 'type' => GlenmoreParameters::TILE_TYPE_CASTLE,
                'name' => GlenmoreParameters::CARD_CAWDOR_CASTLE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 51, 'tile_buy_cost_glm_id' => 34]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 51, 'tile_buy_cost_glm_id' => 35]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 51, 'tile_buy_cost_glm_id' => 36]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 51, 'tile_activation_bonus_glm_id' => 57]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 51, 'tile_buy_bonus_glm_id' => 24]
        );

        // Insertion of tile 52

        $this->connection->insert('tile_glm',
            ['id' => 52, 'help_id' => null,
                'card_id' => 10, 'type' => GlenmoreParameters::TILE_TYPE_CASTLE,
                'name' => GlenmoreParameters::CARD_LOCH_SHIEL, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 3]);

        // Insertion of tile 53

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 58, 'help_id' => null, 'resource_id' => 1, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 53, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_FOREST, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 53, 'tile_activation_bonus_glm_id' => 58]
        );

        // Insertion of tile 54

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 59, 'help_id' => null, 'resource_id' => 5, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 54, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_FOREST, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 54, 'tile_activation_bonus_glm_id' => 59]
        );

        // Insertion of tile 55

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 60, "help_id" => null, 'amount' => 1, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 61, "help_id" => null, 'amount' => 3, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 62, "help_id" => null, 'amount' => 5, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 63, "help_id" => null, 'amount' => 8, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 64, "help_id" => null, 'amount' => 12, 'resource_id' => 10]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 20, "help_id" => null, 'price' => 1, 'resource_id' => null]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 21, "help_id" => null, 'price' => 2, 'resource_id' => null]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 22, "help_id" => null, 'price' => 3, 'resource_id' => null]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 23, "help_id" => null, 'price' => 4, 'resource_id' => null]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 24, "help_id" => null, 'price' => 5, 'resource_id' => null]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 37, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 55, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_FAIR, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 55, 'tile_activation_bonus_glm_id' => 60]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 55, 'tile_activation_bonus_glm_id' => 61]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 55, 'tile_activation_bonus_glm_id' => 62]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 55, 'tile_activation_bonus_glm_id' => 63]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 55, 'tile_activation_bonus_glm_id' => 64]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 55, 'tile_activation_cost_glm_id' => 20]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 55, 'tile_activation_cost_glm_id' => 21]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 55, 'tile_activation_cost_glm_id' => 22]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 55, 'tile_activation_cost_glm_id' => 23]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 55, 'tile_activation_cost_glm_id' => 24]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 55, 'tile_buy_cost_glm_id' => 37]
        );

        // Insertion of tile 56

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 25, "help_id" => null, 'price' => 3, 'resource_id' => null]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 38, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 65, "help_id" => null, 'amount' => 8, 'resource_id' => 10]
        );

        $this->connection->insert('tile_glm',
            ['id' => 56, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_GROCER, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 56, 'tile_buy_cost_glm_id' => 38]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 56, 'tile_activation_bonus_glm_id' => 65]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 56, 'tile_activation_cost_glm_id' => 25]
        );

        // Insertion of tile 57

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 66, "help_id" => null, 'amount' => 1, 'resource_id' => 3]
        );

        $this->connection->insert('tile_glm',
            ['id' => 57, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_PASTURE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 57, 'tile_activation_bonus_glm_id' => 66]
        );

        // Insertion of tile 58

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 67, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 39, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 25, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 58, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 58, 'tile_activation_bonus_glm_id' => 67]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 58, 'tile_buy_bonus_glm_id' => 25]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 58, 'tile_buy_cost_glm_id' => 39]
        );

        // Insertion of tile 59

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 26, "help_id" => null, 'price' => 1, 'resource_id' => 5]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 68, "help_id" => null, 'amount' => 1, 'resource_id' => 6]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 26, 'help_id' => null, 'resource_id' => 6, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 40, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 59, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_DISTILLERY, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 59, 'tile_buy_cost_glm_id' => 40]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 59, 'tile_buy_bonus_glm_id' => 26]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 59, 'tile_activation_cost_glm_id' => 26]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 59, 'tile_activation_bonus_glm_id' => 68]
        );

        // Insertion of tile 60

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 42, 'help_id' => null, 'resource_id' => null, 'price' => 2]
        );

        $this->connection->insert('tile_glm',
            ['id' => 60, 'help_id' => null,
                'card_id' => 12, 'type' => GlenmoreParameters::TILE_TYPE_BLUE,
                'name' => GlenmoreParameters::CARD_LOCH_OICH, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 60, 'tile_buy_cost_glm_id' => 42]
        );

        // Insertion of tile 61

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 69, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 43, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 27, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 61, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 61, 'tile_activation_bonus_glm_id' => 69]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 61, 'tile_buy_bonus_glm_id' => 27]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 61, 'tile_buy_cost_glm_id' => 43]
        );

        // Insertion of tile 62

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 70, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 44, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 28, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 62, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                'name' => GlenmoreParameters::TILE_NAME_VILLAGE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 62, 'tile_activation_bonus_glm_id' => 70]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 62, 'tile_buy_bonus_glm_id' => 28]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 62, 'tile_buy_cost_glm_id' => 44]
        );

        // Insertion of tile 63

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 71, 'help_id' => null, 'resource_id' => 10, 'amount' => 4]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 45, 'help_id' => null, 'price' => 1, 'resource_id' => 6]
        );

        $this->connection->insert('tile_glm',
            ['id' => 63, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_TAVERN, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 63, 'tile_activation_bonus_glm_id' => 71]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 63, 'tile_buy_cost_glm_id' => 45]
        );

        // Insertion of tile 64

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 46, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 47, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_buy_cost_glm',
            ['id' => 48, 'help_id' => null, 'resource_id' => 5, 'price' => 1]
        );

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 72, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
        );

        $this->connection->insert('tile_buy_bonus_glm',
            ['id' => 29, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 64, 'help_id' => null,
                'card_id' => 11, 'type' => GlenmoreParameters::TILE_TYPE_CASTLE,
                'name' => GlenmoreParameters::CARD_DONAN_CASTLE, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 64, 'tile_buy_cost_glm_id' => 46]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 64, 'tile_buy_cost_glm_id' => 47]
        );

        $this->connection->insert('tile_glm_tile_buy_cost_glm',
            ['tile_glm_id' => 64, 'tile_buy_cost_glm_id' => 48]
        );

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 64, 'tile_activation_bonus_glm_id' => 72]
        );

        $this->connection->insert('tile_glm_tile_buy_bonus_glm',
            ['tile_glm_id' => 64, 'tile_buy_bonus_glm_id' => 29]
        );

        // Insertion of tile 65

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 73, 'help_id' => null, 'resource_id' => 4, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 65, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_GREEN,
                'name' => GlenmoreParameters::TILE_NAME_CATTLE, 'containing_river' => 0,
                'containing_road' => 0, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 65, 'tile_activation_bonus_glm_id' => 73]
        );

        // Insertion of tile 66


        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 74, 'help_id' => null, 'resource_id' => 10, 'amount' => 7]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 27, 'help_id' => null, 'resource_id' => 1, 'price' => 1]
        );

        $this->connection->insert('tile_activation_cost_glm',
            ['id' => 28, 'help_id' => null, 'resource_id' => 2, 'price' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 66, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_BROWN,
                'name' => GlenmoreParameters::TILE_NAME_BRIDGE, 'containing_river' => 1,
                'containing_road' => 0, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 66, 'tile_activation_bonus_glm_id' => 74]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 66, 'tile_activation_cost_glm_id' => 27]
        );

        $this->connection->insert('tile_glm_tile_activation_cost_glm',
            ['tile_glm_id' => 66, 'tile_activation_cost_glm_id' => 28]
        );

        // Insertion of tile 67

        $this->connection->insert('tile_activation_bonus_glm',
            ['id' => 75, 'help_id' => null, 'resource_id' => 2, 'amount' => 1]
        );

        $this->connection->insert('tile_glm',
            ['id' => 67, 'help_id' => null,
                'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_YELLOW,
                'name' => GlenmoreParameters::TILE_NAME_QUARRY, 'containing_river' => 0,
                'containing_road' => 1, 'level' => 3]);

        $this->connection->insert('tile_glm_tile_activation_bonus_glm',
            ['tile_glm_id' => 67, 'tile_activation_bonus_glm_id' => 75]
        );

        // Insertion of starting villages

        $activationBonus = 76;
        $buyBonus = 30;
        for ($i = 68; $i <= 72; ++$i) {

            $this->connection->insert('tile_activation_bonus_glm',
                ['id' => $activationBonus, 'help_id' => null, 'resource_id' => 8, 'amount' => 1]
            );

            $this->connection->insert('tile_buy_bonus_glm',
                ['id' => $buyBonus, 'help_id' => null, 'resource_id' => 9, 'amount' => 1]
            );

            $this->connection->insert('tile_glm',
                ['id' => $i, 'help_id' => null,
                    'card_id' => null, 'type' => GlenmoreParameters::TILE_TYPE_VILLAGE,
                    'name' => GlenmoreParameters::TILE_NAME_START_VILLAGE, 'containing_river' => 1,
                    'containing_road' => 1, 'level' => -1]);

            $this->connection->insert('tile_glm_tile_activation_bonus_glm',
                ['tile_glm_id' => $i, 'tile_activation_bonus_glm_id' => $activationBonus]
            );

            $this->connection->insert('tile_glm_tile_buy_bonus_glm',
                ['tile_glm_id' => $i, 'tile_buy_bonus_glm_id' => $buyBonus]
            );
            ++$activationBonus;
            ++$buyBonus;
        }
    }





    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

}