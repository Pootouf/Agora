<?php

namespace App\Entity\Game\Glenmore;

interface GlenmoreParameters
{
    // GAME

    // MIN AND MAX NUMBER OF PLAYER
    public const int MIN_NUMBER_OF_PLAYER = 2;
    public const int MAX_NUMBER_OF_PLAYER = 5;

    // MONEY GIVEN AT START OF THE GAME
    public const int START_MONEY = 6;

    // PLAYER ROUND PHASE

    public const int STABLE_PHASE = 0;
    public const int BUYING_PHASE = 1;
    public const int ACTIVATION_PHASE = 2;
    public const int MOVEMENT_PHASE = 3;
    public const int SELLING_PHASE = 4;

    // MAINBOARD BOXES
    public const int NUMBER_OF_BOXES_ON_BOARD = 14;

    // COLORS

    // PAWNS COLOR
    public const string COLOR_YELLOW = "yellow";
    public const string COLOR_RED = "red";
    public const string COLOR_GREEN = "green";
    public const string COLOR_BLUE = "blue";
    public const string COLOR_WHITE = "white";

    // PAWNS COLOR BY POSITION
    public const array COLOR_FROM_POSITION = ['yellow','red', 'green', 'blue', 'white'];


    // VILLAGERS COLOR
    public const string COLOR_BLACK = "black";

    // MINIMUM VILLAGER COUNT PER VILLAGE
    public const int MIN_VILLAGER_PER_VILLAGE = 1;

    // PRODUCTION RESOURCES COLOR (not yet defined)
    public const string COLOR_BROWN = "brown";
    public const string COLOR_GREY = "grey";

    // RESOURCE TYPES
    public const string PRODUCTION_RESOURCE = "production";
    public const string WHISKY_RESOURCE = "whisky";
    public const string HAT_RESOURCE = "hat";
    public const string VILLAGER_RESOURCE = "villager";
    public const string MOVEMENT_RESOURCE = "movement";
    public const string POINT_RESOURCE = "points";

    // DIRECTIONS
    public const int NORTH = 0;
    public const int NORTH_EAST = 1;
    public const int EAST = 2;
    public const int SOUTH_EAST = 3;
    public const int SOUTH = 4;
    public const int SOUTH_WEST = 5;
    public const int WEST = 6;
    public const int NORTH_WEST = 7;

    // TILES

    // TILES LEVEL
    public const int TILE_LEVEL_ZERO = 0;
    public const int TILE_LEVEL_ONE = 1;
    public const int TILE_LEVEL_TWO = 2;
    public const int TILE_LEVEL_THREE = 3;

    // MAXIMUM NUMBER OF RESOURCES PER TILE
    public const int MAX_RESOURCES_PER_TILE = 3;

    // TILES

    // TILES TYPE
    public const string TILE_TYPE_YELLOW = "yellow";
    public const string TILE_TYPE_GREEN = "green";
    public const string TILE_TYPE_BROWN = "brown";
    public const string TILE_TYPE_BLUE = "blue";
    public const string TILE_TYPE_VILLAGE = "village";
    public const string TILE_TYPE_CASTLE = "castle";

    // TILES NAME
    public const string TILE_NAME_FOREST = "forest";
    public const string TILE_NAME_PASTURE = "pasture";
    public const string TILE_NAME_QUARRY = "quarry";
    public const string TILE_NAME_FIELD = "field";
    public const string TILE_NAME_CATTLE = "cattle";
    public const string TILE_NAME_DISTILLERY = "distillery";
    public const string TILE_NAME_TAVERN = "tavern";
    public const string TILE_NAME_BUTCHER = "butcher";
    public const string TILE_NAME_FAIR = "fair";
    public const string TILE_NAME_BRIDGE = "bridge";
    public const string TILE_NAME_GROCER = "grocer";
    public const string TILE_NAME_VILLAGE = "village";

    public const string TILE_NAME_START_VILLAGE = "start village";

    // CARDS

    // CARDS NAME
    public const string CARD_CASTLE_OF_MEY = "Castle of Mey";
    public const string CARD_CAWDOR_CASTLE = "Cawdor Castle";
    public const string CARD_DUART_CASTLE = "Duart Castle";
    public const string CARD_CASTLE_MOIL = "Castle Moil";
    public const string CARD_ARMADALE_CASTLE = "Armadale Castle";
    public const string CARD_LOCH_SHIEL = "Loch Shiel";
    public const string CARD_DONAN_CASTLE = "Donan Castle";
    public const string CARD_LOCH_OICH = "Loch Oich";
    public const string CARD_LOCH_NESS = "Loch Ness";
    public const string CARD_IONA_ABBEY = "Iona Abbey";
    public const string CARD_LOCH_LOCHY = "Loch Lochy";
    public const string CARD_LOCH_MORAR = "Loch Morar";
    public const string CARD_CASTLE_STALKER = "Castle Stalker";

    // POINTS GIVEN BY SPECIAL CARDS
    public const int IONA_ABBEY_POINTS = 2;
    public const int LOCH_MORAR_POINTS = 2;
    public const int DUART_CASTLE_POINTS = 3;

    // WAREHOUSE

    // Extreme value for resource sales and purchases
    public const int MIN_TRADE = 0;
    public const int MAX_TRADE = 3;

    // Number of money compared to the quantity of resources
    public const array MONEY_FROM_QUANTITY = [1, 2, 3, -1];

    // COINS NEEDED FOR A RESOURCE
    public const int COIN_NEEDED_FOR_RESOURCE_ONE = 1;
    public const int COIN_NEEDED_FOR_RESOURCE_TWO = 2;
    public const int COIN_NEEDED_FOR_RESOURCE_THREE = 3;


    // BOT SECTION

    public const int MINIMUM_NUMBER_PLAYER_FOR_NO_BOT = 4;
    public const string BOT_NAME = 'bot';

    //NOTIFICATIONS
    public const int NOTIFICATION_DURATION = 10;

}
