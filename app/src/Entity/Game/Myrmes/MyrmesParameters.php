<?php

namespace App\Entity\Game\Myrmes;

interface MyrmesParameters
{

    // GAME
    // MIN AND MAX NUMBER OF PLAYER

    const int MAX_NUMBER_OF_PLAYER = 4;
    const int MIN_NUMBER_OF_PLAYER = 2;


    // Player phases

    const int PHASE_INVALID = -1;
    const int PHASE_EVENT = 0;
    const int PHASE_BIRTH = 1;
    const int PHASE_WORKER = 2;
    const int PHASE_HARVEST = 3;
    const int PHASE_WORKSHOP = 4;
    const int PHASE_WINTER = 5;


    // Player color

    const string PLAYER_COLOR_BLUE = 'blue';
    const string PLAYER_COLOR_RED = 'red';
    const string PLAYER_COLOR_YELLOW = 'yellow';
    const string PLAYER_COLOR_GRAY = 'gray';

    const array PLAYERS_COLORS = [
        self::PLAYER_COLOR_BLUE,
        self::PLAYER_COLOR_YELLOW,
        self::PLAYER_COLOR_RED,
        self::PLAYER_COLOR_GRAY
    ];


    // Area's for nurses

    const int BASE_AREA = 0;
    const int LARVAE_AREA = 1;
    const int SOLDIERS_AREA = 2;
    const int WORKER_AREA = 3;
    const int WORKSHOP_AREA = 4;
    const int GOAL_AREA = 5;
    const int AREA_COUNT = 6;

    // Workshop areas

    const int WORKSHOP_GOAL_AREA = 5;
    const int WORKSHOP_ANTHILL_HOLE_AREA = 6;
    const int WORKSHOP_LEVEL_AREA = 7;
    const int WORKSHOP_NURSE_AREA = 8;


    //Nurses parameters

    const int START_NURSES_COUNT_PER_PLAYER = 3;
    const int MAX_NURSES_COUNT_PER_PLAYER = 8;


    // Win by area's nurses

    const array WIN_LARVAE_BY_NURSES_COUNT = array(
        1 => 1,
        2 => 3,
        3 => 5
    );

    const array WIN_SOLDIERS_BY_NURSES_COUNT = array(
        2 => 1,
        3 => 2
    );

    const array WIN_WORKERS_BY_NURSES_COUNT = array(
        2 => 1,
        4 => 2
    );

    // Number of nurses by area

    const array NUMBER_NURSE_IN_LARVAE_AREA = array(1, 1, 1);
    const array NUMBER_NURSE_IN_WORKSHOP_AREA = array(1, 1, 1, 1);
    const array NUMBER_NURSE_IN_SOLDIERS_AREA = array(2, 1);
    const array NUMBER_NURSE_IN_WORKER_AREA = array(2, 2);

    // Events bonus

    const int BONUS_LEVEL = 0;
    const int BONUS_POINT = 1;
    const int BONUS_LARVAE = 2;
    const int BONUS_HARVEST = 3;
    const int BONUS_MOVEMENT = 4;
    const int BONUS_WARRIOR = 5;
    const int BONUS_PHEROMONE = 6;
    const int BONUS_WORKER = 7;


    // Bonus harvested pheromone

    const int HARVESTED_TILE_BONUS = 3;


    // Years

    const int FIRST_YEAR_NUM = 1;
    const int SECOND_YEAR_NUM = 2;
    const int THIRD_YEAR_NUM = 3;


    // Seasons

    const string SPRING_SEASON_NAME = "spring";
    const string SUMMER_SEASON_NAME = "summer";
    const string WINTER_SEASON_NAME = "winter";
    const string FALL_SEASON_NAME = "fall";
    const string INVALID_SEASON_NAME = "invalid";

    // Warehouse available emplacements

