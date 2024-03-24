<?php

namespace App\Entity\Game\Splendor;

class SplendorParameters
{
    // SPLENDOR GAME PARAMETERS
    public static int $MIN_NUMBER_OF_PLAYER = 2;
    public static int $MAX_NUMBER_OF_PLAYER = 4;
    public static int $MAX_PRESTIGE_POINTS = 15;
    //The minimum available tokens is 4 at the beginning of the players's round, but 3 when taking the second same color
    // token.
    public static int $MIN_AVAILABLE_TOKENS = 3;

    public static int $TOKENS_NUMBER_4_PLAYERS = 7;

    public static int $TOKENS_NUMBER_3_PLAYERS = 5;

    public static int $TOKENS_NUMBER_2_PLAYERS = 4;

    //DRAW CARDS PARAMETERS
    public static int $DRAW_CARD_LEVEL_ONE = 0;
    public static int $DRAW_CARD_LEVEL_TWO = 1;
    public static int $DRAW_CARD_LEVEL_THREE = 2;

    //DEVELOPMENT CARDS LEVEL
    public static int $DEVELOPMENT_CARD_LEVEL_ONE = 1;
    public static int $DEVELOPMENT_CARD_LEVEL_TWO = 2;
    public static int $DEVELOPMENT_CARD_LEVEL_THREE = 3;

    //MAIN BOARD PARAMETERS
    public static int $NUMBER_OF_ROWS_BY_GAME = 3;

    //PERSONAL BOARD PARAMETERS
    public static int $PLAYER_MAX_TOKEN = 10;
    public static int $MAX_COUNT_RESERVED_CARDS = 3;

    //TOKENS' COLOR
    public static string $COLOR_BLUE = 'blue';
    public static string $COLOR_RED = 'red';
    public static string $COLOR_YELLOW = 'yellow';
    public static string $COLOR_GREEN = 'green';
    public static string $COLOR_WHITE = 'white';
    public static string $COLOR_BLACK = 'black';
    public static string $LABEL_JOKER = "yellow";

    //SERVICE UTILITIES
    public static int $COMES_OF_THE_DISCARDS = 1;
    public static int $COMES_OF_THE_ROWS = 2;

    // POINTS PRESTIGE GIVEN BY NOBLE TILES

    public static int $POINT_PRESTIGE_BY_NOBLE_TILE = 3;

    //NOTIFICATIONS
    public static int $NOTIFICATION_DURATION_5 = 5;
    public static int $NOTIFICATION_DURATION_10 = 10;

}