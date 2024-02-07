<?php

namespace App\Service\Game;

use App\Entity\Game\DTO\Game;

abstract class AbstractGameManagerService {

    public static string $SIXQP_LABEL = "6QP";

    public static string $SPL_LABEL = "SPL";

    public static int $ERROR_INVALID_NUMBER_OF_PLAYER = -4;
    public static int $ERROR_INVALID_GAME = -2;
    public static int $ERROR_ALREADY_IN_PARTY = -1;
    public static int $ERROR_PLAYER_NOT_FOUND = -5;
    public static int $ERROR_GAME_ALREADY_LAUNCHED = -3;
    public static int $SUCCESS = 1;

    /**
     * createGame : create a  game
     * @return int the id of the game
     */
    public abstract function createGame(): int;

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
    public abstract function createPlayer(string $playerName, Game $game): int;

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
    public abstract function deletePlayer(string $playerName, Game $game): int;

    /**
     * deleteGame : delete a game
     * @param Game $game the game to delete
     * @return int
     *      $SUCCESS : success
     *      $ERROR_INVALID_GAME : the game argument in invalid
     */
    public abstract function deleteGame(Game $game): int;

    /**
     * launchGame : launch a game
     * @param Game $game the game to launch
     * @return int
     *      $SUCCESS : success
     *      $ERROR_INVALID_NUMBER_OF_PLAYER : invalid number of player
     *      $ERROR_INVALID_GAME : the game argument in invalid
     *      $ERROR_GAME_ALREADY_LAUNCHED : the game is already launched
     */
    public abstract function launchGame(Game $game): int;
}
