<?php

namespace App\Entity\Game\Myrmes;

interface MyrmesTranslation
{
    // ERROR MESSAGES
    public const string ERROR_CANNOT_PLACE_TILE = "Can't place this tile";
    public const string ERROR_CANNOT_DO_GOAL_LOWER_DIFFICULTY_NEEDED =
        "The player can't do this goal, he must do a goal with a lower difficulty before";
    public const string ERROR_GOAL_CANT_BE_DONE = "Goal can't be done";
    public const string ERROR_GOAL_DIFFICULTY_INVALID = "Goal difficulty invalid for pheromone goal";

    // NOTIFICATION MESSAGES
    public const string WARNING = "Attention !";
    public const string CONGRATULATION = "Félicitation !";
    public const string VALIDATION = "Action validée !";
    public const string ERROR_IMPOSSIBLE_TO_PLACE_NURSE = "Impossible de placer une nourrice";
    public const string ERROR_VALIDATE_NURSE_IMPOSSIBLE = "Impossible de confirmer le placement des nourrices";
    public const string VALIDATE_NURSE = "Les nourrices ont bien donné naissance aux fourmis";
    public const string CANCEL_NURSE_PLACEMENT = "Le placement des nourrices a bien été annulé";
    public const string GOAL_VALIDATE = "Vous avez validé l'objectif";
    public const string ERROR_BONUS_DECREASE_IMPOSSIBLE = "Impossible de baisser plus ton bonus";
    public const string ERROR_BONUS_INCREASE_IMPOSSIBLE = "Impossible d'augmenter plus ton bonus";
    public const string BONUS_VALIDATE = "Ton bonus a bien été validé";
    public const string WORKER_PLACE_IN_ANTHILL = "L'ouvrière travaille dans la fourmilière au niveau ";
    public const string ERROR_WORKER_PLACE_IN_ANTHILL = "L'ouvrière ne peut pas travailler dans ce niveau de fourmilière !";
    public const string CLEAN_PHEROMONE = "Tu as nettoyé la phéromone !";
    public const string PLACE_ANTHILL_HOLE = "Tu as placé un nouveau trou pour ta fourmilière !";
    public const string INCREASE_ANTHILL_LEVEL = "Tu as augmenté le niveau de ta fourmilière !";
    public const string CREATE_NURSE = "Tu possèdes maintenant une nouvelle nourrice !";
    public const string SACRIFICE_LARVAE = "Tu as sacrifié des larves pour de la nourriture !";
    public const string ERROR_PHEROMONE_GOAL = "Les phéromones ne sont pas connectées ou tu n'en as pas sélectionné assez.";
    public const string ERROR_STONE_OR_DIRT_GOAL = "Tu n'as pas assez de ressources !";
    public const string ERROR_SPECIAL_TILE_GOAL = "Tu n'as pas sélectionné assez de tuiles spéciales !";
    public const string NEED_TO_SELECT_QUARRY_RESOURCE =
        "N'oublie pas de sélectionner la ressource que tu veux pour ta fouille !";


    // LOG MESSAGES
    public const string NOT_ABLE = " mais n'a pas pu";


    // RESPONSE MESSAGES
    public const string RESPONSE_ERROR_CALCULATING_MAIN_BOARD = "Error while calculating main board tiles disposition";
    public const string RESPONSE_INVALID_TILE = "Invalid tile";
    public const string RESPONSE_INVALID_TILE_TYPE = "Invalid tile type";
    public const string RESPONSE_NOT_IN_WORKER_PHASE = "Not in worker phase";
    public const string RESPONSE_GOAL_VALIDATE = "Goal validated";
    public const string RESPONSE_NO_NURSE_WORKSHOP = "No nurse available in workshop";
    public const string RESPONSE_NO_PHEROMONE_IDS_GIVEN = "No pheromone ids given";
    public const string RESPONSE_INVALID_PLAYER = "Invalid player";

}
