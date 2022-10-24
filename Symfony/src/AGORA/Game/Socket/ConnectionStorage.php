<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 20/04/2018
 * Time: 15:07
 */

namespace AGORA\Game\Socket;


use SplObjectStorage;

class ConnectionStorage {

    /**
     * @var Map<"roomId", Map<"playerId", "connection">>
     */
    private $connections;

    /**
     * @var Map<"connection", Array {"roomId", "playerId"}>
     */
    private $connectionToPlayer;

    function __construct() {
        $this->connections = array();
        $this->connectionToPlayer = new SplObjectStorage();
    }

    function addConnection($roomId, $playerId, $conn) {
        $room = strval($roomId);
        $player = strval($playerId);
        if (!isset($connections[$room])) {
            $connections[$room] = array();
        }
        $this->connections[$room][$player] = $conn;
        $this->connectionToPlayer[$conn] = array($room, $player);
    }

    function removeConnection($connection) {
        $roomAndPlayer = $this->connectionToPlayer[$connection];
        $room = $roomAndPlayer[0];
        $player = $roomAndPlayer[1];
        if (!isset($this->connections[$room]) || !isset($this->connections[$room][$player])) {
            return;
        }
        unset($this->connections[$room][$player]);
        if (count($this->connections[$room]) == 0) {
            unset($this->connections[$room]);
        }
    }

    function getAllConnections($roomId) {
        $room = strval($roomId);
        return array_values($this->connections[$room]);
    }

    function getPlayerIdFromConnection($conn){
        return $this->connectionToPlayer[$conn][1];
    }

    function getGameIdFromConnection($conn){
        return $this->connectionToPlayer[$conn][0];
    }
    

    function getConnection($roomId, $playerId) {
        $room = strval($roomId);
        $player = strval($playerId);
        if (!isset($this->connections[$room]) || !isset($this->connections[$room][$player])) {
            return null;
        }
        return $this->connections[$room][$player];
    }

}