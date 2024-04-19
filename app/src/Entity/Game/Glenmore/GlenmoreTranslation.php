<?php

namespace App\Entity\Game\Glenmore;

interface GlenmoreTranslation
{
    // NOTIFICATION MESSAGES
    const string WARNING = "Attention !";
    const string CANNOT_BUY_RESOURCE = "Tu ne peux pas acheter cette ressource !";
    const string VALIDATE_BUY = "Achat validé !";
    const string VALIDATE_SELLING = "Vente validée !";
    const string VALIDATE_ACTION = "Action validée !";
    const string VALIDATE_LEADER = "Chef sélectionné !";
    const string VALIDATE_RESOURCE_SELECTION = "Ressource sélectionnée !";
    const string RESOURCE_SELECTION_DESCRIPTION = "Si tu as fini n'oublie pas de valider ton choix !";
    const string BUY_DESCRIPTION = "Tu as acheté ";
    const string SELL_DESCRIPTION = "Tu as vendu ";
    const string LEADER_DESCRIPTION = "Un nouveau chef fait parti de ton village !";
    const string TILE_PLACED_DESCRIPTION = "Ta tuile a bien été posée, active tes tuiles ou finis la phase";
    const string CANNOT_BUY_OR_PLACE_TILE = "Tu ne peux pas acheter/placer cette tuile !";
    const string WRONG_RESOURCE_SELECTED_TO_BUY = "Pas cette ressource, fais un effort !";
    const string WRONG_RESOURCE_SELECTED_TO_ACTIVATE = "Choisis une autre ressource !";
    const string WRONG_RESOURCE_SELECTED_TO_SELL = "C'est pas ça que tu as choisi de vendre !";
    const string CANNOT_SELECT_MORE_RESOURCES = "Tu ne peux pas sélectionner plus de ressources !";
    const string CANNOT_GET_OUT_LAST_VILLAGER = "Tu ne peux pas enlever ton dernier villageois !";
    const string CANNOT_ACTIVATE_TILE = "Tu ne peux pas activer cette tuile !";
    const string CANNOT_MOVE_HERE = "Tu ne peux pas te déplacer ici !";
    const array RESOURCE_DESC = [
        "yellow" =>"du blé",
        "white" => "de la laine",
        "brown" => "de la viande",
        "green" => "de l'herbe",
        "grey" => "de la pierre",
    ];



    // LOG MESSAGES
    const string TRY_BUY_RESOURCE = " a essayé d'acheter une ressource ";
    const string TRY_BUY_RESOURCE_NOT_PLAYER_ROUND = GlenmoreTranslation::TRY_BUY_RESOURCE . "hors de son tour";
    const string NOT_ABLE = " mais n'a pas pu";
    const string BUY_RESOURCE = " a acheté une ressource ";
    const string TRY_SELL_RESOURCE_NOT_PLAYER_ROUND = " a essayé de vendre une ressource hors de son tour";
    const string TRY_SELL_RESOURCE_ALREADY_IN_SELECTION = " n'a pas pu vendre une ressource car il était "
                    . "déjà en sélection de ressources";
    const string ENTER_SELLING_PHASE = " est entré en phase de sélection de ressources pour vendre";
    const string TRY_SELECT_TILE_NOT_PLAYER_ROUND = " a essayé de choisir une tuile hors de son tour";
    const string CHOOSE_TILE = " a choisi la tuile ";
    const string CANNOT_BUY = " mais ne peut pas l'acheter";
    const string TRY_PLACE_TILE_NOT_PLAYER_ROUND = " a essayé de placer une tuile hors de son tour";
    const string TRY_PLACE_TILE_IN_RESOURCE_SELECTION =
        " a essayé de placer une tuile mais est en sélection de ressources";
    const string NOT_ABLE_TO_PLACE_TILE = " n'a pas pu placer la tuile ";
    const string TILE_PLACED = " a placé la tuile ";
    const string TRY_SELECT_RESOURCE_NOT_ABLE = " a essayé de sélectionner une ressource mais n'a pas pu";
    const string RESOURCE_SELECTED = " a sélectionné la ressource ";
    const string TRY_SELECT_LEADER_NOT_ABLE = " a essayé de sélectionner un chef de village mais n'a pas pu";
    const string LEADER_SELECTED = " a selectionné un chef de village";
    const string TRY_GET_OUT_VILLAGER_NOT_PLAYER_ROUND = " a essayé de sortir un villageois hors de son tour";
    const string TRY_GET_OUT_VILLAGER_NOT_ABLE = " a essayé de sortir un villageois mais n'a pas pu";
    const string GET_OUT_VILLAGER = " a sorti un villageois de son village";
    const string TRY_ACTIVATE_TILE_NOT_PLAYER_ROUND = " a essayé d'activer une tuile en dehors de son tour";
    const string TRY_ACTIVATE_TILE = " a essayé d'activer la tuile ";
    const string ACTIVATE_TILE = " a activé la tuile ";
    const string FINISH_ACTIVATION_PHASE = " a terminé sa phase d'activation";
    const string TRY_MOVE_VILLAGER_NOT_PLAYER_ROUND =
        " a essayé de déplacer un villageois, alors que ce n'était pas son tour";
    const string TRY_MOVE_VILLAGER = " a essayé de déplacer un villageois depuis la tuile ";
    const string IN_DIRECTION = " dans la direction ";
    const string MOVE_VILLAGER = " a déplacé un villageois depuis la tuile ";
    const string FINISH_ACQUISITION_PHASE = " a mis fin à sa phase d'acquisition";
    const string CANCEL_SELECTION = " a redéposé les ressources choisies";
    const string NOT_SELECTED_NEEDED_RESOURCES = " n'a pas choisi les ressources demandées";
    const string TRY_SELL_RESOURCE = " a essayé de vendre la ressource ";
    const string VALIDATE_RESOURCES_SELECTION = " a validé sa prise de ressources";
    const string END_ROUND = " a mis fin à son tour";
    const string CANCEL_TILE_ACQUISITION = " a annulé sa prise de tuile";
    const string CANCEL_TILE_ACTIVATION = " a annulé l'activation de sa tuile";
    const string GAME_STRING = "La partie ";
    const string HAS_BEEN_CREATED = " a été créée";
    const string JOIN_GAME = " a rejoint la partie ";
    const string LEAVE_GAME = " a été retiré de la partie ";
    const string HAS_ENDED = " a pris fin";
    const string GAME_STARTED = " a débuté";



