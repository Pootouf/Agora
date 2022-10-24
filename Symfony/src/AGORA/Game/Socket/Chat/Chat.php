<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 20/04/2018
 * Time: 15:44
 */

namespace AGORA\Game\Socket\Chat;


use AGORA\Game\Socket\Game;

class Chat extends Game {

    function handleAction($conn, $gameId, $playerId, $action) {
        echo $action . " from chat\n";

        if ($action === "user1") {
            $this->connectionStorage->addConnection(1, 1, $conn);
            echo "Connexion : " . $this->connectionStorage->getConnection(1, 1)->resourceId . "\n";
        } else if ($action === "user2") {
            $this->connectionStorage->addConnection(1, 2, $conn);
            echo "Connexion : " . $this->connectionStorage->getConnection(1, 2)->resourceId . "\n";

        } else {
            foreach($this->connectionStorage->getAllConnections(1) as $c) {
                $c->send($action);
            }
        }
    }
}