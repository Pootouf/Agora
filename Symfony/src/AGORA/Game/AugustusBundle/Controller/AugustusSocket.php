<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 20/04/2018
 * Time: 15:18
 */

namespace AGORA\Game\AugustusBundle\Controller;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\DependencyInjection\Container;

class AugustusSocket implements MessageComponentInterface {


    private $container;
    private $controller;    
    protected $logService;

    public function __construct(Container $container) {
        $this->container = $container;

        $this->controller = $this->container->get('agora_game.augustusController');
        $this->controller->setContainer($container);
        $this->logService = $container->get('agora_game.augustusLog');
    }


    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn) {
        $this->logService->logInfo("onOpen : nouvelle Connection {$conn->resourceId}");
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     *
     */
    function onClose(ConnectionInterface $conn) {
        $this->logService->logInfo("onClose : {$conn->resourceId} c'est deconnecté");
   }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e) {
        $this->logService->logError("onError : <{$conn->resourceId}> Message={$e->getMessage()} File={$e->getFile()} Line={$e->getLine()} ");
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg) {
        
        $this->logService->logInfo("onMessage : <{$from->resourceId}>  message={$msg}");
        
        $content = json_decode($msg);
        $this->controller->handleAction($from, $content->gameId, $content->playerId, $content->action);
    }
}