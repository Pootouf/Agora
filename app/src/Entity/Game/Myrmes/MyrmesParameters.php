<?php

namespace App\Entity\Game\Myrmes;

class MyrmesParameters
{
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

}