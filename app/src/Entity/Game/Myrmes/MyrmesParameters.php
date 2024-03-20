<?php

namespace App\Entity\Game\Myrmes;

class MyrmesParameters
{
    // Area's for nurses
    public static int $BASE_AREA = 0;
    public static int $LARVAE_AREA = 1;
    public static int $SOLDIERS_AREA = 2;
    public static int $WORKER_AREA = 3;
    public static int $WORKSHOP_GOAL_AREA = 4;
    public static int $WORKSHOP_ANTHILL_HOLE_AREA = 5;
    public static int $WORKSHOP_LEVEL_AREA = 6;
    public static int $WORKSHOP_NURSE_AREA = 7;
    public static int $AREA_COUNT = 8;

    // Win by area's nurses
    public static array $WIN_LARVAE_BY_NURSES_COUNT = array(0, 1, 3, 5);
    public static array $WIN_SOLDIERS_BY_NURSES_COUNT = array(0, 0, 1, 1);
    public static array $WIN_WORKERS_BY_NURSES_COUNT = array(0, 0, 1, 0, 2);
}