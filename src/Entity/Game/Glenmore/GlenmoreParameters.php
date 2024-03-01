<?php

namespace App\Entity\Game\Glenmore;
class GlenmoreParameters {

    public static string $COLOR_YELLOW = "yellow";
    public static string $COLOR_RED = "red";
    public static string $COLOR_GREEN = "green";
    public static string $COLOR_BLUE = "blue";
    public static string $COLOR_WHITE = "white";
    public static string $COLOR_BLACK = "black";
    public static string $COLOR_BROWN = "brown";
    public static string $COLOR_GREY = "grey";
    public static string $PRODUCTION_RESOURCE = "production";
    public static string $WHISKY_RESOURCE = "whisky";
    public static string $HAT_RESOURCE = "hat";
    public static string $VILLAGER_RESOURCE = "villager";
    public static string $MOVEMENT_RESOURCE = "movement";
    public static int $NORTH = 0;
    public static int $NORTH_EAST = 1;
    public static int $EAST = 2;
    public static int $SOUTH_EAST = 3;
    public static int $SOUTH = 4;
    public static int $SOUTH_WEST = 5;
    public static int $WEST = 6;
    public static int $NORTH_WEST = 7;

    public static int $TILE_LEVEL_ZERO = 0;
    public static int $TILE_LEVEL_ONE = 1;
    public static int $TILE_LEVEL_TWO = 2;
    public static int $TILE_LEVEL_THREE = 3;

    public static int $MAX_RESOURCES_PER_TILE = 3;
    public static int $MIN_NUMBER_OF_PLAYER = 2;
    public static int $MAX_NUMBER_OF_PLAYER = 3;

    public static int $NUMBER_OF_TILES_ON_BOARD = 14;

    public static int $MIN_TRADE = 0;

    public static int $MAX_TRADE = 3;
}