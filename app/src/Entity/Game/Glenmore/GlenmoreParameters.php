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
    public static string $POINT_RESOURCE = "points";


    public static string $TILE_NAME_START_VILLAGE = "start village";

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
    public static int $MAX_NUMBER_OF_PLAYER = 5;

    public static int $START_MONEY = 6;
    public static int $COIN_NEEDED_FOR_RESOURCE_ONE = 1;
    public static int $COIN_NEEDED_FOR_RESOURCE_TWO = 2;
    public static int $COIN_NEEDED_FOR_RESOURCE_THREE = 3;

    public static array $COLOR_FROM_POSITION = ['yellow','red', 'green', 'blue', 'white'];


    public static int $NUMBER_OF_BOXES_ON_BOARD = 14;
    public static string $CARD_CASTLE_OF_MEY = "Castle of Mey";
    public static string $CARD_CAWDOR_CASTLE = "Cawdor Castle";
    public static string $CARD_DUART_CASTLE = "Duart Castle";
    public static string $CARD_CASTLE_MOIL = "Castle Moil";
    public static string $CARD_ARMADALE_CASTLE = "Armadale Castle";
    public static string $CARD_LOCH_SHIEL = "Loch Shiel";
    public static string $CARD_DONAN_CASTLE = "Donan Castle";
    public static string $CARD_LOCH_OICH = "Loch Oich";
    public static string $CARD_LOCH_NESS = "Loch Ness";
    public static string $CARD_IONA_ABBEY = "Iona Abbey";
    public static string $CARD_LOCH_LOCHY = "Loch Lochy";
    public static string $CARD_LOCH_MORAR = "Loch Morar";
    public static string $CARD_CASTLE_STALKER = "Castle Stalker";
    public static string $TILE_TYPE_YELLOW = "yellow";
    public static string $TILE_TYPE_GREEN = "green";
    public static string $TILE_TYPE_BROWN = "brown";
    public static string $TILE_TYPE_BLUE = "blue";
    public static string $TILE_TYPE_VILLAGE = "village";
    public static string $TILE_TYPE_CASTLE = "castle";
    public static string $TILE_NAME_FOREST = "forest";
    public static string $TILE_NAME_PASTURE = "pasture";
    public static string $TILE_NAME_QUARRY = "quarry";
    public static string $TILE_NAME_FIELD = "field";
    public static string $TILE_NAME_CATTLE = "cattle";
    public static string $TILE_NAME_DISTILLERY = "distillery";
    public static string $TILE_NAME_TAVERN = "tavern";
    public static string $TILE_NAME_BUTCHER = "butcher";
    public static string $TILE_NAME_FAIR = "fair";
    public static string $TILE_NAME_BRIDGE = "bridge";
    public static string $TILE_NAME_GROCER = "grocer";
    public static string $TILE_NAME_VILLAGE = "village";

    public static int $IONA_ABBEY_POINTS = 2;
    public static int $LOCH_MORAR_POINTS = 2;
    public static int $DUART_CASTLE_POINTS = 3;


    // Extreme value for resource sales and purchases
    public static int $MIN_TRADE = 0;
    public static int $MAX_TRADE = 3;
}