<?php

namespace App\Entity\Game\Splendor;

interface SplendorParameters
{
    // SPLENDOR GAME PARAMETERS

    public const int MIN_NUMBER_OF_PLAYER = 2;
    public const int MAX_NUMBER_OF_PLAYER = 4;
    public const int MAX_PRESTIGE_POINTS = 15;

    //The minimum available tokens is 4 at the beginning of the players's round, but 3 when taking the second same color
    // token.
    public const int MIN_AVAILABLE_TOKENS = 3;
    public const int TOKENS_NUMBER_4_PLAYERS = 7;
    public const int TOKENS_NUMBER_3_PLAYERS = 5;
    public const int TOKENS_NUMBER_2_PLAYERS = 4;

    // DRAW CARDS PARAMETERS

    public const int DRAW_CARD_LEVEL_ONE = 0;
    public const int DRAW_CARD_LEVEL_TWO = 1;
    public const int DRAW_CARD_LEVEL_THREE = 2;

    // DEVELOPMENT CARDS LEVEL

    public const int DEVELOPMENT_CARD_LEVEL_ONE = 1;
    public const int DEVELOPMENT_CARD_LEVEL_TWO = 2;
    public const int DEVELOPMENT_CARD_LEVEL_THREE = 3;

    //MAIN BOARD PARAMETERS

    public const int NUMBER_OF_ROWS_BY_GAME = 3;

    //PERSONAL BOARD PARAMETERS

    public const int PLAYER_MAX_TOKEN = 10;
    public const int MAX_COUNT_RESERVED_CARDS = 3;

    //TOKENS' COLOR

    public const string COLOR_BLUE = 'blue';
    public const string COLOR_RED = 'red';
    public const string COLOR_YELLOW = 'yellow';
    public const string COLOR_GREEN = 'green';
    public const string COLOR_WHITE = 'white';
    public const string COLOR_BLACK = 'black';
    public const string LABEL_JOKER = "yellow";

    //SERVICE UTILITIES

    public const int COMES_OF_THE_DISCARDS = 1;
    public const int COMES_OF_THE_ROWS = 2;

    // POINTS PRESTIGE GIVEN BY NOBLE TILES

    public const int POINT_PRESTIGE_BY_NOBLE_TILE = 3;

    //NOTIFICATIONS

    public const int NOTIFICATION_DURATION_5 = 5;
    public const int NOTIFICATION_DURATION_10 = 10;

}
