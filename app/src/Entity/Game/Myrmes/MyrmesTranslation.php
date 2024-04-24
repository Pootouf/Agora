<?php

namespace App\Entity\Game\Myrmes;

interface MyrmesTranslation
{
    // ERROR MESSAGES
    const string ERROR_CANNOT_PLACE_TILE = "Can't place this tile";
    const string ERROR_CANNOT_DO_GOAL_LOWER_DIFFICULTY_NEEDED =
        "The player can't do this goal, he must do a goal with a lower difficulty before";

    // NOTIFICATION MESSAGES
    const string WARNING = "Attention !";
    const string BIRTH_PHASE = "Tu es en phase d'évènement.";
    const string IMPOSSIBLE_TO_PLACE_NURSE = "Impossible de placer une nourrice";

    // LOG MESSAGES
    const string NOT_ABLE = " mais n'a pas pu";


    // RESPONSE MESSAGES
    const string RESPONSE_ERROR_CALCULATING_MAIN_BOARD = "Error while calculating main board tiles disposition";
    const string RESPONSE_INVALID_TILE = "Invalid tile";
    const string RESPONSE_INVALID_TILE_TYPE = "Invalid tile type";
}
