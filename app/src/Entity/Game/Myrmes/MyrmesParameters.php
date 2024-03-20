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
    public static int $WORKSHOP_AREA = 4;
    public static int $AREA_COUNT = 5;

    public static int $FIRST_YEAR_NUM = 1;
    public static int $SECOND_YEAR_NUM = 2;
    public static int $THIRD_YEAR_NUM = 3;


    public static string $SPRING_SEASON_NAME = "spring";
    public static string $SUMMER_SEASON_NAME = "summer";
    public static string $WINTER_SEASON_NAME = "winter";
    public static string $FALL_SEASON_NAME = "fall";
    public static string $INVALID_SEASON_NAME = "invalid";

}