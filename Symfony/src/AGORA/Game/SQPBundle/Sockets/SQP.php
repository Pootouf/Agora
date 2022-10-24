<?php

namespace AGORA\Game\SQPBundle\Sockets;

use AGORA\Game\SQPBundle\Model\SQPAPI;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use AGORA\Game\SQPBundle\Service\SqpLogService;

class SQP implements MessageComponentInterface {
    protected $clients;
    protected $container;
    protected $sqpapi;
    //Association partie(gameId) -> nombre de joueurs
    protected $nbPlayersPerGame;
    //Double association partie(gameId) -> joueur(playerId) -> socket
    protected $playersPerGame;
    protected $tchatPerGame;
    protected $logService;

    public function __construct(SQPAPI $sqp, SqpLogService $sqpLog) {
        $this->sqpapi = $sqp;
        $this->clients = new \SplObjectStorage;
        $this->nbPlayersPerGame = array();
        $this->playersPerGame = array();
        $this->tchatPerGame = array();
        $this->logService = $sqpLog;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        $this->logService->logInfo("onOpen : nouvelle Connection {$conn->resourceId}");
    }

    //Le coeur du comportement du serveur
    public function onMessage(ConnectionInterface $from, $msg) {
       
        $this->logService->logInfo("onMessage : <{$from->resourceId}> msg={$msg}");
        
        $content = json_decode($msg);
        //Quand un joueur se connecte
        if ($content->type == "ready") {
            //On incrémente le nombre de joueurs
            if (isset($this->nbPlayersPerGame[''.$content->gameId])) {
                $this->nbPlayersPerGame[''.$content->gameId] += 1;

                $this->logService->logInfo("onMessage : Players for game ".$content->gameId." are ".$this->nbPlayersPerGame[''.$content->gameId]);
            } else {
                $this->nbPlayersPerGame[''.$content->gameId] = 1;
                $this->logService->logInfo("onMessage : Players for game ".$content->gameId." are ".$this->nbPlayersPerGame[''.$content->gameId]);
            }
            //On associe à la partie le nouveau joueur et au joueur sa socket
            if (!isset($this->playersPerGame[''.$content->gameId])) {
                $this->playersPerGame[''.$content->gameId] = array();
            }

            $this->playersPerGame[''.$content->gameId][''.$content->playerId] = $from;
            //On envoie l'historique du tchat (les 20 derniers messages)
            if (!isset($this->tchatPerGame[''.$content->gameId])) {
                $this->tchatPerGame[''.$content->gameId] = array();
            }
            $ret = array('type' => "tchatLoad", 'tchat' => $this->tchatPerGame[''.$content->gameId]);
            $ret = json_encode($ret);
            $from->send($ret);

            $this->logService->logInfo("PlayerId : " . $content->playerId . " is ready !");

            //Si la partie est prête à commencer
            if ($this->sqpapi->isReadyToBegin($content->gameId, $this->nbPlayersPerGame[''.$content->gameId])) {
                $this->logService->logInfo("onMessage : SQPGame ready to begin !");
                $players = $this->sqpapi->getPlayersFromLobbyInOrder($content->gameId);
                $ret = array('type' => "begin", 'players' => $players);
                $ret = json_encode($ret);
                foreach ($this->playersPerGame[''.$content->gameId] as $client) {
                    $this->logService->logInfo("onMessage : Informing person ".$client->resourceId);
                    $client->send($ret);
                }
            } else {
                $players = $this->sqpapi->getPlayersFromLobbyInOrder($content->gameId);
                $ret = array('type' => "newPlayer", 'players' => $players);
                $ret = json_encode($ret);
                foreach ($this->playersPerGame[''.$content->gameId] as $client) {
                    if ($client != $from) {
                        $this->logService->logInfo("onMessage : Informing person ".$client->resourceId." of new arrival");
                        $client->send($ret);
                    }
                }
            }
        //Si l'host envoie le signal de lancement de la partie
        } else if ($content->type == "begin") {
            $this->logService->logInfo("onMessage : Début de la partie");
            $this->sqpapi->setupBoard($content->gameId);
            $hands = $this->sqpapi->distributeToEveryone($content->gameId);
            $players = $this->sqpapi->getPlayersFromLobbyInOrder($content->gameId);
            $board = $this->sqpapi->getBoard($content->gameId);
            foreach ($hands as $key => $hand) {
                $ret = array('type' => "refreshAll", 'hand' => $hand, 'players' => $players, 'board' => $board);
                $ret = json_encode($ret);
                $this->playersPerGame[$content->gameId][$key]->send($ret);
            }
        //Lorsqu'un joueur veut déposer une carte sur le plateau
        } else if ($content->type == "card") {
            $this->logService->logInfo("onMessage : SQPPlayer ".$content->playerId." of SQPGame ".$content->gameId." wants to place the card ".$content->card." in the row ".$content->row);;
            $board = ";;;;";
            $turn = 0;
            $hand = $this->sqpapi->getHand($content->playerId);
            $msg ="";
            $board = $this->sqpapi->addCardToBoard($content->playerId, $content->gameId, $content->card, $content->row, $msg);
            //Si une erreur survient lors du placement de la carte
            if ($board == false) {
                 $this->sqpapi->addCardToHand($content->playerId, $content->card);
                 $ret = array('type' => "error", 'msg' => "Vous ne pouvez pas placer cette carte !");
                 $ret = json_encode($ret);
                 $from->send($ret);
            //Si le joueur doit se prendre une ligne
            } else if ($msg == "takerowneeded") {
                $board = $this->sqpapi->takeRow($content->gameId, $content->playerId, $content->row);
            }
            $handAsArray = preg_split("/,/",$hand, -1, PREG_SPLIT_NO_EMPTY);
            //Si la main du joueur est vide après avoir joué
            if (count($handAsArray) == 0) {
                //On check si c'est la fin de la partie
                if ($this->sqpapi->checkEndGame($content->gameId, $content->playerId)) {
                    $ret = array('type' => "END", 'players' => $this->sqpapi->getPlayersFromLobbyInOrder($content->gameId));
                    $ret = json_encode($ret);
                    foreach ($this->playersPerGame[''.$content->gameId] as $conn) {
                        $conn->send($ret);
                    }
                    $ret = array('type' => "refreshHand", 'hand' => $hand);
                    $ret = json_encode($ret);
                    $from->send($ret);
                    $ret = array('type' => "refreshBoard", 'board' => $board, 'turn' => $turn, 'justPlayed' => $content->playerId,
                        'players' => $this->sqpapi->getPlayersFromLobbyInOrder($content->gameId));
                    $ret = json_encode($ret);
                    foreach ($this->playersPerGame[''.$content->gameId] as $conn) {
                        $conn->send($ret);
                    }

                    $this->sqpapi->endGame($content->gameId);

                    return;
                //On check si c'est le dernier tour
                } else if ($this->sqpapi->checkLastPlayer($content->gameId,$content->playerId)) {
                    $this->logService->logInfo("onMessage : End of round");;
                    $this->sqpapi->resetDeckAndBoard($content->gameId);
                    $board = $this->sqpapi->setupBoard($content->gameId);
                    $this->sqpapi->distributeToEveryone($content->gameId);
                }
            }
            //Incrémentation du tour de jeu
            $turn = $this->sqpapi->increaseOrderTurn($content->gameId, $content->playerId);
            $this->logService->logInfo("onMessage : Card added to board !");
            $ret = array('type' => "refreshHand", 'hand' => $hand);
            $ret = json_encode($ret);
            $from->send($ret);
            $ret = array('type' => "refreshBoard", 'board' => $board, 'turn' => $turn, 'justPlayed' => $content->playerId,
                'players' => $this->sqpapi->getPlayersFromLobbyInOrder($content->gameId));
            $ret = json_encode($ret);
            foreach ($this->playersPerGame[''.$content->gameId] as $conn) {
                $conn->send($ret);
            }
        //Un joueur séléectionne une carte
        } else if ($content->type == "readyCard") {
            $this->sqpapi->readyCard($content->playerId, $content->card);
            if ($this->sqpapi->arePlayersCardReady($content->gameId)) {
                $this->sqpapi->setOrderTurn($content->gameId);
                $ret = array('type' => "READY",
                    'players' => $this->sqpapi->getPlayersFromLobbyInOrder($content->gameId));
                $ret  = json_encode($ret);
                foreach ($this->playersPerGame[''.$content->gameId] as $playerId => $client) {
                    $client->send($ret);
                }
            } else {
                $ret = array('type' => "readyCard", 'playerId' => $content->playerId);
                $ret = json_encode($ret);
                foreach ($this->playersPerGame[''.$content->gameId] as $client) {
                    if ($client != $from) {
                        $client->send($ret);
                    }
                }
            }
        } else if ($content->type == "message") {
            $this->logService->logInfo("onMessage : Message received by $content->username : $content->message Sending it right now");
            //On sauvegarde le message dans la socket
            $countMsg = count($this->tchatPerGame[''.$content->gameId]);
            if ($countMsg == 20) {
                array_shift($this->tchatPerGame[''.$content->gameId]);
                --$countMsg;
            }
            $this->tchatPerGame[''.$content->gameId][$countMsg] = $msg;
            //On envoie le message à tout le monde
            foreach ($this->playersPerGame[''.$content->gameId] as $playerId => $client) {
                if ($client != $from) {
                    $client->send($msg);
                }
            }
        }
    }

    //Lorsqu'un joueur quitte la partie, on décrémente le nombre de joueurs connectés
    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        foreach ($this->playersPerGame as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if ($value2 == $conn) {
                    unset($this->playersPerGame[''.$key][''.$key2]);
                    $this->nbPlayersPerGame[''.$key] -= 1;

                    $this->logService->logInfo("onClose : Players for game $key are ".$this->nbPlayersPerGame[''.$key]);
                }
            }
        }

        $this->logService->logInfo("onClose : {$conn->resourceId} c'est deconnecté");
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $this->logService->logError("onError : <{$conn->resourceId}> Message={$e->getMessage()} File={$e->getFile()} Line={$e->getLine()} ");
        $conn->close();
    }
}
