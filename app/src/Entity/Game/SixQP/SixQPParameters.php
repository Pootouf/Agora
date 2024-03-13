<?php

namespace App\Entity\Game\SixQP;

class SixQPParameters
{
    // 6 QUI PREND GAME PARAMETERS
    public static int $MIN_NUMBER_OF_PLAYER = 2;
    public static int $MAX_NUMBER_OF_PLAYER = 10;
    public static int $NUMBER_OF_CARDS_BY_PLAYER = 10;

    // MAIN BOARD ROWS
    public static int $NUMBER_OF_ROWS_BY_GAME = 4;
    public static int $MAX_CARD_COUNT_IN_LINE = 5;

    // ENDING CONDITION
    public static int $MAX_POINTS = 66;
}