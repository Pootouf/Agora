<?php

namespace App\Data;

use App\Entity\Platform\Game;

class SearchData
{
    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $availability;

    /**
     * @var \DateTime | null
     */
    public $datecreation;

    /**
     * @var string
     */
    public $dateselection;

    /**
     * @var \DateTime | null
     */
    public $datecreationplus;

    /**
     * @var Game
     */
    public $game;
}