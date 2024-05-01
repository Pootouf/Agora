<?php

namespace App\Entity\Game\SixQP;

interface SixQPParameters
{
    // 6 QUI PREND GAME PARAMETERS

    const int MIN_NUMBER_OF_PLAYER = 2;
    const int MAX_NUMBER_OF_PLAYER = 10;
    const int NUMBER_OF_CARDS_BY_PLAYER = 10;

    // MAIN BOARD ROWS

    const int NUMBER_OF_ROWS_BY_GAME = 4;
    const int MAX_CARD_COUNT_IN_LINE = 5;

    // ENDING CONDITION

    const int MAX_POINTS = 66;
}
