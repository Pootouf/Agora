<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Game\Myrmes\MyrmesParameters;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class VersionMYRMES_DATA extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        //TILES

        // WATER TILES

        $id = 1;
        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 3,
                'coord_y' => 12,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 3,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 11,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 13,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 21,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 8,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 16,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 7,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 17,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 7,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 17,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 8,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 16,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 3,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 11,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 13,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 21,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 11,
                'coord_y' => 12,
                'type' => MyrmesParameters::$WATER_TILE_TYPE]);
        $id ++;






        //DIRT_TILES

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 1,
                'coord_y' => 8,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 1,
                'coord_y' => 16,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 2,
                'coord_y' => 5,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 2,
                'coord_y' => 7,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 2,
                'coord_y' => 17,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 2,
                'coord_y' => 19,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 7,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 17,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 0,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 2,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 22,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 24,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 2,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 22,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 1,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 23,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 1,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 7,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 17,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 23,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 12,
                'coord_y' => 5,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 12,
                'coord_y' => 7,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 12,
                'coord_y' => 17,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 12,
                'coord_y' => 19,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 13,
                'coord_y' => 8,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 13,
                'coord_y' => 16,
                'type' => MyrmesParameters::$DIRT_TILE_TYPE]);
        $id ++;



        //MUSHROOM_TYPE

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 0,
                'coord_y' => 9,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 0,
                'coord_y' => 15,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 2,
                'coord_y' => 11,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 2,
                'coord_y' => 13,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 3,
                'coord_y' => 4,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 3,
                'coord_y' => 20,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 5,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 19,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 4,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 10,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 14,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 20,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 4,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 8,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 12,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 16,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 20,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 0,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 4,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 10,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 14,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 20,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 24,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 5,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 19,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 11,
                'coord_y' => 4,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 11,
                'coord_y' => 20,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 12,
                'coord_y' => 11,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 12,
                'coord_y' => 13,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 13,
                'coord_y' => 10,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 13,
                'coord_y' => 14,
                'type' => MyrmesParameters::$MUSHROOM_TILE_TYPE]);
        $id ++;



        //STONE_TYPE

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 1,
                'coord_y' => 12,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 3,
                'coord_y' => 2,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 3,
                'coord_y' => 8,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 3,
                'coord_y' => 16,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 3,
                'coord_y' => 22,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 6,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 18,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 1,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 23,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 6,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 18,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 11,
                'coord_y' => 2,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 11,
                'coord_y' => 8,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 11,
                'coord_y' => 16,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 11,
                'coord_y' => 22,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 13,
                'coord_y' => 12,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 14,
                'coord_y' => 9,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 14,
                'coord_y' => 15,
                'type' => MyrmesParameters::$STONE_TILE_TYPE]);
        $id ++;




        //GRASS_TILE

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 0,
                'coord_y' => 11,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 0,
                'coord_y' => 13,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 1,
                'coord_y' => 6,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 1,
                'coord_y' => 10,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 1,
                'coord_y' => 14,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 1,
                'coord_y' => 18,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 2,
                'coord_y' => 3,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 2,
                'coord_y' => 9,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 2,
                'coord_y' => 15,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 2,
                'coord_y' => 21,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 3,
                'coord_y' => 6,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 3,
                'coord_y' => 10,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 3,
                'coord_y' => 14,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 3,
                'coord_y' => 18,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 1,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 9,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 15,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 4,
                'coord_y' => 23,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 5,
                'coord_y' => 12,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 3,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 5,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 9,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 11,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 13,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 15,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 19,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 6,
                'coord_y' => 21,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 0,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 6,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 10,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 14,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 18,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 7,
                'coord_y' => 24,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 3,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 5,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 9,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 11,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 13,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 15,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 19,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 8,
                'coord_y' => 21,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 2,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 12,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 9,
                'coord_y' => 22,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 9,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 10,
                'coord_y' => 15,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 11,
                'coord_y' => 6,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 11,
                'coord_y' => 10,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 11,
                'coord_y' => 14,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 11,
                'coord_y' => 18,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 12,
                'coord_y' => 3,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 12,
                'coord_y' => 9,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 12,
                'coord_y' => 15,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 12,
                'coord_y' => 21,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 13,
                'coord_y' => 6,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 13,
                'coord_y' => 18,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 14,
                'coord_y' => 11,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);
        $id ++;

        $this->connection->insert('tile_myr',
            ['id' => $id, 'help_id' => null,
                'coord_x' => 14,
                'coord_y' => 13,
                'type' => MyrmesParameters::$GRASS_TILE_TYPE]);


        // INSERTION OF PHEROMONES

        $type = MyrmesParameters::$PHEROMONE_TYPE_ZERO;
        for ($i = 0; $i < 6; ++$i) {
            $this->connection->insert('tile_type_myr',
                ['help_id' => null, 'orientation' => $i, 'type' => $type]
            );
        }
        $type = MyrmesParameters::$PHEROMONE_TYPE_ONE;
        for ($i = 0; $i < 3; ++$i) {
            $this->connection->insert('tile_type_myr',
                ['help_id' => null, 'orientation' => $i, 'type' => $type]
            );
        }
        $type = MyrmesParameters::$PHEROMONE_TYPE_TWO;
        for ($i = 0; $i < 6; ++$i) {
            $this->connection->insert('tile_type_myr',
                ['help_id' => null, 'orientation' => $i, 'type' => $type]
            );
        }
        $type = MyrmesParameters::$PHEROMONE_TYPE_THREE;
        for ($i = 0; $i < 6; ++$i) {
            $this->connection->insert('tile_type_myr',
                ['help_id' => null, 'orientation' => $i, 'type' => $type]
            );
        }
        $type = MyrmesParameters::$PHEROMONE_TYPE_FOUR;
        for ($i = 0; $i < 12; ++$i) {
            $this->connection->insert('tile_type_myr',
                ['help_id' => null, 'orientation' => $i, 'type' => $type]
            );
        }
        $type = MyrmesParameters::$PHEROMONE_TYPE_FIVE;
        for ($i = 0; $i < 6; ++$i) {
            $this->connection->insert('tile_type_myr',
                ['help_id' => null, 'orientation' => $i, 'type' => $type]
            );
        }
        $type = MyrmesParameters::$PHEROMONE_TYPE_SIX;
        for ($i = 0; $i < 6; ++$i) {
            $this->connection->insert('tile_type_myr',
                ['help_id' => null, 'orientation' => $i, 'type' => $type]
            );
        }

        $type = MyrmesParameters::$SPECIAL_TILE_TYPE_FARM;
        for ($i = 0; $i < 6; ++$i) {
            $this->connection->insert('tile_type_myr',
                ['help_id' => null, 'orientation' => $i, 'type' => $type]
            );
        }

        $type = MyrmesParameters::$SPECIAL_TILE_TYPE_QUARRY;
        for ($i = 0; $i < 6; ++$i) {
            $this->connection->insert('tile_type_myr',
                ['help_id' => null, 'orientation' => $i, 'type' => $type]
            );
        }

        $type = MyrmesParameters::$SPECIAL_TILE_TYPE_SUBANTHILL;
        for ($i = 0; $i < 6; ++$i) {
            $this->connection->insert('tile_type_myr',
                ['help_id' => null, 'orientation' => $i, 'type' => $type]
            );
        }

        //INSERTION OF RESOURCES

        $this->connection->insert('resource_myr',
            ['help_id' => null, 'description' => MyrmesParameters::$RESOURCE_TYPE_DIRT]
        );

        $this->connection->insert('resource_myr',
            ['help_id' => null, 'description' => MyrmesParameters::$RESOURCE_TYPE_STONE]
        );

        $this->connection->insert('resource_myr',
            ['help_id' => null, 'description' => MyrmesParameters::$RESOURCE_TYPE_GRASS]
        );

        $this->connection->insert('resource_myr',
            ['help_id' => null, 'description' => MyrmesParameters::$RESOURCE_TYPE_LARVAE]
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}

