<?php

namespace App\Entity\Game\Myrmes;

class MyrmesParameters
{
    // GAME

    // MIN AND MAX NUMBER OF PLAYER

    public const int MAX_NUMBER_OF_PLAYER = 4;
    public const int MIN_NUMBER_OF_PLAYER = 2;

    // Player phases

    public const int PHASE_EVENT = 0;
    public const int PHASE_BIRTH = 1;
    public const int PHASE_WORKER = 2;
    public const int PHASE_HARVEST = 3;
    public const int PHASE_WORKSHOP = 4;

    // Player color

    public const array PLAYERS_COLORS = ['blue', 'yellow', 'red', 'gray'];

    // Area's for nurses

    public const int BASE_AREA = 0;
    public const int LARVAE_AREA = 1;
    public const int SOLDIERS_AREA = 2;
    public const int WORKER_AREA = 3;
    public const int WORKSHOP_AREA = 4;
    public const int AREA_COUNT = 5;

    // Workshop areas

    public const int WORKSHOP_GOAL_AREA = 1;
    public const int WORKSHOP_ANTHILL_HOLE_AREA = 2;
    public const int WORKSHOP_LEVEL_AREA = 3;
    public const int WORKSHOP_NURSE_AREA = 4;

    //Nurses parameters

    public const int START_NURSES_COUNT_PER_PLAYER = 3;
    public const int MAX_NURSES_COUNT_PER_PLAYER = 8;

    // Win by area's nurses

    public const array WIN_LARVAE_BY_NURSES_COUNT = array(
        1 => 1,
        2 => 3,
        3 => 5
    );

    public const array WIN_SOLDIERS_BY_NURSES_COUNT = array(
        2 => 1,
        3 => 2
    );

    public const array WIN_WORKERS_BY_NURSES_COUNT = array(
        2 => 1,
        4 => 2
    );

    // Events bonus

    public const int BONUS_LEVEL = 0;
    public const int BONUS_POINT = 1;
    public const  int BONUS_LARVAE = 2;
    public const int BONUS_HARVEST = 3;
    public const int BONUS_MOVEMENT = 4;
    public const int BONUS_WARRIOR = 5;
    public const int BONUS_PHEROMONE = 6;
    public const int BONUS_WORKER = 7;

    // Bonus harvested pheromone

    public const int HARVESTED_TILE_BONUS = 3;

    // Years

    public const int FIRST_YEAR_NUM = 1;
    public const int SECOND_YEAR_NUM = 2;
    public const int THIRD_YEAR_NUM = 3;

    // Seasons

    public const string SPRING_SEASON_NAME = "spring";
    public const string SUMMER_SEASON_NAME = "summer";
    public const string WINTER_SEASON_NAME = "winter";
    public const string FALL_SEASON_NAME = "fall";
    public const string INVALID_SEASON_NAME = "invalid";

    // TILE TYPES

    public const string WATER_TILE_TYPE = "water";
    public const string DIRT_TILE_TYPE = "dirt";
    public const string MUSHROOM_TILE_TYPE = "mushroom";
    public const string STONE_TILE_TYPE = "stone";
    public const string GRASS_TILE_TYPE = "grass";

    // Buy for add anthill level

    public const array BUY_RESOURCE_FOR_LEVEL_ONE = array(
        "grass" => 2
    );

    public const array BUY_RESOURCE_FOR_LEVEL_TWO = array(
        "grass" => 2,
        "stone" => 1
    );

    public const array BUY_RESOURCE_FOR_LEVEL_THREE = array(
        "stone" => 3
    );

    // Pheromone Level

    public const int PHEROMONE_LEVEL_ZERO = 0;
    public const int PHEROMONE_LEVEL_TWO = 2;
    public const int PHEROMONE_LEVEL_FOUR = 4;
    public const int PHEROMONE_LEVEL_SIX = 6;
    public const int PHEROMONE_LEVEL_EIGHT = 8;

    // Pheromone Type

    public const int PHEROMONE_TYPE_ZERO = 0;
    public const int PHEROMONE_TYPE_ONE = 1;
    public const int PHEROMONE_TYPE_TWO = 2;
    public const int PHEROMONE_TYPE_THREE = 3;
    public const int PHEROMONE_TYPE_FOUR = 4;
    public const int PHEROMONE_TYPE_FIVE = 5;
    public const int PHEROMONE_TYPE_SIX = 6;

    // Pheromone Amount

    public const array PHEROMONE_TYPE_AMOUNT = [6, 2, 2, 2, 2, 2, 1];

    // Pheromone Type Level

    public const array PHEROMONE_TYPE_LEVEL = [
        0 => 0,
        1 => 2,
        2 => 2,
        3 => 4,
        4 => 4,
        5 => 6,
        6 => 8
    ];

