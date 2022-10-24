<?php

namespace AGORA\Game\Puissance4Bundle\Controller;

use AGORA\Game\Socket\ConnectionStorage;
use Symfony\Component\DependencyInjection\Container;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Puissance4Socket implements MessageComponentInterface {
	/**
     * Contiendra les utilisateurs connectés.
     */
    protected $connectionStorage;
    protected $service;
    protected $logService;

    function __construct(Container $container) {
        $this->connectionStorage = new ConnectionStorage();
        $this->service = $container->get('agora_game.puissance4');
        $this->logService = $container->get('agora_game.puissance4Log');
    }

    /**
     * Méthode appelée lorsqu'un utilisateur se connecte sur la WebSocket.
     */
    function onOpen(ConnectionInterface $conn) {
        $this->logService->logInfo("onOpen : nouvelle Connection {$conn->resourceId}");
    }

    /**
     * Méthode appelée lorsqu'un navigateur envoie un message à la webSocket.
     */
    public function onMessage(ConnectionInterface $from, $msg) {
        
        $this->logService->logInfo("onMessage : <{$from->resourceId}> msg={$msg}");

        $content = json_decode($msg);
        
        if ($content->type == "connect") {
            $gameId = $content->gameId;
            $playerId = $content->playerId;
            $this->connectionStorage->addConnection($gameId, $playerId, $from);
            $this->resfreshPlayers($gameId);
        } else if($content->type == "jouer"){
            $playerIdFrom = $this->connectionStorage->getPlayerIdFromConnection($from);
            $gameId = $this->connectionStorage->getGameIdFromConnection($from);

            if ($this->service->getGame($gameId) == null) {
                return;
            }

            //TODO : voir si il est bien enregistré dans le connectionStorage lié à gameID

            //VERIFIER si c'est bien le tour du joueur qui envoie le message.
            if ($playerIdFrom == $this->service->getCurrentPlayerID($gameId)) {
                $symbole = $this->service->getPlayerEntityPuissance4FromPlayerId($playerIdFrom, $gameId)->getSymbole();

                $this->service->jouerCase($content->column, $gameId, $playerIdFrom, $symbole);
                $this->resfreshPlayers($gameId);

                $game = $this->service->getPuissance4GameFromGame($gameId);
                if ($game->getState() == "finished") {
                    $board = $this->service->getGame($gameId)->getBoard();
                    if ($this->service->boardComplete($board)) { $equality = true; } else { $equality = false; }

                    $players = $this->service->getPlayers($gameId);
                    $winner = $equality ? null : $this->service->getCurrentPlayer($gameId);
                    $this->service->endGame($players, $gameId, $winner);
                }
            }   
        } else if($content->type == "kick") {
            $gameId = $this->connectionStorage->getGameIdFromConnection($from);
            $player = $content->playerId;
            if ($this->service->getGame($gameId) == null) {
                return;
            }
            $TableauEnvoye = $this->service->kickPlayer($gameId, $player);
            
            foreach ($this->connectionStorage->getAllConnections($gameId) as $c) {
                $Tab = $TableauEnvoye;
                if ($TableauEnvoye["action"] == 'begin' || $TableauEnvoye["action"] == 'win') {
                    $playerId = $this->connectionStorage->getPlayerIdFromConnection($c);
                    $playerSymbol = $this->service->getPlayerEntityPuissance4FromPlayerId($playerId, $gameId)->getSymbole();
                    $yourTurn = ($playerId == $this->service->getCurrentPlayerID($gameId));
        
                    //On complète le tableau des données avec un booléen indiquant si c'est le tour du joueur ou non.
                    $Tab = $Tab + [
                        'votreTour' =>  $yourTurn,
                        'playerSymbol' => $playerSymbol
                    ];
                    $c->send(json_encode($Tab));
                } else if ($TableauEnvoye != null || $TableauEnvoye["action"] == 'kick') {
                    $playerId = $this->connectionStorage->getPlayerIdFromConnection($c);
                    if ($playerId == $player) {
                        $playerSymbol = $this->service->getPlayerEntityPuissance4FromPlayerId($playerId, $gameId)->getSymbole();
                        $yourTurn = ($playerId == $this->service->getCurrentPlayerID($gameId));
            
                        //On complète le tableau des données avec un booléen indiquant si c'est le tour du joueur ou non.
                        $Tab = $Tab + [
                            'votreTour' =>  $yourTurn,
                            'playerSymbol' => $playerSymbol
                        ];
                        $c->send(json_encode($Tab));
                        break;
                    }
                }
            }
            if ($TableauEnvoye["action"] == 'win') {
                // On déclenche la MAJ du leaderBoard et on termine la partie.
                $players = $this->service->getPlayers($gameId);
                $winner = $this->service->getCurrentPlayer($gameId);
                $this->service->endGame($players, $gameId, $winner);
            }
        }
    }

    private function resfreshPlayers($gameId){
        $TableauEnvoye = $this->service->getArrayForRefreshDatas($gameId);

        foreach ($this->connectionStorage->getAllConnections($gameId) as $c) {
            $playerId = $this->connectionStorage->getPlayerIdFromConnection($c);
            $playerSymbol = $this->service->getPlayerEntityPuissance4FromPlayerId($playerId, $gameId)->getSymbole();
            $yourTurn = ($playerId == $this->service->getCurrentPlayerID($gameId));

            //On complète le tableau des données avec un booléen indiquant si c'est le tour du joueur ou non.
            $Tab = $TableauEnvoye + [
                'votreTour' =>  $yourTurn,
                'playerSymbol' => $playerSymbol
            ];
            
            $c->send(json_encode($Tab));
        }
    }

    /**
     * Méthode appelée lorsqu'un utilisateur se déconnecte.
     */
    public function onClose(ConnectionInterface $conn) {
        $this->logService->logInfo("onClose : {$conn->resourceId} c'est deconnecté");
    }


    /**
     * Méthode appelée en cas d'erreur sur la WebSocket.
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        $this->logService->logError("onError : <{$conn->resourceId}> Message={$e->getMessage()} File={$e->getFile()} Line={$e->getLine()} ");
        $conn->close();
    }
}
