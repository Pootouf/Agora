<?php

namespace App\Entity\Game\Myrmes;

class MyrmesParameters
{
    // GAME

    // MIN AND MAX NUMBER OF PLAYER

    public static int $MAX_NUMBER_OF_PLAYER = 4;
    public static int $MIN_NUMBER_OF_PLAYER = 2;


    // Area's for nurses
    public static int $BASE_AREA = 0;
    public static int $LARVAE_AREA = 1;
    public static int $SOLDIERS_AREA = 2;
    public static int $WORKER_AREA = 3;
    public static int $WORKSHOP_ANTHILL_HOLE_AREA = 4;
    public static int $WORKSHOP_LEVEL_AREA = 5;
    public static int $WORKSHOP_NURSE_AREA = 6;
    public static int $WORKSHOP_GOAL_AREA = 7;
    public static int $AREA_COUNT = 8;
    public static int $START_NURSES_COUNT_PER_PLAYER = 3;
    public static int $MAX_NURSES_COUNT_PER_PLAYER = 8;

    // Win by area's nurses
    public static array $WIN_LARVAE_BY_NURSES_COUNT = array(1 => 1, 2 => 3, 3 => 5);
    public static array $WIN_SOLDIERS_BY_NURSES_COUNT = array(2 => 1, 3 => 2);
    public static array $WIN_WORKERS_BY_NURSES_COUNT = array(2 => 1, 4 => 2);

    // Years
    public static int $FIRST_YEAR_NUM = 1;
    public static int $SECOND_YEAR_NUM = 2;
    public static int $THIRD_YEAR_NUM = 3;

    // Seasons
    public static string $SPRING_SEASON_NAME = "spring";
    public static string $SUMMER_SEASON_NAME = "summer";
    public static string $WINTER_SEASON_NAME = "winter";
    public static string $FALL_SEASON_NAME = "fall";
    public static string $INVALID_SEASON_NAME = "invalid";


    // TILE TYPES
    public static string $WATER_TILE_TYPE = "water";
    public static string $DIRT_TILE_TYPE = "dirt";
    public static string $MUSHROOM_TILE_TYPE = "mushroom";
    public static string $STONE_TILE_TYPE = "stone";
    public static string $GRASS_TILE_TYPE = "grass";

}