    const int WAREHOUSE_LOCATIONS_AVAILABLE_ANTHILL_LEVEL_LESS_THAN_2 = 4;
    const int WAREHOUSE_LOCATIONS_AVAILABLE_ANTHILL_LEVEL_AT_LEAST_2 = 6;


    // TILE TYPES

    const string WATER_TILE_TYPE = "water";
    const string DIRT_TILE_TYPE = "dirt";
    const string MUSHROOM_TILE_TYPE = "mushroom";
    const string STONE_TILE_TYPE = "stone";
    const string GRASS_TILE_TYPE = "grass";


    // Buy for add anthill level

    const array BUY_RESOURCE_FOR_LEVEL_ONE = array(
        "grass" => 2
    );

    const array BUY_RESOURCE_FOR_LEVEL_TWO = array(
        "grass" => 2,
        "stone" => 1
    );

    const array BUY_RESOURCE_FOR_LEVEL_THREE = array(
        "stone" => 3
    );


    // Pheromone Level

    const int PHEROMONE_LEVEL_ZERO = 0;
    const int PHEROMONE_LEVEL_TWO = 2;
    const int PHEROMONE_LEVEL_FOUR = 4;
    const int PHEROMONE_LEVEL_SIX = 6;
    const int PHEROMONE_LEVEL_EIGHT = 8;


    // Pheromone Type

    const int PHEROMONE_TYPE_ZERO = 0;
    const int PHEROMONE_TYPE_ONE = 1;
    const int PHEROMONE_TYPE_TWO = 2;
    const int PHEROMONE_TYPE_THREE = 3;
    const int PHEROMONE_TYPE_FOUR = 4;
    const int PHEROMONE_TYPE_FIVE = 5;
    const int PHEROMONE_TYPE_SIX = 6;


    // Pheromone Amount

    const array PHEROMONE_TYPE_AMOUNT = [6, 2, 2, 2, 2, 2, 1];


    // Pheromone Type Level

    const array PHEROMONE_TYPE_LEVEL = [
        0 => 0,
        1 => 2,
        2 => 2,
        3 => 4,
        4 => 4,
        5 => 6,
        6 => 8
    ];

    // Pheromone Type Orientation

    const array PHEROMONE_TYPE_ORIENTATIONS = [
        0 => 6,
        1 => 3,
        2 => 6,
        3 => 6,
        4 => 12,
        5 => 6,
        6 => 6,
    ];

    // Special tiles

    // Special tiles Type

    const array SPECIAL_TILE_TYPES = [MyrmesParameters::SPECIAL_TILE_TYPE_FARM,
        MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY,
        MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL];

    const int SPECIAL_TILE_TYPE_FARM = 7;
    const int SPECIAL_TILE_TYPE_QUARRY = 8;
    const int SPECIAL_TILE_TYPE_SUBANTHILL = 9;

    // Special tiles amount
    const array SPECIAL_TILE_TYPE_AMOUNT = [
        7 => 4,
        8 => 4,
        9 => 8
    ];


    // Special tiles level
    const array SPECIAL_TILES_TYPE_LEVEL = [
        7 => 2,
        8 => 2,
        9 => 4
    ];


    // Special tiles Orientation

    const array SPECIAL_TILES_TYPE_ORIENTATIONS = [
        7 => 6,
        8 => 6,
        9 => 6,
    ];


    // Direction's tiles

    const int DIRECTION_NORTH_WEST = 1;
    const int DIRECTION_NORTH_EAST = 2;
    const int DIRECTION_EAST = 3;
    const int DIRECTION_SOUTH_EAST = 4;
    const int DIRECTION_SOUTH_WEST = 5;
    const int DIRECTION_WEST = 6;


    // Prey Types

    const string LADYBUG_TYPE = "ladybug";
    const string TERMITE_TYPE = "termite";
    const string SPIDER_TYPE = "spider";

    // Prey Numbers, sum must be equal to the size of PREY_POSITIONS
    const int LADYBUG_NUMBER = 6;
    const int TERMITE_NUMBER = 6;
    const int SPIDER_NUMBER = 6;

