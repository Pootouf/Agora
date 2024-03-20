<?php

namespace App\Entity\Game\Myrmes;

class MyrmesParameters
{
    // GAME

    // MIN AND MAX NUMBER OF PLAYER

    public static int $MAX_NUMBER_OF_PLAYER = 4;
    public static int $MIN_NUMBER_OF_PLAYER = 2;


    // Area's for nurses
    public static array $AREA_LARVAE = array(1, 2, 2);
    public static array $AREA_SOLDIERS = array(2, 1);
    public static array $FIRST_AREA_WORKER = array(2, 2);
    public static array $AREA_WORKSHOP = array(
        "RIGHT" => "GOAL",
        "LEFT" => "ANTHILL_HOLE",
        "BOTTOM" => "NURSE",
        "TOP" => "LEVEL"
    );

    public static int $FIRST_YEAR_NUM = 1;
    public static int $SECOND_YEAR_NUM = 2;
    public static int $THIRD_YEAR_NUM = 3;


    public static string $SPRING_SEASON_NAME = "spring";
    public static string $SUMMER_SEASON_NAME = "summer";
    public static string $WINTER_SEASON_NAME = "winter";
    public static string $FALL_SEASON_NAME = "fall";
    public static string $INVALID_SEASON_NAME = "invalid";

}