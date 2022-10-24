<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 20/04/2018
 * Time: 14:46
 */

namespace AGORA\Game\Socket;


abstract class Game {

    /**
     * @var $connectionStorage Les connexions liées a un jeu.
     */
    protected $connectionStorage;

    function __construct() {
        $this->connectionStorage = new ConnectionStorage();
    }

    abstract protected function handleAction($conn, $gameId, $playerId, $action);
}