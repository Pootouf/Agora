<?php

namespace App\Entity\Game\Glenmore;

interface GlenmoreTranslation
{
    // NOTIFICATION MESSAGES
    public const string WARNING = "Attention !";
    public const string CANNOT_BUY_RESOURCE = "Tu ne peux pas acheter cette ressource !";
    public const string VALIDATE_BUY = "Achat validé !";
    public const string VALIDATE_SELLING = "Vente validée !";
    public const string VALIDATE_ACTION = "Action validée !";
    public const string VALIDATE_LEADER = "Chef sélectionné !";
    public const string VALIDATE_RESOURCE_SELECTION = "Ressource sélectionnée !";
    public const string RESOURCE_SELECTION_DESCRIPTION = "Si tu as fini n'oublie pas de valider ton choix !";
    public const string BUY_DESCRIPTION = "Tu as acheté ";
    public const string SELL_DESCRIPTION = "Tu as vendu ";
    public const string LEADER_DESCRIPTION = "Un nouveau chef fait parti de ton village !";
    public const string TILE_PLACED_DESCRIPTION = "Ta tuile a bien été posée, active tes tuiles ou finis la phase";
    public const string CANNOT_BUY_OR_PLACE_TILE = "Tu ne peux pas acheter/placer cette tuile !";
    public const string WRONG_RESOURCE_SELECTED_TO_BUY = "Pas cette ressource, fais un effort !";
    public const string WRONG_RESOURCE_SELECTED_TO_ACTIVATE = "Choisis une autre ressource !";
    public const string WRONG_RESOURCE_SELECTED_TO_SELL = "C'est pas ça que tu as choisi de vendre !";
    public const string CANNOT_SELECT_MORE_RESOURCES = "Tu ne peux pas sélectionner plus de ressources !";
    public const string CANNOT_GET_OUT_LAST_VILLAGER = "Tu ne peux pas enlever ton dernier villageois !";
    public const string CANNOT_ACTIVATE_TILE = "Tu ne peux pas activer cette tuile !";
    public const string CANNOT_MOVE_HERE = "Tu ne peux pas te déplacer ici !";
    public const array RESOURCE_DESC = [
        "yellow" => "du blé",
        "white" => "de la laine",
        "brown" => "de la viande",
        "green" => "de l'herbe",
        "grey" => "de la pierre",
    ];



    // LOG MESSAGES
    public const string TRY_BUY_RESOURCE = " a essayé d'acheter une ressource ";
    public const string TRY_BUY_RESOURCE_NOT_PLAYER_ROUND = GlenmoreTranslation::TRY_BUY_RESOURCE . "hors de son tour";
    public const string NOT_ABLE = " mais n'a pas pu";
    public const string BUY_RESOURCE = " a acheté une ressource ";
    public const string TRY_SELL_RESOURCE_NOT_PLAYER_ROUND = " a essayé de vendre une ressource hors de son tour";
    public const string TRY_SELL_RESOURCE_ALREADY_IN_SELECTION = " n'a pas pu vendre une ressource car il était "
        . "déjà en sélection de ressources";
    public const string ENTER_SELLING_PHASE = " est entré en phase de sélection de ressources pour vendre";
    public const string TRY_SELECT_TILE_NOT_PLAYER_ROUND = " a essayé de choisir une tuile hors de son tour";
    public const string CHOOSE_TILE = " a choisi la tuile ";
    public const string CANNOT_BUY = " mais ne peut pas l'acheter";
    public const string TRY_PLACE_TILE_NOT_PLAYER_ROUND = " a essayé de placer une tuile hors de son tour";
    public const string TRY_PLACE_TILE_IN_RESOURCE_SELECTION =
        " a essayé de placer une tuile mais est en sélection de ressources";
    public const string NOT_ABLE_TO_PLACE_TILE = " n'a pas pu placer la tuile ";
    public const string TILE_PLACED = " a placé la tuile ";
    public const string TRY_SELECT_RESOURCE_NOT_ABLE = " a essayé de sélectionner une ressource mais n'a pas pu";
    public const string RESOURCE_SELECTED = " a sélectionné la ressource ";
    public const string TRY_SELECT_LEADER_NOT_ABLE = " a essayé de sélectionner un chef de village mais n'a pas pu";
    public const string LEADER_SELECTED = " a selectionné un chef de village";
    public const string TRY_GET_OUT_VILLAGER_NOT_PLAYER_ROUND = " a essayé de sortir un villageois hors de son tour";
    public const string TRY_GET_OUT_VILLAGER_NOT_ABLE = " a essayé de sortir un villageois mais n'a pas pu";
    public const string GET_OUT_VILLAGER = " a sorti un villageois de son village";
    public const string TRY_ACTIVATE_TILE_NOT_PLAYER_ROUND = " a essayé d'activer une tuile en dehors de son tour";
    public const string TRY_ACTIVATE_TILE = " a essayé d'activer la tuile ";
    public const string ACTIVATE_TILE = " a activé la tuile ";
    public const string FINISH_ACTIVATION_PHASE = " a terminé sa phase d'activation";
    public const string TRY_MOVE_VILLAGER_NOT_PLAYER_ROUND =
        " a essayé de déplacer un villageois, alors que ce n'était pas son tour";
    public const string TRY_MOVE_VILLAGER = " a essayé de déplacer un villageois depuis la tuile ";
    public const string IN_DIRECTION = " dans la direction ";
    public const string MOVE_VILLAGER = " a déplacé un villageois depuis la tuile ";
    public const string FINISH_ACQUISITION_PHASE = " a mis fin à sa phase d'acquisition";
    public const string CANCEL_SELECTION = " a redéposé les ressources choisies";
    public const string NOT_SELECTED_NEEDED_RESOURCES = " n'a pas choisi les ressources demandées";
    public const string TRY_SELL_RESOURCE = " a essayé de vendre la ressource ";
    public const string VALIDATE_RESOURCES_SELECTION = " a validé sa prise de ressources";
    public const string END_ROUND = " a mis fin à son tour";
    public const string CANCEL_TILE_ACQUISITION = " a annulé sa prise de tuile";
    public const string CANCEL_TILE_ACTIVATION = " a annulé l'activation de sa tuile";
    public const string HAS_BEEN_CREATED = " a été créée";
    public const string JOIN_GAME = " a rejoint la partie ";
    public const string LEAVE_GAME = " a été retiré de la partie ";
    public const string WIN_GAME = " a gagné la partie ";
    public const string GAME_DESC = "La partie";
    public const string HAS_ENDED = " a pris fin";
    public const string GAME_STARTED = " a débuté";



