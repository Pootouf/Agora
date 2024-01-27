<?php

use App\Entity\Game\DTO\Game;

abstract class AbstractGameService {

    public static string $SIXQP_LABEL = "6QP";

    /**
     * createGame : create a  game
     * @return int the id of the game
     */
    public abstract function createGame(): int;

    /**
     * createPlayer : create a player of the game and save him in the database
     * @param string $playerName the name of the player to create
     * @param Game $game the game of the player
     * @return int -4 if too many player, 1 success, -6 invalid game
     */
    public abstract function createPlayer(string $playerName, Game $game): int;

    /**
     * deletePlayer : delete a player
     * @param string $playerName the name of the player to delete
     * @param Game $game the game of the player
     * @return int 1 success, -1 no player, -6 invalid game
     */
    public abstract function deletePlayer(string $playerName, Game $game): int;

    /**
     * deleteGame : delete a game
     * @param Game $game the game to delete
     * @return int 1 success, -6 invalid game
     */
    public abstract function deleteGame(Game $game): int;

    /**
     * launchGame : launch a game
     * @param Game $game the game to launch
     * @return int -4 not a valid number of player, 1 success, -6 invalid game
     */
    public abstract function launchGame(Game $game): int;
}
