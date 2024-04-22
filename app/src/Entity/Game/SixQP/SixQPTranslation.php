<?php

namespace App\Entity\Game\SixQP;

interface SixQPTranslation
{

    // LOG MESSAGES
    const string CANNOT_CHOOSE_CARD = " n'a pas pu choisir la carte ";
    const string CHOOSE_CARD = " a choisi la carte ";
    const string PLACE_CARD = " a placé la carte ";
    const string DURING_GAME = " durant la partie ";
    const string CANNOT_PLACE_CARD_ON_LINE = " n'a pas pu placé sa carte sur la ligne ";
    const string ON_LINE = " sur la ligne ";
    const string NEW_ROUND = "Une nouvelle manche a débuté durant la partie ";
    const string CANNOT_CREATE_NEW_ROUND = "Echec de création d'une nouvelle manche dans la partie ";
    const string SYSTEM_PLACE_CARD = "Le système a placé la carte ";
    const string PLAYER_WIN = " a gagné la partie ";
    const string GAME_STRING = "La partie ";
    const string GAME_ENDED = " s'est terminée";


    // RESPONSE MESSAGES
    const string RESPONSE_ALREADY_PLAYED = "Player already have played";
    const string RESPONSE_CANNOT_CHOOSE_CARD = "Impossible to choose";
    const string RESPONSE_NEED_TO_CHOOSE = "Need to choose";
    const string RESPONSE_CARD_PLACED = "Card placed";
    const string RESPONSE_CHOOSE_CARD = "Choose a card";
    const string RESPONSE_CARD_INVALID_POSITION = "Can't place the card here";
}