    // RESPONSE MESSAGES
    public const string RESPONSE_CANNOT_AFFORD_RESOURCE = "Can't afford this resource";
    public const string RESPONSE_CANNOT_AFFORD_TILE = "Can't afford this tile";
    public const string RESPONSE_RESOURCE_BOUGHT = "Player bought this resource";
    public const string RESPONSE_SELECTED_TILE = "player selected this tile";
    public const string RESPONSE_TRY_SELL_RESOURCE_ALREADY_IN_SELECTION =
        "Can't sell a resource when already in resource selection";
    public const string RESPONSE_ENTER_SELLING_PHASE = "Player activated selling selection of resource";
    public const string RESPONSE_TRY_PLACE_TILE_IN_RESOURCE_SELECTION =
        "can't place this tile, need to validate selection of proper resources";
    public const string RESPONSE_NOT_ABLE_TO_PLACE_TILE = "Can't place this tile ";
    public const string RESPONSE_TILE_PLACED = "Player put this tile";
    public const string RESPONSE_RESOURCE_SELECTED = "A new resource has been selected";
    public const string RESPONSE_LEADER_SELECTED = "A leader has been selected";
    public const string RESPONSE_CANNOT_SELECT_MORE_RESOURCE = "Can not select more resource";
    public const string RESPONSE_INVALID_MOVE = "Invalid move";
    public const string RESPONSE_VILLAGER_REMOVED = "A villager has been removed";
    public const string RESPONSE_CANNOT_ACTIVATE_TILE = "Can't activate this tile: ";
    public const string RESPONSE_TILE_ACTIVATED = "Tile was activated";
    public const string RESPONSE_FINISH_ACTIVATION_PHASE = " has ended activation phase";
    public const string RESPONSE_CANNOT_MOVE_VILLAGER =
        "Could not move a villager from this tile to targeted one : ";
    public const string RESPONSE_MOVE_VILLAGER = "The villager has been moved";
    public const string RESPONSE_FINISH_ACQUISITION_PHASE = " has ended new resources acquisition phase";
    public const string RESPONSE_CANCEL_SELECTION = "The chosen resources have been canceled";
    public const string RESPONSE_NOT_SELECTED_NEEDED_RESOURCES = "Player has not selected needed resources";
    public const string RESPONSE_CANNOT_SELL_RESOURCE = "Can't sell this resource";
    public const string RESPONSE_VALIDATE_RESOURCES_SELECTION = "Player validate the selection of resources";
    public const string RESPONSE_END_ROUND = "Player ended his round";
    public const string RESPONSE_CANCEL_TILE_ACQUISITION = "Player cancel his tile selection";
    public const string RESPONSE_CANCEL_TILE_ACTIVATION = "Player cancel his tile activation";


    // ERROR MESSAGES
    public const string ERROR_CANNOT_PICK_MORE_RESOURCES = "Can't pick more resource for this tile";
}
