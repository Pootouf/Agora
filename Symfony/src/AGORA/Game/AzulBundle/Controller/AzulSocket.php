<?php

namespace AGORA\Game\AzulBundle\Controller;

use AGORA\Game\Socket\ConnectionStorage;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\DependencyInjection\Container;

class AzulSocket implements MessageComponentInterface {

    private $service;
    private $logService;
    private array $allFinish;

    public function __construct(Container $container)
    {
        $this->connectionStorage= new ConnectionStorage();
        $this->service = $container->get('agora_game.azul');
        $this->logService = $container->get('agora_game.azulLog');
        $this->allFinish = array();
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
        $this->logService->logInfo("AZULSOCKET|ONMESSAGE: Un message a été reçu de  {$from->resourceId} :  {$msg}");
        
        $content = json_decode($msg);

        if(strcmp($content->action,"connect") == 0){
            $this->connectionStorage->addConnection($content->gameId, $content->playerId, $from);
            // $this->service->joinPlayer($content->userId,$content->gameId);
            $this->service->assignFirstPlayerToken($content->gameId);
            foreach($this->service->getPlayers($content->gameId) as $player)  {
                $this->refreshAll($content->gameId, false, $player->getId());
            }
        } else if(strcmp($content->action,"piocher") == 0){
            if($this->service->isTheyTurn($content->gameId, $content->playerId)){
                $arrChar = explode("_", $content->source);
                $fromFabric = true;

                if(strcmp($content->source, "centre") == 0){
                    $fromFabric = false;
                    $arrChar[1] = "";
                } 
                $this->service->drawTile($content->gameId, $content->playerId,intval($arrChar[1])-1,$content->tuileColor, $content->nbTuiles, $fromFabric);
                    
                $this->refreshAll($content->gameId, false, -1);
            } else {
                $from->send(json_encode(array(
                    'action' => "not your turn",
                    'turn' =>  $this->service->getPlayerName($this->service->getGame($content->gameId)->getCurrentPlayerId(), $content->gameId)
                )));
            }

            
        } else if(strcmp($content->action,"poser") == 0){
            if($this->service->isTheyTurn($content->gameId, $content->playerId)){
                $arrChar = explode("_",$content->case);
                $this->service->putTile($content->gameId, $content->playerId,intval($arrChar[1])-1,strcmp($content->destination,"motif") ==0 );
                $this->refreshAll($content->gameId, true,$content->playerId);
            } else {
                $from->send(json_encode(array(
                    'action' => "not your turn",
                    'turn' =>  $this->service->getPlayerName($this->service->getGame($content->gameId)->getCurrentPlayerId(), $content->gameId)
                )));
            }
        } else if(strcmp($content->action, "actuFab") == 0){
            $this->service->actuFabric($content->gameId, $content->fab);
            $this->refreshAll($content->gameId,false, $content->playerId);
        } else if(strcmp($content->action, "fin_manche") == 0){
            $this->service->viderMotif($content->playerId, $content->gameId);
            $this->service->givePlayerPoint($content->playerId, $content->gameId, $content->score);
            $this->service->setCouvercle($content->couvercle, $content->gameId);
            $this->service->viderPlancher($content->playerId, $content->gameId);
            $this->service->updateWall($content->playerId, $content->gameId, $content->wall);
        } else if(strcmp($content->action, "fin") == 0){
            if(!array_search($content->playerId, $this->allFinish)){
                array_push($this->allFinish, $content->playerId);
                $this->service->givePlayerPoint($content->playerId, $content->gameId, $content->score);
            }
            $allConn = $this->connectionStorage->getAllConnections($content ->gameId);
            
            if(count($allConn) == count($this->allFinish)){
                $winning = $this->service->isWinner($content->playerId, $content->gameId);
                $winName = null;
                
                if($winning == $content->playerId){
                    $hasWin = true;
                    
                } else {
                    $hasWin = false;
                    $winName = $this->service ->getWinnerName($winning, $content->gameId);
                }
                $from->send(json_encode(array(
                    "action" => "fin",
                    "win" => $hasWin,
                    "winner" => $winName
                )));

                $this->service->endGame($content->gameId,$winning );
            } else {
                $from->send(json_encode(array(
                    "action" => "..."
                )));
            }

            
        }  else if (strcmp($content->action, "?") == 0) {
            $allConn = $this->connectionStorage->getAllConnections($content ->gameId);
            
            if(count($allConn) == count($this->allFinish)){
                $winning = $this->service->isWinner($content->playerId, $content->gameId);
                $winName = null;
                
                if($winning == 0){
                    $winnig = true;
                    
                } else {
                    $winnig = false;
                    $winName = $this->service ->getWinnerName($winnig, $content->gameId);
                }
                $from->send(json_encode(array(
                    "action" => "fin",
                    "win" => $winning,
                    "winner" => $winName
                )));
            } else {
                $from->send(json_encode(array(
                    "action" => "..."
                )));
            }
        }  else if(strcmp($content->action, "actuSac") == 0){
            
        } else {
            $from->send(json_encode(array('action' => "bad action : " . $content->action)));
        }
        //to add fin_manche, fin 
        
    }

