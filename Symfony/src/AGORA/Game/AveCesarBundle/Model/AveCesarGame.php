<?php

/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 21/04/18
 * Time: 11:54
 */

namespace AGORA\Game\AveCesarBundle\Model;

use AGORA\Game\AveCesarBundle\Service\AveCesarService;
use AGORA\Game\Socket\Game;

class AveCesarGame extends Game
{

    private $tchatperGame = array();

    /**
     * @var AveCesarTurnResolver
     */
    private $turnResolver;

    /**
     * @var AveCesarService
     */
    private $service;

    public function __construct(AveCesarService $service)
    {
        parent::__construct();
        $this->turnResolver = new AveCesarTurnResolver($service);
        $this->service = $service;
    }

    public function handleAction($conn, $gameId, $playerId, $action)
    {
        if ($action->type == "connect") {
            $this->connectionStorage->addConnection($gameId, $playerId, $conn);
            $connections = $this->connectionStorage->getAllConnections($gameId);
            $message = array(
                'type' => 'connect',
                'playerId' => $playerId,
                'username' => $this->service->getPlayerName($playerId),
                'position' => $this->service->getPlayer($gameId, $playerId)->getPosition()
            );
            foreach ($connections as $c) {
                $c->send(json_encode($message));
            }
            $service = $this->service;
            if ($service->getMaxPlayer($gameId) == count($service->getAllPlayers($gameId)) && $service->getGameState($gameId) == "waiting") {
                $service->setGameState($gameId, "started");
                $message = array(
                    'type' => 'start',
                );
                foreach ($connections as $c) {
                    $c->send(json_encode($message));
                }
            }

            //On envoie l'historique du tchat (les 20 derniers messages)
            if (!isset($this->tchatPerGame['' . $gameId])) {
                $this->tchatPerGame['' . $gameId] = array();
            }
            $ret = array('type' => "tchatLoad", 'tchat' => $this->tchatPerGame['' . $gameId]);
            $ret = json_encode($ret);
            $conn->send($ret);
        } else if ($action->type == "move") {
            if (!isset($action->card)) {
                $message = array(
                    'type' => 'error',
                    'message' => 'mouvement impossible carte inconnue'
                );
                $conn->send(json_encode($message));
                return;
            }
            if (($card = $this->turnResolver->move($gameId, $playerId, $action->position, $action->card)) != -1) {
                $this->service->setLastMovePlayedForPlayer($playerId);
                if ($card != 0) {
                    $message = array(
                        'type' => 'card',
                        'card' => $card
                    );
                    $conn->send(json_encode($message));
                }
                $connections = $this->connectionStorage->getAllConnections($gameId);
                $message = array(
                    'type' => 'move',
                    'playerId' => $playerId,
                    'position' => $action->position,
                    'nextPlayer' => $this->turnResolver->getNextPlayer($gameId),
                    'firstPlayer' => $this->service->getFirstPlayer($gameId)->getId(),
                    'endPlayer' => $this->service->isFinishPlayer($gameId, $playerId),
                    'cesar' => $this->service->isCesar($gameId, $playerId),
                    'lap' => $this->service->getlap($gameId, $playerId)
                );
                foreach ($connections as $c) {
                    $c->send(json_encode($message));
                }
                if ($this->service->isFinishGame($gameId)) {
                    $message = array(
                        'type' => 'finished',
                        'ranking' => $this->service->getRanking($gameId),
                    );
                    foreach ($connections as $c) {
                        $c->send(json_encode($message));
                    }
                    $this->service->finishGame($gameId);
                }
            } else {
                $message = array(
                    'type' => 'moveError',
                    'message' => 'mouvement impossible avec cette carte et cette position',
                    'position' => $action->position
                );
                $conn->send(json_encode($message));
            }
        } else if ($action->type == "pass") {
            if ($this->turnResolver->pass($gameId, $playerId) == -1) {
                $message = array(
                    'type' => 'error',
                    'message' => 'impossible de passer votre tour'
                );
                $conn->send(json_encode($message));
                return;
            }
            $this->service->setLastMovePlayedForPlayer($playerId);
            $connections = $this->connectionStorage->getAllConnections($gameId);
            $message = array(
                'type' => 'pass',
                'playerId' => $playerId,
                'nextPlayer' => $this->turnResolver->getNextPlayer($gameId)
            );
            foreach ($connections as $c) {
                $c->send(json_encode($message));
            }
        } else if ($action->type == "message") {
            $connections = $this->connectionStorage->getAllConnections($gameId);
            //On sauvegarde le message dans la socket
            $countMsg = count($this->tchatPerGame['' . $gameId]);
            if ($countMsg == 20) {
                array_shift($this->tchatPerGame['' . $gameId]);
                --$countMsg;
            }
            $stockMess = array(
                'type' => "message",
                'gameId' => $gameId,
                'playerId' => $playerId,
                'username' => $action->username,
                'message' => $action->message
            );
            $this->tchatPerGame['' . $gameId][$countMsg] = $stockMess;
            //On envoie le message à tout le monde
            $toSend = json_encode($stockMess);
            foreach ($connections as $playerId => $client) {
                if ($client != $conn) {
                    $client->send($toSend);
                }
            }
        }
    }
}
