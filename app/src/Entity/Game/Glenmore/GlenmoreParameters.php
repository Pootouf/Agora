<?php

namespace App\Entity\Game\Glenmore;
interface GlenmoreParameters {

    // GAME

    // MIN AND MAX NUMBER OF PLAYER
    const int MIN_NUMBER_OF_PLAYER = 2;
    const int MAX_NUMBER_OF_PLAYER = 5;

    // MONEY GIVEN AT START OF THE GAME
    const int START_MONEY = 6;

    // PLAYER ROUND PHASE

    const int STABLE_PHASE = 0;
    const int BUYING_PHASE = 1;
    const int ACTIVATION_PHASE = 2;
    const int MOVEMENT_PHASE = 3;
    const int SELLING_PHASE = 4;

    // MAINBOARD BOXES
    const int NUMBER_OF_BOXES_ON_BOARD = 14;

    // COLORS

    // PAWNS COLOR
    const string COLOR_YELLOW = "yellow";
    const string COLOR_RED = "red";
    const string COLOR_GREEN = "green";
    const string COLOR_BLUE = "blue";
    const string COLOR_WHITE = "white";

    // PAWNS COLOR BY POSITION
    const array COLOR_FROM_POSITION = ['yellow','red', 'green', 'blue', 'white'];


    // VILLAGERS COLOR
    const string COLOR_BLACK = "black";

    // MINIMUM VILLAGER COUNT PER VILLAGE
    const int MIN_VILLAGER_PER_VILLAGE = 1;

    // PRODUCTION RESOURCES COLOR (not yet defined)
    const string COLOR_BROWN = "brown";
    const string COLOR_GREY = "grey";

    // RESOURCE TYPES
    const string PRODUCTION_RESOURCE = "production";
    const string WHISKY_RESOURCE = "whisky";
    const string HAT_RESOURCE = "hat";
    const string VILLAGER_RESOURCE = "villager";
    const string MOVEMENT_RESOURCE = "movement";
    const string POINT_RESOURCE = "points";

    // DIRECTIONS
    const int NORTH = 0;
    const int NORTH_EAST = 1;
    const int EAST = 2;
    const int SOUTH_EAST = 3;
    const int SOUTH = 4;
    const int SOUTH_WEST = 5;
    const int WEST = 6;
    const int NORTH_WEST = 7;

    // TILES

    // TILES LEVEL
    const int TILE_LEVEL_ZERO = 0;
    const int TILE_LEVEL_ONE = 1;
    const int TILE_LEVEL_TWO = 2;
    const int TILE_LEVEL_THREE = 3;

    // MAXIMUM NUMBER OF RESOURCES PER TILE
    const int MAX_RESOURCES_PER_TILE = 3;

    // TILES

    // TILES TYPE
    const string TILE_TYPE_YELLOW = "yellow";
    const string TILE_TYPE_GREEN = "green";
    const string TILE_TYPE_BROWN = "brown";
    const string TILE_TYPE_BLUE = "blue";
    const string TILE_TYPE_VILLAGE = "village";
    const string TILE_TYPE_CASTLE = "castle";

    // TILES NAME
    const string TILE_NAME_FOREST = "forest";
    const string TILE_NAME_PASTURE = "pasture";
    const string TILE_NAME_QUARRY = "quarry";
    const string TILE_NAME_FIELD = "field";
    const string TILE_NAME_CATTLE = "cattle";
    const string TILE_NAME_DISTILLERY = "distillery";
    const string TILE_NAME_TAVERN = "tavern";
    const string TILE_NAME_BUTCHER = "butcher";
    const string TILE_NAME_FAIR = "fair";
    const string TILE_NAME_BRIDGE = "bridge";
    const string TILE_NAME_GROCER = "grocer";
    const string TILE_NAME_VILLAGE = "village";

    const string TILE_NAME_START_VILLAGE = "start village";

    // CARDS

    // CARDS NAME
    const string CARD_CASTLE_OF_MEY = "Castle of Mey";
    const string CARD_CAWDOR_CASTLE = "Cawdor Castle";
    const string CARD_DUART_CASTLE = "Duart Castle";
    const string CARD_CASTLE_MOIL = "Castle Moil";
    const string CARD_ARMADALE_CASTLE = "Armadale Castle";
    const string CARD_LOCH_SHIEL = "Loch Shiel";
    const string CARD_DONAN_CASTLE = "Donan Castle";
    const string CARD_LOCH_OICH = "Loch Oich";
    const string CARD_LOCH_NESS = "Loch Ness";
    const string CARD_IONA_ABBEY = "Iona Abbey";
    const string CARD_LOCH_LOCHY = "Loch Lochy";
    const string CARD_LOCH_MORAR = "Loch Morar";
    const string CARD_CASTLE_STALKER = "Castle Stalker";

    // POINTS GIVEN BY SPECIAL CARDS
    const int IONA_ABBEY_POINTS = 2;
    const int LOCH_MORAR_POINTS = 2;
    const int DUART_CASTLE_POINTS = 3;

    // WAREHOUSE

    // Extreme value for resource sales and purchases
    const int MIN_TRADE = 0;
    const int MAX_TRADE = 3;

    // Number of money compared to the quantity of resources
    const array MONEY_FROM_QUANTITY = [1, 2, 3, -1];

    // COINS NEEDED FOR A RESOURCE
    const int COIN_NEEDED_FOR_RESOURCE_ONE = 1;
    const int COIN_NEEDED_FOR_RESOURCE_TWO = 2;
    const int COIN_NEEDED_FOR_RESOURCE_THREE = 3;


    // BOT SECTION

    const int MINIMUM_NUMBER_PLAYER_FOR_NO_BOT = 4;
    const string BOT_NAME = 'bot';

    //NOTIFICATIONS
    const int NOTIFICATION_DURATION = 10;

}