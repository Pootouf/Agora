<?php

namespace AGORA\Game\RRBundle\Controller;

use AGORA\Game\RRBundle\Model\RailwayType;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\DependencyInjection\Container;

class RRSocket implements MessageComponentInterface {

    private $service;
    private $logService;

    public function __construct(Container $container)
    {
        $this->service = $container->get('agora_game.rr');
        $this->logService = $container->get('agora_game.rrLog');
        $this->logService->logInfo("RRSOCKET|CONSTRUCT: NEW SERVICE");
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->logService->logInfo("onOpen : nouvelle Connection {$conn->resourceId}");
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->logService->logInfo("onClose : {$conn->resourceId} c'est deconnecté");
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $this->logService->logInfo("RRSOCKET|ONMESSAGE: Un message a été reçu de  {$from->resourceId} :  {$msg}");
        $content = json_decode($msg);

        //CHANGER LE TOUT POUR QU'ON ENVOIE JUSTE LE ID ET LE SERVICE FAIT LE RESTE
        if($content->type == "jouer"){
            if($content->action=='rail'){

                $details = array('railway' => -1, 'rail' => -1 , 'pos' => -1, 'nb' => $content->nb);
                
                switch($content->line){
                    case 'stPeter': 
                        $details['railway'] = RailwayType::stPetersburg;
                    break;
                    case 'kiev':
                        $details['railway'] = RailwayType::kiev;
                    break;
                    case 'trans':
                        $details['railway'] = RailwayType::transsiberian;
                    break;
                    default:
                        $details['railway'] = -273;
                } 

                foreach($this->service->getActions($content->gameId) as $action){
                    $this->logService->logInfo("RRSOCKET|DEBUG : {$content->actionId} == {$action->getId()}");
                }
                
                if(($result = $this->service->play($content->gameId, $content->playerId, $content->actionId, $details)) == 0){

                    $actionInfo = array();
                    $actionReserved = array();

                    foreach($this->service->getGameBoard()->getActionsArray() as $action){
                        $actionInfo[$action->getId()] = $action->getIdentification();
                        $actionReserved[$action->getId()] = $action->isReserved();
                    }


                    $toSend = array(
                        'type'=> 'refresh',
                        'line' => $content->line,
                        'lineContent' => $this->service->getPlayer($content->playerId)->getRailways()[$details['railway']]->getRailSlots(),
                        'actionReserved' => $actionReserved,
                        'actionInfo' => $actionInfo,
                        'playerId' => $content->playerId
                    // 'board' => $this->service->getGameBoard(),
                    // 'actions' => $this->service->getGameBoard() -> getActionsArray(),
                    );

                    $action = $this->service->getGameBoard()->getAction($content->actionId);

                    if($content->nb == -273){
                        //c'est la premier fois qu'on utiliser l'action
                        $toSend['nb'] = $action->getDetails()['nb']-1;
                        $toSend['actionType'] = $content->action;
                        $toSend['actionId'] = $content->actionId;
                    } else if(($content->nb) -1 <= 0){
                        //Il ne reste plus d'action dispo
                        $toSend['nb'] = 0;
                    } else {
                        //Il reste des actions dispo
                        $toSend['nb'] = ($content->nb) -1;
                        $toSend['actionType'] = $content->action;
                        $toSend['actionId'] = $content->actionId;
                    }

                    $json = json_encode($toSend);
                    $this->logService->logInfo("RRSOCKET: JSON: {$json}");
                    $from->send($json);
                } else {
                    $this->logService->logInfo("RRSOCKET:NO-SEND : Mauvaise action = {$result}" );
                }
                
            }
            
        }
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        $this->logService->logError("onError : <{$conn->resourceId}> Message={$e->getMessage()} File={$e->getFile()} Line={$e->getLine()} ");
        $conn->close();
    }

}

?>
