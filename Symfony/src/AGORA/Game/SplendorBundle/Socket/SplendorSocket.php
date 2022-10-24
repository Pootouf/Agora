<?php


namespace AGORA\Game\SplendorBundle\Socket;

use AGORA\Game\SplendorBundle\Service\SplendorLogService;
use AGORA\Game\SplendorBundle\Service\SplendorService;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class SplendorSocket implements MessageComponentInterface {

    protected $service;
    protected $clients;
    protected $logService;

    public function __construct(SplendorService $srvc, SplendorLogService $logger) {
        $this->service = $srvc;
        $this->clients = [];
        $this->logService = $logger;
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn) {
        $this->logService->logInfo("onOpen : nouvelle Connection {$conn->resourceId}");
        if (!in_array($conn, $this->clients)) {
            array_push($this->clients, $conn);
        }
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn) {
        $this->logService->logInfo("onClose : {$conn->resourceId} c'est deconnecté");

        $k = array_search($conn, $this->clients);
        array_splice($this->clients, $k, 1);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->logService->logError("onError : <{$conn->resourceId}> Message={$e->getMessage()} File={$e->getFile()} Line={$e->getLine()} ");
    }

    /**
     * Triggered when a client sends data through the socket
     * @param \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg) {

        $this->logService->logInfo("onMessage : <{$from->resourceId}> msg={$msg}");

        $content = json_decode($msg);

        switch ($content->type) {
            case "takeTokens":
                $gameId = intval($content->gameId);
                $userId = intval($content->userId);
                $tokens = $this->service->getTokens($gameId, $userId, explode(",", $content->tokens));

                if ($tokens !== null) {
                    $this->service->setLastMovePlayedForPlayer($userId, $gameId);
                    $tokensPlayer = $tokens[0]; $tokensGame = $tokens[1];
                    $data = array("type" => $content->type, "userId" => $content->userId, "tokensPlayer" => $tokensPlayer
                    , "tokensGame" => $tokensGame);
                    $data = json_encode($data);
                    foreach ($this->clients as $client) {
                        $client->send($data);
                    }
                }
                break;
            case "reserveCard":
                $gameId = intval($content->gameId);
                $userId = intval($content->userId);
                $tab = $this->service->reserveCard($gameId, $userId, intval($content->cardId));

                if ($tab !== null) {
                    $this->service->setLastMovePlayedForPlayer($userId, $gameId);
                    $data = array("type" => $content->type, "userId" => $content->userId, "oldCard" => $content->cardId
                        ,"newCard" => $tab[0], "joker" => $tab[1]);
                    $data = json_encode($data);
                    foreach ($this->clients as $client) {
                        $client->send($data);
                    }
                }
                break;
            case "buyCard":
                $gameId = intval($content->gameId);
                $userId = intval($content->userId);
                $tab = $this->service->buyCard($gameId, $userId, intval($content->cardId));
                
                if ($tab !== null) {
                    $this->service->setLastMovePlayedForPlayer($userId, $gameId);
                    $data = array("type" => $content->type, "userId" => $content->userId, "oldCard" => $content->cardId
                        ,"newCard" => $tab[0], "tokens" => $tab[1], "prestige" => $tab[2], 'gameTokens' => $tab[3]);
                    $data = json_encode($data);
                    
                    foreach ($this->clients as $client) {
                        $client->send($data);
                    }
                }
                break;
            case "canBuyCard":
                $gameId = intval($content->gameId);
                $userId = intval($content->userId);
                $res = $this->service->canBuyCard($gameId, $userId, intval($content->cardId));
                if ($res !== null) {
                    $data = array("type" => $content->type, "userId" => $content->userId, "cardId" => $content->cardId
                        ,"canBuyCard" => $res);
                    $data = json_encode($data);
                    
                    // foreach ($this->clients as $client) {
                        $from->send($data);
                    // }
                    
                }
                break;
            case "reserveRandomCard":
                $gameId = intval($content->gameId);
                $userId = intval($content->userId);

                $card = $this->service->getRandomCard($gameId, intval($content->level));
                $this->service->addHiddenCard($gameId, $userId, intval($card));
                $tab = $this->service->reserveCard($gameId, $userId, intval($card));
                if ($tab !== null) {
                    $this->service->setLastMovePlayedForPlayer($userId, $gameId);
                    $data = array("type" => $content->type, "userId" => $content->userId, "oldCard" => $card
                    , "joker" => $tab[1]);
                    $data = json_encode($data);
                    foreach ($this->clients as $client) {
                        $client->send($data);
                    }
                }
                break;
            case "canVisitNoble":
                $nobles = $this->service->canVisitNoble(intval($content->gameId), intval($content->userId));
                if ($nobles !== null) {
                    if (count($nobles) == 1) {
                        $prestige = $this->service->visitNoble($content->gameId, $content->userId, $nobles[0]);
                        if ($prestige != null) {
                            $data = array("type" => "visitNoble", "userId" => $content->userId
                            , "nobleId" => $nobles[0], "prestige" => $prestige);
                            $data = json_encode($data);
                            foreach ($this->clients as $client) {
                                $client->send($data);
                            }
                        }
                    } else if (count($nobles) > 1) {
                        $data = array("type" => $content->type, "nobles" => implode(",", $nobles));
                        $data = json_encode($data);
                        $from->send($data);
                    } else {
                        $end = $this->service->endTurn($content->gameId, $content->userId);
                        if ($end !== null) {
                            if (count($end) > 2) {
                                $data = array("type" => "moreTenToken", "userId" => $content->userId, "tokens" => $end[1]
                                , "total" => $end[2]);
                                $data = json_encode($data);
                                $from->send($data);
                            } else if ($end[0] === false) {
                                $data = array("type" => "endTurn", "userId" => $content->userId, "next" => $end[1]);
                                $data = json_encode($data);
                                foreach ($this->clients as $client) {
                                    $client->send($data);
                                }
                            } else {
                                $data = array("type" => "gameWin", "userId" => $content->userId, "winner" => $end[1]);
                                $data = json_encode($data);
                                foreach ($this->clients as $client) {
                                    $client->send($data);
                                }
                            }
                        }
                    }
                }
                break;
            case "visitNoble":
                $prestige = $this->service->visitNoble($content->gameId, $content->userId, $content->nobleId);
                if ($prestige !== null) {
                    $data = array("type" => "visitNoble", "userId" => $content->userId
                        , "nobleId" => $content->nobleId, "prestige" => $prestige);
                    $data = json_encode($data);
                    foreach ($this->clients as $client) {
                        $client->send($data);
                    }
                }
                break;
            case "endTurn":
                $end = $this->service->endTurn($content->gameId, $content->userId);
                if ($end !== null) {
                    if (count($end) > 2) {
                        $data = array("type" => "moreTenToken", "userId" => $content->userId, "tokens" => $end[1]
                            , "total" => $end[2]);
                        $data = json_encode($data);
                        $from->send($data);
                    } else if ($end[0] === false) {
                        $data = array("type" => "endTurn", "userId" => $content->userId, "next" => $end[1]);
                        $data = json_encode($data);
                        foreach ($this->clients as $client) {
                            $client->send($data);
                        }
                    } else {
                        $data = array("type" => "gameWin", "userId" => $content->userId, "winner" => $end[1]);
                        $data = json_encode($data);
                        foreach ($this->clients as $client) {
                            $client->send($data);
                        }
                    }
                }
                break;
            case "removeTokens":
                $tokens = $this->service->removeTokens($content->gameId, $content->userId, explode(",", $content->tokens));
                if ($tokens != null) {
                    $this->service->setLastMovePlayedForPlayer($content->userId, $content->gameId);
                    $data = array("type" => $content->type, "userId" => $content->userId, "tokens" => $tokens);
                    $data = json_encode($data);
                    foreach ($this->clients as $client) {
                        $client->send($data);
                    }
                }
                break;

        }

    }

}