    // RESPONSE MESSAGES
    const string RESPONSE_CANNOT_AFFORD_RESOURCE = "Can't afford this resource";
    const string RESPONSE_CANNOT_AFFORD_TILE = "Can't afford this tile";
    const string RESPONSE_RESOURCE_BOUGHT = "Player bought this resource";
    const string RESPONSE_SELECTED_TILE = "player selected this tile";
    const string RESPONSE_TRY_SELL_RESOURCE_ALREADY_IN_SELECTION =
        "Can't sell a resource when already in resource selection";
    const string RESPONSE_ENTER_SELLING_PHASE = "Player activated selling selection of resource";
    const string RESPONSE_TRY_PLACE_TILE_IN_RESOURCE_SELECTION =
        "can't place this tile, need to validate selection of proper resources";
    const string RESPONSE_NOT_ABLE_TO_PLACE_TILE = "Can't place this tile ";
    const string RESPONSE_TILE_PLACED = "Player put this tile";
    const string RESPONSE_RESOURCE_SELECTED = "A new resource has been selected";
    const string RESPONSE_LEADER_SELECTED = "A leader has been selected";
    const string RESPONSE_CANNOT_SELECT_MORE_RESOURCE = "Can not select more resource";
    const string RESPONSE_INVALID_MOVE = "Invalid move";
    const string RESPONSE_VILLAGER_REMOVED = "A villager has been removed";
    const string RESPONSE_CANNOT_ACTIVATE_TILE = "Can't activate this tile: ";
    const string RESPONSE_TILE_ACTIVATED = "Tile was activated";
    const string RESPONSE_FINISH_ACTIVATION_PHASE = " has ended activation phase";
    const string RESPONSE_CANNOT_MOVE_VILLAGER =
        "Could not move a villager from this tile to targeted one : ";
    const string RESPONSE_MOVE_VILLAGER = "The villager has been moved";
    const string RESPONSE_FINISH_ACQUISITION_PHASE = " has ended new resources acquisition phase";
    const string RESPONSE_CANCEL_SELECTION = "The chosen resources have been canceled";
    const string RESPONSE_NOT_SELECTED_NEEDED_RESOURCES = "Player has not selected needed resources";
    const string RESPONSE_CANNOT_SELL_RESOURCE = "Can't sell this resource";
    const string RESPONSE_VALIDATE_RESOURCES_SELECTION = "Player validate the selection of resources";
    const string RESPONSE_END_ROUND = "Player ended his round";
    const string RESPONSE_CANCEL_TILE_ACQUISITION = "Player cancel his tile selection";
    const string RESPONSE_CANCEL_TILE_ACTIVATION = "Player cancel his tile activation";
}
