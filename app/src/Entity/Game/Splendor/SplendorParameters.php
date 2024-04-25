<?php

namespace App\Entity\Game\Splendor;

interface SplendorParameters
{
    // SPLENDOR GAME PARAMETERS

    const int MIN_NUMBER_OF_PLAYER = 2;
    const int MAX_NUMBER_OF_PLAYER = 4;
    const int MAX_PRESTIGE_POINTS = 15;

    //The minimum available tokens is 4 at the beginning of the players's round, but 3 when taking the second same color
    // token.
    const int MIN_AVAILABLE_TOKENS = 3;
    const int TOKENS_NUMBER_4_PLAYERS = 7;
    const int TOKENS_NUMBER_3_PLAYERS = 5;
    const int TOKENS_NUMBER_2_PLAYERS = 4;

    // DRAW CARDS PARAMETERS

    const int DRAW_CARD_LEVEL_ONE = 0;
    const int DRAW_CARD_LEVEL_TWO = 1;
    const int DRAW_CARD_LEVEL_THREE = 2;

    // DEVELOPMENT CARDS LEVEL

    const int DEVELOPMENT_CARD_LEVEL_ONE = 1;
    const int DEVELOPMENT_CARD_LEVEL_TWO = 2;
    const int DEVELOPMENT_CARD_LEVEL_THREE = 3;

    //MAIN BOARD PARAMETERS

    const int NUMBER_OF_ROWS_BY_GAME = 3;

    //PERSONAL BOARD PARAMETERS

    const int PLAYER_MAX_TOKEN = 10;
    const int MAX_COUNT_RESERVED_CARDS = 3;

    //TOKENS' COLOR

    const string COLOR_BLUE = 'blue';
    const string COLOR_RED = 'red';
    const string COLOR_YELLOW = 'yellow';
    const string COLOR_GREEN = 'green';
    const string COLOR_WHITE = 'white';
    const string COLOR_BLACK = 'black';
    const string LABEL_JOKER = "yellow";

    //SERVICE UTILITIES

    const int COMES_OF_THE_DISCARDS = 1;
    const int COMES_OF_THE_ROWS = 2;

    // POINTS PRESTIGE GIVEN BY NOBLE TILES

    const int POINT_PRESTIGE_BY_NOBLE_TILE = 3;

    //NOTIFICATIONS

    const int NOTIFICATION_DURATION_5 = 5;
    const int NOTIFICATION_DURATION_10 = 10;

}