    public function refreshAll($gameId, $endTurn, $refreshWho){
        // if ($refreshWho != $this->service->getPreviousPlayerId() && $this->service->getPreviousPlayerId() != NULL) 
        //     $this->refreshAll($gameId,$endTurn,$this->service->getPreviousPlayerId());
        $response = array();
        $azulGame = $this->service->getGame($gameId);
        $response['action'] = "actu";
        $response['previousTurn'] = $this->service->getPreviousPlayerName();
        $response['turn'] = $this->service->getPlayerName($azulGame->getCurrentPlayerId(), $gameId);
        if ($refreshWho == -1) {

            $currentPlayer = $this->service->getPlayerEntityAzulFromPlayerId($this->service->getCurrentPlayerId($gameId), $gameId);
            $response['refreshWho'] = $this->service->getPlayerName($currentPlayer->getId(),$gameId);
        } else {
            $response['refreshWho'] = $this->service->getPlayerName($refreshWho,$gameId);
        }
        $response['endTurn'] = $endTurn;
        $response['currentPlayerId'] = $azulGame->getCurrentPlayerId();
        $color = $this->service->getAllColor();
        //On récupère le centre
        $response['centre'] = array();

        $response['gameId'] = $gameId;
        $response['isGameFilled'] = $this->service->isGameFull($gameId);
        $response['players'] = $this->service->getPlayersNb($gameId);
        $response['capacity'] = $this->service->getGameCapacity($gameId);


        $center = $azulGame -> getCenter();

        $index = 0;
        foreach($color as $c){
            for($i = 0; $i < $center[$c]; $i++){
                
                $response['centre'][$index++] = $c;
            }
        }
        
        //On récupère les fabrics
        $fabrics = $azulGame->getFabrics();
        $response['fab'] = $fabrics;
        // var_dump("FABRICS");
        // var_dump($fabrics);
        // var_dump($this->service->getGame($gameId)->getFabrics());

        //$response['fab'] = array(); 
        //$this->service->print_txt("getFabrics()");
        //$this->service->print_array($fabrics);

        /*for($f = 0; $f < count($fabrics); $f++){
            $response['fab'][$f] = array();
            foreach($color as $c){
                // $this->service->print_txt("$c:");
                if(array_key_exists($c, $fabrics[$f])){
                    // $this->service->print_txt("$f:$c:");
                    // $this->service->print_txt($fabrics[$f][$c]);
                    // $this->service->print_txt("\n");
                    for($nbc = 0; $nbc < $fabrics[$f][$c]; $nbc++ ){
                        array_push($response['fab'][$f],$c);
                    }
                }
            }
        }*/


        //On récupère l'état du sac
        $response['sac'] = array();
        $sac = $azulGame->getReserve();
        foreach($color as $c){
            if(array_key_exists($c, $sac)){
              $response['sac'][$c] = $sac[$c]; 
            }
            
        }

        //On récupère l'état du couvrecle
        $response['couvercle'] = array();
        $couvercle = $azulGame -> getOutTile();
        foreach($color as $c){
            if(array_key_exists($c, $couvercle)){
                $response['couvercle'][$c] = $couvercle[$c];
            }
        }
        $allConn = $this->connectionStorage->getAllConnections($gameId);
        
        foreach($allConn as $conn){
            
            $connPlayer = $this->service->getPlayerEntityAzulFromPlayerId($this->connectionStorage->getPlayerIdFromConnection($conn), $gameId);
            $currentplayer = $this->service->getPlayerEntityAzulFromPlayerId($this->service->getCurrentPlayerId($gameId), $gameId);

            $player = $this->service->getPlayerEntityAzulFromPlayerId($refreshWho == -1 ? 
                ($currentplayer == NULL ? $connPlayer : $currentplayer):$refreshWho,$gameId);

            $response['first'] = $player->getFirstPlayerToken();
            

            // $this->service->print_txt("who -> $refreshWho (player:".$player->getId().":".$this->service->getPlayerName($player->getId(),$gameId).")\n");
            if($refreshWho == -1 || $refreshWho == $player->getId()){
                $response['for_me'] = true;
            } else {
                $response['for_me'] = false;
            }
        
            //On récupère le plancher
            $response['plancher'] = array();
            $playerPlancher = $player->getMalusTiles();
            foreach($color as $c){
                if(array_key_exists($c, $playerPlancher)){
                    for($i = 0; $i < $playerPlancher[$c]; $i++){
                        array_push($response['plancher'],$c);
                    }
                }
                
            }

            $response['motif'] = array();
            $motif = $player->getMotif();

            for($i = 0; $i < count($motif); $i++){
                $response['motif'][$i] = array();
                if(count($motif[$i]) > 0){
                    for($j = 0; $j < $motif[$i][1]; $j++){
                        array_push($response['motif'][$i],$motif[$i][0] );
                    }
                }
            }

            //var_dump("motif");
            //var_dump($response['motif']);
            
            //On récupère le score du joueurs
            $response['score'] = $player->getVictoryPointsScale();
            
            //On récupère le mur du joueur
            $response['mur'] = $player->getWall();
            //On récupère la selection du joueur
            $selected = $player->getSelectedTile();

            if(count($selected) != 0){
                $response['selected'] = $selected[0];
                $response['nbSelect'] = $selected[1];
            } else {
                $response['selected'] = "none";
            }

            $conn->send(json_encode($response));
        }


    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        $this->logService->logError("onError : <{$conn->resourceId}> Message={$e->getMessage()} File={$e->getFile()} Line={$e->getLine()} ");
        $conn->close();
    }

}

?>
