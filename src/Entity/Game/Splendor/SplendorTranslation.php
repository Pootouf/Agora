<?php

namespace App\Entity\Game\Splendor;

interface SplendorTranslation
{
    // NOTIFICATION MESSAGES
    public const string MESSAGE_TITLE_ROUND_START = "C'est votre tour !";
    public const string MESSAGE_DESCRIPTION_ROUND_START = "Jouez votre meilleur coup !";
    public const string MESSAGE_TITLE_OTHER_PLAYER_ROUND_START = "Joueur suivant !";
    public const string MESSAGE_DESCRIPTION_OTHER_PLAYER_ROUND_START = "C'est au tour de ";
    public const string MESSAGE_TITLE_NOBLE_VISIT = "Un noble vous rend visite !";
    public const string MESSAGE_TITLE_OTHER_PLAYER_NOBLE_VISIT = " reçoit la visite d'un noble";
    public const string MESSAGE_TITLE_ACTION_VALIDATED = "Action validée !";


    // LOG MESSAGES
    public const string CANNOT_BUY_CARD = " n'a pas pu acheter la carte ";
    public const string RECEIVED_NOBLE_VISIT = " a reçu la visite d'un noble venant de la tuile noble";
    public const string BOUGHT_CARD = " a acheté la carte ";
    public const string RESERVED_CARD = " a réservé la carte ";
    public const string TRY_RESERVE_CARD = " a essayé de réserver la carte ";
    public const string NOT_ABLE = " alors qu'il ne peut pas";
    public const string TOKEN_SELECTION_CANCELED = " a reposé les jetons en cours de sélection";
    public const string NO_MORE_TOKEN_SELECTED_COLOR = "There is no more token of this color";
    public const string TRY_TAKE_TOKEN = " a essayé de prendre un jeton ";
    public const string TAKE_TOKEN = " a pris un jeton ";
    public const string WIN_GAME = " a gagné la partie ";
    public const string GAME_DESC = "La partie";
    public const string HAS_ENDED = " s'est terminée";


    // RESPONSE MESSAGES
    public const string RESPONSE_NOT_PLAYER_CARD = "Not player's card";
    public const string RESPONSE_CARD_NOT_RESERVED = "The card is not reserved";
    public const string RESPONSE_CANNOT_BUY_CARD = "Can't buy this card : ";
    public const string RESPONSE_CANNOT_RESERVE_CARD = "Can't reserve this card";
    public const string RESPONSE_BOUGHT_CARD = "Card Bought";
    public const string RESPONSE_RESERVED_CARD = "Card Reserved";
    public const string RESPONSE_TOKEN_SELECTION_CANCELED = "Selected tokens cleaned";
    public const string RESPONSE_NOT_ABLE_TO_TAKE_TOKEN = "Impossible to choose the selected token";
    public const string RESPONSE_TAKE_TOKEN = "Token picked";
}
