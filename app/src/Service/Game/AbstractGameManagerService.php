<?php

namespace App\Service\Game;

use App\Entity\Game\DTO\Game;

abstract class AbstractGameManagerService {

    const string SIXQP_LABEL = "6QP";
    const string SPL_LABEL = "SPL";
    const string GLM_LABEL = "GLM";
    const string MYR_LABEL = "MYR";

    const int SUCCESS = 1;
    const int ERROR_ALREADY_IN_PARTY = -1;
    const int ERROR_INVALID_GAME = -2;
    const int ERROR_GAME_ALREADY_LAUNCHED = -3;
    const int ERROR_INVALID_NUMBER_OF_PLAYER = -4;
    const int ERROR_PLAYER_NOT_FOUND = -5;


    /**
     * createGame : create a  game
     * @return int the id of the game
     */
    abstract public function createGame(): int;

    /**
     * createPlayer : create a player of the game and save him in the database
     * @param string $playerName the name of the player to create
     * @param Game $game the game of the player
     * @return int
     *      $SUCCESS : success
     *      $ERROR_TOO_MANY_PLAYER : too many players
     *      $ERROR_INVALID_GAME : the game argument is invalid
     *      $ERROR_ALREADY_IN_PARTY : the player is already in the party
     *      $ERROR_GAME_ALREADY_LAUNCHED : the game is already launched
     */
    abstract public function createPlayer(string $playerName, Game $game): int;

    /**
     * deletePlayer : delete a player
     * @param string $playerName the name of the player to delete
     * @param Game $game the game of the player
     * @return int
     *      $SUCCESS : success
     *      $ERROR_PLAYER_NOT_FOUND : impossible to find the player
     *      $ERROR_INVALID_GAME : the game argument in invalid
     *      $ERROR_GAME_ALREADY_LAUNCHED : the game is already launched
     */
    abstract public function deletePlayer(string $playerName, Game $game): int;

    /**
     * deleteGame : delete a game
     * @param Game $game the game to delete
     * @return int
     *      $SUCCESS : success
     *      $ERROR_INVALID_GAME : the game argument in invalid
     */
    abstract public function deleteGame(Game $game): int;

    /**
     * launchGame : launch a game
     * @param Game $game the game to launch
     * @return int
     *      $SUCCESS : success
     *      $ERROR_INVALID_NUMBER_OF_PLAYER : invalid number of player
     *      $ERROR_INVALID_GAME : the game argument in invalid
     *      $ERROR_GAME_ALREADY_LAUNCHED : the game is already launched
     */
    abstract public function launchGame(Game $game): int;
}