    // Prey Position
    const array PREY_POSITIONS = [[1, 6], [1, 18], [4, 9], [4, 15], [5, 12], [6, 9], [6, 15], [7, 0], [7, 6],
        [7, 18], [7, 24], [8, 9], [8, 15], [9, 12], [10, 9], [10, 15], [13, 6], [13, 18]];


    //Player start data
    const int NUMBER_OF_WORKER_AT_START = 2;
    const int NUMBER_OF_LARVAE_AT_START = 1;
    const int ANTHILL_START_LEVEL = 0;
    const int PLAYER_START_SCORE = 10;
    // Number of soldiers for attack preys

    const array NUMBER_SOLDIERS_FOR_ATTACK_PREY = array(
        "ladybug" => 1,
        "termite" => 1,
        "spider" => 2
    );

    // Food gain by attack preys

    const array FOOD_GAIN_BY_ATTACK_PREY = array(
        "ladybug" => 2,
        "termite" => 1,
        "spider" => 1
    );

    // Victory gain by attack preys

    const array VICTORY_GAIN_BY_ATTACK_PREY = array(
        "ladybug" => 0,
        "termite" => 2,
        "spider" => 4
    );



    //Anthill hole start position
    const array ANTHILL_HOLE_POSITION_BY_NUMBER_OF_PLAYER = [
        2 => [[11, 6], [11, 18]],
        3 => [[5, 4], [4, 19], [12, 13]],
        4 => [[3, 6], [3, 18], [11, 6], [11, 18]]
    ];


    // Max anthill holes number by player
    const int MAX_ANTHILL_HOLE_NB = 4;


    //Excluded tile with 2 players
    const array EXCLUDED_TILES_2_PLAYERS = [
        [7, 0], [8, 1], [9, 0], [9, 2], [10, 1], [10, 3], [11, 2], [11, 4], [12, 3], [12, 5], [13, 6],
        [7, 24], [8, 23], [9, 24], [9, 22], [10, 23], [10, 21], [11, 22], [11, 20], [12, 21], [12, 19], [13, 18]
    ];


    // Resources Type
    const string RESOURCE_TYPE_DIRT = "dirt";
    const string RESOURCE_TYPE_STONE = "stone";
    const string RESOURCE_TYPE_GRASS = "grass";
    const string RESOURCE_TYPE_LARVAE = "larvae";


    // Goal Difficulty
    const int GOAL_DIFFICULTY_LEVEL_ONE = 1;
    const int GOAL_DIFFICULTY_LEVEL_TWO = 2;
    const int GOAL_DIFFICULTY_LEVEL_THREE = 3;


    // Goal Points
    const int GOAL_REWARD_LEVEL_ONE = 6;
    const int GOAL_REWARD_LEVEL_TWO = 9;
    const int GOAL_REWARD_LEVEL_THREE = 12;
    const int GOAL_REWARD_WHEN_GOAL_ALREADY_DONE = 3;


    // Anthill Level
    const int ANTHILL_LEVEL_ONE = 1;
    const int ANTHILL_LEVEL_TWO = 2;
    const int ANTHILL_LEVEL_THREE = 3;

    // Goal names
    const string GOAL_RESOURCE_FOOD_NAME = "resource_food";
    const string GOAL_RESOURCE_STONE_NAME = "resource_stone";
    const string GOAL_RESOURCE_STONE_OR_DIRT_NAME = "resource_stone_dirt";
    const string GOAL_LARVAE_NAME = "larvae";
    const string GOAL_PREY_NAME = "prey";
    const string GOAL_SOLDIER_NAME = "soldier";
    const string GOAL_SPECIAL_TILE_NAME = "special_tile";
    const string GOAL_NURSES_NAME = "nurses";
    const string GOAL_ANTHILL_LEVEL_NAME = "anthill_level";
    const string GOAL_PHEROMONE_NAME = "pheromone";


