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
    const string VALIDATION = "Action validée !";
    const string ERROR_IMPOSSIBLE_TO_PLACE_NURSE = "Impossible de placer une nourrice";
    const string ERROR_VALIDATE_NURSE_IMPOSSIBLE = "Impossible de confirmer le placement des nourrices";
    const string VALIDATE_NURSE = "Les nourrices ont bien donné naissance aux fourmis";
    const string CANCEL_NURSE_PLACEMENT = "Le placement des nourrices a bien été annulé";
    const string GOAL_VALIDATE = "Vous avez validé l'objectif";
    const string ERROR_BONUS_DECREASE_IMPOSSIBLE = "Impossible de baisser plus ton bonus";
    const string ERROR_BONUS_INCREASE_IMPOSSIBLE = "Impossible d'augmenter plus ton bonus";
    const string BONUS_VALIDATE = "Ton bonus a bien été validé";
    const string WORKER_PLACE_IN_ANTHILL = "L'ouvrière travaille dans la fourmilière au niveau ";
    const string ERROR_WORKER_PLACE_IN_ANTHILL = "L'ouvrière ne peut pas travailler dans ce niveau de fourmilière !";
    const string CLEAN_PHEROMONE = "Tu as nettoyé la phéromone !";
    const string PLACE_ANTHILL_HOLE = "Tu as placé un nouveau trou pour ta fourmilière !";
    const string INCREASE_ANTHILL_LEVEL = "Tu as augmenté le niveau de ta fourmilière !";
    const string CREATE_NURSE = "Tu possèdes maintenant une nouvelle nourrice !";
    const string SACRIFICE_LARVAE = "Tu as sacrifié des larves pour de la nourriture !";
    const string ERROR_PHEROMONE_GOAL = "Les phéromones ne sont pas connectées ou tu n'en as pas sélectionné assez.";
    const string ERROR_STONE_OR_DIRT_GOAL = "Tu n'as pas assez de ressources !";
    const string ERROR_SPECIAL_TILE_GOAL = "Tu n'as pas sélectionné assez de tuiles spéciales !";
    const string NEED_TO_SELECT_QUARRY_RESOURCE =
        "N'oublie pas de sélectionner la ressource que tu veux pour ta fouille !";


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
