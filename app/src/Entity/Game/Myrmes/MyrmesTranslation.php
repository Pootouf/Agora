<?php

namespace App\Entity\Game\Myrmes;

interface MyrmesTranslation
{
    // ERROR MESSAGES
    const string ERROR_CANNOT_PLACE_TILE = "Can't place this tile";
    const string ERROR_CANNOT_DO_GOAL_LOWER_DIFFICULTY_NEEDED =
        "The player can't do this goal, he must do a goal with a lower difficulty before";
    const string ERROR_GOAL_CANT_BE_DONE = "Goal can't be done";
    const string ERROR_GOAL_DIFFICULTY_INVALID = "Goal difficulty invalid for pheromone goal";

    // NOTIFICATION MESSAGES
    const string WARNING = "Attention !";
    const string CONGRATULATION = "Félicitation !";
    const string BIRTH_PHASE = "Tu es en phase d'évènement.";
    const string IMPOSSIBLE_TO_PLACE_NURSE = "Impossible de placer une nourrice";
    const string GOAL_VALIDATE = "Vous avez valider l'objectif";

    // LOG MESSAGES
    const string NOT_ABLE = " mais n'a pas pu";


    // RESPONSE MESSAGES
    const string RESPONSE_ERROR_CALCULATING_MAIN_BOARD = "Error while calculating main board tiles disposition";
    const string RESPONSE_INVALID_TILE = "Invalid tile";
    const string RESPONSE_INVALID_TILE_TYPE = "Invalid tile type";
    const string RESPONSE_NOT_IN_WORKER_PHASE = "Not in worker phase";
    const string RESPONSE_GOAL_VALIDATE = "Goal validated";
    const string RESPONSE_NO_NURSE_WORKSHOP = "No nurse available in workshop";
    const string RESPONSE_NO_PHEROMONE_IDS_GIVEN = "No pheromone ids given";
    const string RESPONSE_INVALID_PLAYER = "Invalid player";

}
