<?php

namespace App\Entity\Game\SixQP;

interface SixQPParameters
{
    // 6 QUI PREND GAME PARAMETERS

    public const int MIN_NUMBER_OF_PLAYER = 2;
    public const int MAX_NUMBER_OF_PLAYER = 10;
    public const int NUMBER_OF_CARDS_BY_PLAYER = 10;

    // MAIN BOARD ROWS

    public const int NUMBER_OF_ROWS_BY_GAME = 4;
    public const int MAX_CARD_COUNT_IN_LINE = 5;

    // ENDING CONDITION

    public const int MAX_POINTS = 66;
}