    // Goal needed resources
    // Goal Food
    const int GOAL_NEEDED_RESOURCES_FOOD_LEVEL_ONE = 3;
    const int GOAL_NEEDED_RESOURCES_FOOD_LEVEL_TWO = 5;

    // Goal Stone
    const int GOAL_NEEDED_RESOURCES_STONE_LEVEL_TWO = 3;
    const int GOAL_NEEDED_RESOURCES_STONE_LEVEL_THREE = 5;

    // Goal Stone or dirt
    const int GOAL_NEEDED_RESOURCES_STONE_OR_DIRT_LEVEL_ONE = 2;
    const int GOAL_NEEDED_RESOURCES_STONE_OR_DIRT_LEVEL_THREE = 6;

    // Goal Soldier
    const int GOAL_NEEDED_RESOURCES_SOLDIER_LEVEL_ONE = 2;

    // Goal Nurse
    const int GOAL_NEEDED_RESOURCES_NEEDED_NURSE_LEVEL_TWO = 6;
    const int GOAL_NEEDED_RESOURCES_REMOVED_NURSE_LEVEL_TWO = 1;
    const int GOAL_NEEDED_RESOURCES_NEEDED_NURSE_LEVEL_THREE = 8;
    const int GOAL_NEEDED_RESOURCES_REMOVED_NURSE_LEVEL_THREE = 2;

    // Goal Larvae
    const int GOAL_NEEDED_RESOURCES_LARVAE_LEVEL_ONE = 5;
    const int GOAL_NEEDED_RESOURCES_LARVAE_LEVEL_TWO = 9;

    // Goal Prey
    const int GOAL_NEEDED_RESOURCES_PREY_LEVEL_ONE = 2;
    const int GOAL_NEEDED_RESOURCES_PREY_LEVEL_TWO = 3;
    const int GOAL_NEEDED_RESOURCES_PREY_LEVEL_THREE = 4;

    // Goal Anthill Level
    const int GOAL_NEEDED_RESOURCES_NEEDED_ANTHILL_LEVEL_LEVEL_TWO = 2;
    const int GOAL_NEEDED_RESOURCES_REMOVED_ANTHILL_LEVEL_LEVEL_TWO = 1;
    const int GOAL_NEEDED_RESOURCES_NEEDED_ANTHILL_LEVEL_LEVEL_THREE = 3;
    const int GOAL_NEEDED_RESOURCES_REMOVED_ANTHILL_LEVEL_LEVEL_THREE = 2;

    // Goal Special Tile
    const int GOAL_NEEDED_RESOURCES_NEEDED_SPECIAL_TILE_LEVEL_ONE = 3;
    const int GOAL_NEEDED_RESOURCES_REMOVED_SPECIAL_TILE_LEVEL_ONE = 1;
    const int GOAL_NEEDED_RESOURCES_NEEDED_SPECIAL_TILE_LEVEL_TWO = 3;
    const int GOAL_NEEDED_RESOURCES_REMOVED_SPECIAL_TILE_LEVEL_TWO = 2;

    // Goal Pheromone
    const int GOAL_NEEDED_RESOURCES_NEEDED_PHEROMONE_LEVEL_ONE = 4;
    const int GOAL_NEEDED_RESOURCES_NEEDED_PHEROMONE_LEVEL_THREE = 7;


    // Garden worker parameters
    const int DEFAULT_MOVEMENT_NUMBER = 3;


    // Anthill worker parameters
    const int NO_WORKFLOOR = -1;


    // Score parameters
    const array SCORE_INCREASE_GOAL_ALREADY_DONE = [
        2 => 5,
        3 => 4,
        4 => 3,
    ];
    const int SCORE_INCREASE_GOAL_DIFFICULTY_ONE = 6;
    const int SCORE_INCREASE_GOAL_DIFFICULTY_TWO = 9;
    const int SCORE_INCREASE_GOAL_DIFFICULTY_THREE = 12;
}