    // Special tiles

    // Special tiles Type

    public const int SPECIAL_TILE_TYPE_FARM = 7;
    public const int SPECIAL_TILE_TYPE_QUARRY = 8;
    public const int SPECIAL_TILE_TYPE_SUBANTHILL = 9;
    public const int SPECIAL_TILE_STONE_FARM = 10;
    public const int SPECIAL_TILE_DIRT_QUARRY = 11;

    // Special tiles amount
    public const array SPECIAL_TILE_TYPE_AMOUNT = [
        7 => 4,
        8 => 4,
        9 => 8
    ];

    // Special tiles level
    public const array SPECIAL_TILES_TYPE_LEVEL = [
        7 => 2,
        8 => 2,
        9 => 4
    ];

    // Direction's tiles

    public const int DIRECTION_NORTH_WEST = 1;
    public const int DIRECTION_NORTH_EAST = 2;
    public const int DIRECTION_EAST = 3;
    public const int DIRECTION_SOUTH_EAST = 4;
    public const int DIRECTION_SOUTH_WEST = 5;
    public const int DIRECTION_WEST = 6;

    // Prey Types

    public const string LADYBUG_TYPE = "ladybug";
    public const string TERMITE_TYPE = "termite";
    public const string SPIDER_TYPE = "spider";

    // Prey Numbers
    public const int LADYBUG_NUMBER = 6;
    public const int TERMITE_NUMBER = 6;
    public const int SPIDER_NUMBER = 6;

    // Prey Position
    public const array PREY_POSITIONS = [[1, 6], [1, 18], [4, 9], [4, 15], [5, 12], [6, 9], [6, 15], [7, 0], [7, 6],
        [7, 18], [7, 24], [8, 9], [8, 15], [9, 12], [10, 9], [10, 15], [13, 6], [13, 18]];

    //Player start data
    public const int NUMBER_OF_WORKER_AT_START = 2;
    public const int NUMBER_OF_LARVAE_AT_START = 1;
    public const int ANTHILL_START_LEVEL = 0;
    public const int PLAYER_START_SCORE = 10;
    // Number of soldiers for attack preys

    public const array NUMBER_SOLDIERS_FOR_ATTACK_PREY = array(
        "ladybug" => 1,
        "termite" => 1,
        "spider" => 2
    );

    // Food gain by attack preys

    public const array FOOD_GAIN_BY_ATTACK_PREY = array(
        "ladybug" => 2,
        "termite" => 1,
        "spider" => 1
    );

    // Victory gain by attack preys

    public const array VICTORY_GAIN_BY_ATTACK_PREY = array(
        "ladybug" => 0,
        "termite" => 2,
        "spider" => 4
    );


    //Anthill hole start position
    public const array ANTHILL_HOLE_POSITION_BY_NUMBER_OF_PLAYER = [
        2 => [[11, 6], [11, 18]],
        3 => [[5, 4], [4, 19], [12, 13]],
        4 => [[3, 6], [3, 18], [11, 6], [11, 18]]
    ];


    // Max anthill holes number by player

    public static int $MAX_ANTHILL_HOLE_NB = 4;

    //Excluded tile with 2 players
    public const array EXCLUDED_TILES_2_PLAYERS = [
        [7, 0], [8, 1], [9, 0], [9, 2], [10, 1], [10, 3], [11, 2], [11, 4], [12, 3], [12, 5], [13, 6],
        [7, 24], [8, 23], [9, 24], [9, 22], [10, 23], [10, 21], [11, 22], [11, 20], [12, 21], [12, 19], [13, 18]
    ];

    // Resources Type

    public const string RESOURCE_TYPE_DIRT = "dirt";
    public const string RESOURCE_TYPE_STONE = "stone";
    public const string RESOURCE_TYPE_GRASS = "grass";
    public const string RESOURCE_TYPE_LARVAE = "larvae";

    // Goal Difficulty

    public const int GOAL_DIFFICULTY_LEVEL_ONE = 1;
    public const int GOAL_DIFFICULTY_LEVEL_TWO = 2;
    public const int GOAL_DIFFICULTY_LEVEL_THREE = 3;

    // Goal Points

    public const int GOAL_REWARD_LEVEL_ONE = 6;
    public const int GOAL_REWARD_LEVEL_TWO = 9;
    public const int GOAL_REWARD_LEVEL_THREE = 12;
    public const int GOAL_REWARD_WHEN_GOAL_ALREADY_DONE = 3;

    // Anthill Level

    public const int ANTHILL_LEVEL_ONE = 1;
    public const int ANTHILL_LEVEL_TWO = 2;
    public const int ANTHILL_LEVEL_THREE = 3;
}