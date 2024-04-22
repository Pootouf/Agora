<?php

namespace App\Entity\Game\Splendor;

interface SplendorTranslation
{
    // NOTIFICATION MESSAGES
    const string MESSAGE_TITLE_ROUND_START = "C'est votre tour !";
    const string MESSAGE_DESCRIPTION_ROUND_START = "Jouez votre meilleur coup !";
    const string MESSAGE_TITLE_OTHER_PLAYER_ROUND_START = "Joueur suivant !";
    const string MESSAGE_DESCRIPTION_OTHER_PLAYER_ROUND_START = "C'est au tour de ";
    const string MESSAGE_TITLE_NOBLE_VISIT = "Un noble vous rend visite !";
    const string MESSAGE_TITLE_OTHER_PLAYER_NOBLE_VISIT = " reçoit la visite d'un noble";
    const string MESSAGE_TITLE_ACTION_VALIDATED = "Action validée !";


    // LOG MESSAGES
    const string CANNOT_BUY_CARD = " n'a pas pu acheter la carte ";
    const string RECEIVED_NOBLE_VISIT = " a reçu la visite d'un noble venant de la tuile noble";
    const string BOUGHT_CARD = " a acheté la carte ";
    const string RESERVED_CARD = " a réservé la carte ";
    const string TRY_RESERVE_CARD = " a essayé de réserver la carte ";
    const string NOT_ABLE = " alors qu'il ne peut pas";
    const string TOKEN_SELECTION_CANCELED = " a reposé les jetons en cours de sélection";
    const string NO_MORE_TOKEN_SELECTED_COLOR = "There is no more token of this color";
    const string TRY_TAKE_TOKEN = " a essayé de prendre un jeton ";
    const string TAKE_TOKEN = " a pris un jeton ";
    const string WIN_GAME = " a gagné la partie ";
    const string GAME_DESC = "La partie";
    const string HAS_ENDED = " s'est terminée";


    // RESPONSE MESSAGES
    const string RESPONSE_NOT_PLAYER_CARD = "Not player's card";
    const string RESPONSE_CARD_NOT_RESERVED = "The card is not reserved";
    const string RESPONSE_CANNOT_BUY_CARD = "Can't buy this card : ";
    const string RESPONSE_CANNOT_RESERVE_CARD = "Can't reserve this card";
    const string RESPONSE_BOUGHT_CARD = "Card Bought";
    const string RESPONSE_RESERVED_CARD = "Card Reserved";
    const string RESPONSE_TOKEN_SELECTION_CANCELED = "Selected tokens cleaned";
    const string RESPONSE_NOT_ABLE_TO_TAKE_TOKEN = "Impossible to choose the selected token";
    const string RESPONSE_TAKE_TOKEN = "Token picked";
}
