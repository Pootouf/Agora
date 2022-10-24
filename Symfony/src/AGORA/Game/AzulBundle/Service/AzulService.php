<?php

namespace AGORA\Game\AzulBundle\Service;

use AGORA\Game\AzulBundle\Entity\AzulGame;
use AGORA\Game\GameBundle\Service\GameService;
use Doctrine\ORM\EntityManager;
use AGORA\Game\AzulBundle\Entity\AzulPlayer;
use AGORA\PlatformBundle\Entity\Leaderboard;
use AGORA\Game\GameBundle\Entity\Game;
use \DateTime;
use \DateInterval;

/**
 * Cette classe permet de faire le lien entre le controlleur et le modèle.
 * 
 */
class AzulService{
    private $bDDManager;
    private $gameService;
    private $gameInfo;

    private $fabricLength = 4;
    private $emptyFabrics = array("red" => 0, "blue" => 0, "orange" => 0, "black" => 0, "cyan" => 0);

    private $previousPlayerName = "";
    private $previousPlayerId;
    
    /**
     * Constructeur. L'objet EntityManager (gestion de la BDD) est donné en paramètre.
     */
    public function __construct(EntityManager $em, GameService $gs)
    {
        $this -> bDDManager = $em;
        $this -> gameService = $gs;
        $this->gameInfo = $this->bDDManager->getRepository('AGORAPlatformBundle:GameInfo')->findOneBy(array('gameCode' => "azul"));
    }

    /**
     * Fonction permettant de créer une partie.
     * DONE
     */
    public function createAzulGame($name, $host, $nbPlayer) {
        $azulGame = new AzulGame();

        $allFabrics = array();
        for($i = 0; $i < 5 ; $i++){
            //$coppiedArray = array();
            $coppiedArray = $this->createFabric($azulGame);
            array_push($allFabrics, $coppiedArray);
        }
        
        if($nbPlayer > 2 ){
            //$coppiedArray = array();
            $coppiedArray = $this->createFabric($azulGame);
            array_push($allFabrics, $coppiedArray);
            //$coppiedArray = array();
            $coppiedArray = $this->createFabric($azulGame);
            array_push($allFabrics, $coppiedArray);
        }
        if($nbPlayer > 3){
            //$coppiedArray = array();
            $coppiedArray = $this->createFabric($azulGame);
            array_push($allFabrics, $coppiedArray);
            //$coppiedArray = array();
            $coppiedArray = $this->createFabric($azulGame);
            array_push($allFabrics, $coppiedArray);
        }

        $azulGame->setFabrics($allFabrics);
        $center = $this->emptyFabrics;
        $center["first"] = 0;
        $azulGame->setCenter($center);
        $azulGame->setOutTile($this->emptyFabrics);

        $this->bDDManager->persist($azulGame);
        $this->bDDManager->flush();

        $game = new Game();

        $game->setGameId($azulGame->getId());
        $game->setHostId($host);
        $game->setGameName($name);
        $game->setPlayersNb($nbPlayer);
        $game->setState("waiting");
        $game->setCreationDate(new \DateTime("now"));
        $game->setGameInfoId($this->gameInfo);

        $this->bDDManager->persist($game);
        $this->bDDManager->flush();

        return $azulGame->getId();

    }   

    private function createFabric($game) {
        $bag = array();
        $fabric = array();
        $reserve = $game->getReserve();
        foreach ($reserve as $color => $number){
            for ($n = 0; $n < $number; $n++) {
                array_push($bag,$color);
            }
        }
        for ($n = 0; $n < $this->fabricLength; $n++) {
            $color = $bag[random_int(0,count($bag)-1)];
            array_push($fabric,$color);
            $reserve[$color] -= 1;
        }
        
        return $fabric;
    }

    /**
     * Méthode permettant de mettre a jour les fabriques
     * Cette méthode n'est appeler que lors de la fin de manche
     */
    public function actuFabric($gameId, $fabrics){
        $game = $this->getGame($gameId);

        $game->setFabrics($fabrics);

        $this->bDDManager->persist($game);
        $this->bDDManager->flush();

        $game->setCurrentPlayerId(-$game->getCurrentPlayerId());

        $this->nextPlayer($gameId);

        return $game;
    }

    public function isGameFull($gameId) {
        $players = $this->bDDManager->getRepository('AGORAGameAzulBundle:AzulPlayer')->findBy(array('gameId' => $gameId));
        return $this->getPlayersNb($gameId) >= $this->getGameCapacity($gameId);
    }

    public function getPlayersNb($gameId) {
        $players = $this->bDDManager->getRepository('AGORAGameAzulBundle:AzulPlayer')->findBy(array('gameId' => $gameId));
        return count($players);
    }

    public function getGameCapacity($gameId) {
        $rooms = $this->bDDManager->getRepository('AGORAGameGameBundle:Game');
        $room = $rooms->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
        $game = $this->bDDManager->getRepository('AGORAGameGameBundle:Game')->findOneBy((array('gameId' => $gameId)));
        return $game->getPlayersNb();
    }

    /**
     * Cette fonction permet a un joueur de rejoindre une partie.
     * DONE
     */
    public function joinPlayer($userId, $gameId){
        //On récupère le joueur a ajouter
        $user = $this->bDDManager->getRepository("AGORAUserBundle:User")->find($userId);
        $player = $this->getPlayerEntityAzul($user->getId(), $gameId);

        //On récupère la partie d'Azul à laquelle se connecter.
        $azulGame = $this->getGame($gameId);
        if($azulGame == null){
            //La partie n'existe pas.
            throw new \Exception();
        }

        //On récupère les informations de la partie dans la table Game.
        $rooms = $this->bDDManager->getRepository('AGORAGameGameBundle:Game');
        $room = $rooms->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));

        if($player == null){
            //Le joueur n'a pas encore rejoins la partie.


            //On récupère l'entrée de la partie dans la table des parties de la plateforme
            $game = $this->bDDManager->getRepository('AGORAGameGameBundle:Game')->findOneBy((array('gameId' => $gameId)));

            //On récupère la liste des joueurs ayant déjà rejoints la partie 
            $players = $this->bDDManager->getRepository('AGORAGameAzulBundle:AzulPlayer')->findBy(array('gameId' => $gameId));

            //On vérifie qu'il reste de la place
            if(count($players) >= $game->getPlayersNb()){
                //Plus de place
                return -1;
            }

            //On crée le nouveau joueur
            $azulPlayer = new AzulPlayer();
            $azulPlayer->setUserId($user);
            $azulPlayer->setGameId($azulGame);
            $azulPlayer->setVictoryPointsScale(0);
            $azulPlayer->setFirstPlayerToken(false);
            $azulPlayer->setMalusTiles(array());
            $azulPlayer->setSelectedTile(array());
            $wall = array();
            for($i = 0; $i < 5; $i++){
                $wall[$i] = array();
                for($j = 0; $j < 5; $j++){
                    $wall[$i][$j] = false;
                }
            }
            $azulPlayer->setWall($wall);
            $motif = array();
            for($i = 0; $i < 5; $i++){
                $motif[$i] = array();
            }
            $azulPlayer->setMotif($motif);

            //On le renseigne dans la base de donnée
            $this->bDDManager->persist($azulPlayer);
            $this->bDDManager->flush();

            $azulGame->addPlayer($azulPlayer->getId());


            //On vérifie que la partie est pleine.
            if($this->countPlayerJoin($gameId) == $room->getPlayersNb()){
                //On initialise et lance le jeu
                $game->setState("started");
                
                $this->generateFabrics($azulGame);

                $this->nextPlayer($gameId);
            }
            $this->bDDManager->persist($azulGame);
            $this->bDDManager->flush();

            $this->bDDManager->persist($game);
            $this->bDDManager->flush();


            return $azulPlayer->getId();
        }
        // if($this->countPlayerJoin($gameId) == $room->getPlayersNb()){
        //     if ($azulGame->getFabrics()[0] == []) {
        //         $this->generateFabrics($azulGame);
        //     }
        // }

        return $player->getId();
    }

    private function generateFabrics($azulGame) {
        $fabrics = $azulGame->getFabrics();
        for($i = 0; $i < count($fabrics); $i++){
            for($j = 0; $j < 4; $j++){
                $fabrics[$i] = $this->createFabric($azulGame);
            }
        }
        $azulGame->setFabrics($fabrics);
    }

    public function updateWall($playerId, $gameId, $wall) {
        $azulPlayer = $this->getPlayerEntityAzulFromPlayerId($playerId, $gameId);
        $azulPlayer->setWall($wall);

        $this->bDDManager->persist($azulPlayer);
        $this->bDDManager->flush();
    }

    public function assignFirstPlayerToken($gameId) {
        $azulGame = $this->getGame($gameId);
        if ($azulGame->getCurrentPlayerId() == -1)  {
            $this->nextPlayer($gameId);
        }
    }
    
    /**
     * Fonction permettant de récupèrer le nom de toute les couleurs utilisé dans le jeu
     */
    public function getAllColor(){
        return array("red", "blue","orange","black","cyan", "first");
    }

    /**
     * Fonction permettant de mettre a jour le couvercle du jeu
     */
    public function setCouvercle($couvercle, $gameId){
        $color = $this->getAllColor();
        $outTile = array();
        foreach($color as $c){
            if($c != "first"){
                $outTile[$c] = $couvercle -> $c;
            }
        }
        $azulGame = $this->getGame($gameId);
        $azulGame->setOutTile($outTile);

        $this->bDDManager->persist($azulGame);
        $this->bDDManager->flush();
    }

    /**
     * Fonction permettant de mettre a jour le sac de tuile du jeu
     */
    public function setSac($sac, $gameId){
        $azulGame = $this->getGame($gameId);
        $azulGame->setReserve($sac);

        $this->bDDManager->persist($azulGame);
        $this->bDDManager->flush();
    }

    /**
     * Fonction permettant de piocher une tuile depuis un centre ou une fabrique.
     */
    public function drawTile($gameId, $playerId, $fabricId, $colorPicked, $nbPick, $fromFabric){
        
        $azulGame = $this->getGame($gameId);
        $player = $this->getPlayerEntityAzulFromPlayerId($playerId, $gameId);
        $center = $azulGame->getCenter();
        $color = $this->getAllColor();

        if($fromFabric){
            $fabrics = $azulGame->getFabrics();
            /*foreach($color as $c){
                if($c != $colorPicked && $c != "first"){
                    $center[$c] += $fabrics[$fabricId][$c];
                }
            }*/
            while (in_array($colorPicked, $fabrics[$fabricId])) {
                unset($fabrics[$fabricId][array_search($colorPicked,$fabrics[$fabricId])]);
            }
            foreach ($fabrics[$fabricId] as $tile) {
                $center[$tile] += 1;
            }
            $fabrics[$fabricId] = array();
            $azulGame->setFabrics($fabrics);

            if($player->getFirstPlayerToken()){
                $center["first"] = 1;
                $player->setFirstPlayerToken(false);
            }

        } else {
            if($center["first"] == 1){
                $malus = $player->getMalusTiles();
                $malus['first'] = 1;
                $center["first"] = 0;
                $player->setMalusTiles($malus);
            }
            $center[$colorPicked] -= $nbPick;
        }
        
        $azulGame->setCenter($center);
        
        $player->setSelectedTile(array($colorPicked, $nbPick));

        $this->bDDManager->persist($azulGame);
        $this->bDDManager->flush();

        $this->bDDManager->persist($player);
        $this->bDDManager->flush();

    }

    /**
     * Fonction permettant de savoir si c'est le tour du joueur
     */
    public function isTheyTurn($gameId, $playerId){
        $azulGame = $this->getGame($gameId);

        return $azulGame->getCurrentPlayerId() == $playerId;
    }

    /**
     * Fonction permettant d'attribuer a un joueur des points
     */
    public function givePlayerPoint($playerId, $gameId, $score){
        $player = $this->getPlayerEntityAzulFromPlayerId($playerId, $gameId);

        $player -> setVictoryPointsScale($score);

        $this->bDDManager->persist($player);
        $this->bDDManager->flush();
    }

    /**
     * Fonction permettant de définir si un joueur donné par son identifiant est gagant dans sa partie.
     */
    public function isWinner($playerId, $gameId){
        $allPlayers = $this->getPlayers($gameId);
        $thePlayer = $this->getPlayerEntityAzulFromPlayerId($playerId, $gameId);

        foreach($allPlayers as $player){
            if($player->getId() != $thePlayer->getId() ){
                if($player->getVictoryPointsScale() > $thePlayer->getVictoryPointsScale()){
                    return false;
                }
            }
        }

        return true;

    }

    public function getPlayerName($playerId, $gameId){
        $player = $this->getPlayerEntityAzulFromPlayerId($playerId, $gameId);
        $user = $this->bDDManager->getRepository("AGORAUserBundle:User")->find($player->getUserId());

        return $user->getUsername();
    }

    public function getUserName($userId) {
        $user = $this->bDDManager->getRepository("AGORAUserBundle:User")->find($userId);

        return $user->getUsername();
    }

    /**
     * Fonction permettant de vider les lignes motif plein du plateau personnel du joueur.
     */
    public function viderMotif($playerId, $gameId){
        $player = $this->getPlayerEntityAzulFromPlayerId($playerId, $gameId);
        $motif = $player->getMotif();

        for($i = 0; $i < count($motif); $i++){
            if(count($motif[$i]) == 2 && $motif[$i][1] == $i+1){
                $motif[$i] = array();
            }
        }

        $player->setMotif($motif);

        $this->bDDManager->persist($player);
        $this->bDDManager->flush();
    }

    /**
     * DOnction permettant de vider le plancher d'un joueur
     */
    public function viderPlancher($playerId, $gameId){
        $player = $this->getPlayerEntityAzulFromPlayerId($playerId, $gameId);
        $game = $this->getGame($gameId);

        $plancher = $player->getMalusTiles();

        $couvercle = $game->getOutTile();
        $color = $this->getAllColor();


        foreach($color as $c){
            if($c != "first" && array_key_exists($c, $plancher)){
                $couvercle[$c] += $plancher[$c];
            }
        }

        $plancher = $this->emptyFabrics;
        $plancher['first'] = 0;

        $player -> setMalusTiles($plancher);

        $this->bDDManager->persist($player);
        $this->bDDManager->flush();

        $game -> setOutTile($couvercle);

        $this->bDDManager->persist($game);
        $this->bDDManager->flush();
    }


    public function getPreviousPlayerId() {
        return $this->previousPlayerId;
    }

    public function getPreviousPlayerName() {
        return $this->previousPlayerName;
    }

    /**
     * Fonction permettant de poser une tuile sur les lignes motif du plateau personnel.
     */
    public function putTile($gameId, $playerId, $motifLine,$toMotif){
        $azulGame = $this->getGame($gameId);
        $player = $this->getPlayerEntityAzulFromPlayerId($playerId, $gameId);
        var_dump($playerId);
        $this->previousPlayerId = $playerId;
        $this->previousPlayerName = $this->getPlayerName($playerId, $gameId);
        $selectedTile = $player->getSelectedTile();
        $plancher = $player->getMalusTiles();
        $couvercle = $azulGame ->getOutTile();
        $nbTuile = 0;

        foreach($plancher as $c => $nb){
            $nbTuile += $nb;
        }

        if($toMotif){
           $motif = $player->getMotif();

            if(count($motif[$motifLine]) == 0){
                $motif[$motifLine][0] = $selectedTile[0];
                $motif[$motifLine][1] = $selectedTile[1]; 
            } else {
                $motif[$motifLine][1] += $selectedTile[1];
            }
            
            if($motif[$motifLine][1] > $motifLine+1){

                $overnum = $motif[$motifLine][1] - ($motifLine+1);

                $motif[$motifLine][1] -= $overnum;

                for(; $nbTuile < 7 && $overnum > 0; $nbTuile++){
                    if(array_key_exists($selectedTile[0], $plancher)){
                        $plancher[$selectedTile[0]] += 1;
                    } else {
                        $plancher[$selectedTile[0]] = 1;
                    }

                    $overnum--;
                }

                if($overnum > 0){
                    $couvercle[$selectedTile[0]] += $overnum;
                }

            }

            $player->setMotif($motif); 
        } else {
            for(; $nbTuile < 7 && $selectedTile[1] > 0; $nbTuile++ ){
                if(array_key_exists($selectedTile[0], $plancher)){
                    $plancher[$selectedTile[0]] += 1;
                } else {
                    $plancher[$selectedTile[0]] = 1;
                }
                $selectedTile[1]--;
            }
            if($selectedTile[1] > 0){
                $couvercle[$selectedTile[0]] += $selectedTile[1];
            }
            
        }

        $player->setMalusTiles($plancher);
        $player->setSelectedTile(array());

        $this->bDDManager->persist($player);
        $this->bDDManager->flush();

        $azulGame ->setOutTile($couvercle);

        $this->bDDManager->persist($azulGame);
        $this->bDDManager->flush();

        $center = $azulGame->getCenter();
        $fabrics = $azulGame->getFabrics();

        if($this->isAllFabricEmpty($fabrics) && $this->isBagEmpty($center)){
            $this->regenFabric($gameId);
        }

        $this->nextPlayer($gameId);
    }

    /**
     * Fonction permettant de regénérer les fabrique
     */
    public function regenFabric($gameId){
        $azulGame = $this->getGame($gameId);
        $fabrics = $azulGame->getFabrics();
        $sac = $azulGame->getReserve();
        $couvercle = $azulGame->getOutTile();
        $colors = $this->getAllColor();

        for($i=0;$i<count($fabrics);$i++){
            for($cpt=0; $cpt < 4; $cpt++){

                if(!$this->isBagEmpty($sac)){
                    $avalaibleColor = array();

                    foreach($colors as $c){
                        if($c != "first" && $sac[$c] != 0){
                            array_push($avalaibleColor,$c);
                        }
                    }

                    $c = $avalaibleColor[array_rand($avalaibleColor, 1)];

                    $fabrics[$i] = $this->createFabric($azulGame);

                } else{
                    if(!$this->isBagEmpty($couvercle)){
                        break;
                    } else {
                        foreach($colors as $c){
                            if($c != "first"){
                               $sac[$c] = $couvercle[$c];
                                $couvercle[$c] = 0; 
                            }
                            
                        }
                    }
                } 
                
            }
        }

        $azulGame->setFabrics($fabrics);
        $azulGame->setReserve($sac);
        $azulGame->setOutTile($couvercle);
        $azulGame->setCurrentPlayerId($azulGame->getCurrentPlayerId());

        $this->bDDManager->persist($azulGame);
        $this->bDDManager->flush();

    }
    
    /**
     * Fonction permettant de savoir si un sac de tuile est vide
     */
    public function isBagEmpty($bag){
        $colors = $this->getAllColor();

        foreach($colors as $c){
            if($bag[$c] != 0){
                return false;
            }
        }

        return true;
    }

    /**
     * Fonction permettant de savoir si tout les fabrique d'une partie sont vide
     */
    public function isAllFabricEmpty($fabrics){
        foreach($fabrics as $fab){
            if (count($fab) > 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Fonction permettant d'initialiser le classement du joueur '$user' dans la table 'leaderboard' s'il n'a pas déjà joué.
     * DONE   
     */
    public function initLeaderboard($user) {
        if ($this->bDDManager->getRepository('AGORAPlatformBundle:Leaderboard')
                ->findOneBy(array('userId' => $user->getId(), 'gameInfoId' => $this->gameInfo)) == null) {
            $lb = new Leaderboard();
            $lb -> setGameInfoId($this->gameInfo);
            $lb -> setUserId($user);
            $lb -> setElo(2000);
            $lb -> setVictoryNb(0);
            $lb -> setLoseNb(0);
            $lb -> setEqualityNb(0);

            $this->bDDManager->persist($lb);
            $this->bDDManager->flush();
        }
    }

    public function getCurrentPlayerId($gameId) {
        $azulGame = $this->getGame($gameId);
        $currentId = $azulGame->getCurrentPlayerId();
        return $currentId;
    }

    /**
     * Méthode effectuant le changement de tour pour la partie identifié par le paramêtre.
     * DONE
     * @param int $gameId id de la partie dont on doit effectué le changement de tour
     */
    public function nextPlayer($gameId){
        $azulGame = $this->getGame($gameId);
        $players = $this->getPlayers($gameId);
        if (count($players) != count($azulGame->getPlayerOrdered())) {
            $arr = array();
            for($i = 0; $i < count($players); $i++) {
                array_push($arr,$players[$i]->getId());
            }
            $azulGame->setPlayerOrdered($arr);
        } 
        $allPlayers = $azulGame->getPlayerOrdered();
        $currentId = $azulGame->getCurrentPlayerId();

        if($currentId == -1){
            $playerId = $allPlayers[array_rand($allPlayers,1)];
            $player = $this->getPlayerEntityAzulFromPlayerId($playerId, $gameId);
            $player->setFirstPlayerToken(true);
            $azulGame->setCurrentPlayerId($playerId);
            $this->bDDManager->persist($player);
            $this->bDDManager->flush();
        } else { 
            $next = $allPlayers[(array_search(abs($currentId), $allPlayers, true)+1)%count($allPlayers)];
            
            if($currentId < 0){
                $player = $this->getPlayerEntityAzulFromPlayerId($next, $gameId);
                $player->setFirstPlayerToken(true);
                $this->bDDManager->persist($player);
                $this->bDDManager->flush();
            }

            $azulGame->setCurrentPlayerId($next);
        }

        $this->bDDManager->persist($azulGame);
        $this->bDDManager->flush();
    }

    /**
     * Fonction permettant de quitter la partie identifiée par $gameId si elle est non commencée.
     * Done
     */
    public function quitGame($user, $gameId) {
        $player = $this->bDDManager->getRepository('AGORAGameAzulBundle:AzulPlayer')->findBy(array('gameId' => $gameId, 'userId' => $user))[0];
        if ($player != null) {
            $game = $this->bDDManager->getRepository('AGORAGameGameBundle:Game')
                    ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
            
            if($game->getState() == "waiting" ){

                $azulGame = $this->getGame($gameId);
                $players = $azulGame->getPlayerOrdered();

                unset($players[array_search($player->getId(), $players, true)]);

                $this->bDDManager->remove($player);
                $this->bDDManager->flush();
                $this->bDDManager->persist($azulGame);
                $this->bDDManager->flush();

                if($this->countPlayerJoin($gameId) == 0){
                    $this->supressGame($gameId);
                }

            }
        }
    }

    /**
     * Fonction supprimant la partie d'azul identifiée par $gameId.
     * DONE
     */
    public function supressGame($gameId) {
        $azulGame = $this->getGame($gameId);
        if ($azulGame == null) {
            return;
        }

		$players = $this->bDDManager->getRepository('AGORAGameAzulBundle:AzulPlayer')->findBy(array('gameId' => $gameId));

		foreach ($players as $player) {
            $this->bDDManager->remove($player);
            $this->bDDManager->flush($player);
		}
        
        $game = $this->bDDManager ->getRepository('AGORAGameGameBundle:Game')
            ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
		$this->bDDManager ->remove($game);
		$this->bDDManager ->flush($game);
	
		$this->bDDManager ->remove($azulGame);
		$this->bDDManager ->flush($azulGame);
    }

    /**
     * Fonction terminant une partie de Azul en calculant le classement et en supprimant la partie.
     * DONE
     */
    public function endGame( $gameId, $winner) {
        $players = $this->getPlayers($gameId);

        $this->gameService->computeELO($players, $gameId, $this->gameInfo, $winner);
        $this->supressGame($gameId);
    }

    /**
     * Fonction permettant de kicker un joueur n'ayant pas joué depuis 24h.
     * Done
     */
    public function kickPlayer($gameId) {
        $game = $this->getAzulGameFromGame($gameId);
        //On verifie si le jeu est lancé
        if ($game->getState() == "started") {
            //On récupère l'action du joueurs à vérifier
            $azulGame = $this->getGame($gameId);
            $currentPlayerId = $azulGame->getCurrentPlayerId();
            
            $allPlayer = $azulGame->getPlayerOrdered();

            $lastPlay = $allPlayer[ abs(array_search($currentPlayerId, $allPlayer, true)-1)%count($allPlayer)]->getLastMovePlayed();

            // On récupère la date d'ajourd'hui?
            $now = new DateTime("now");
            if ($lastPlay == null) {
                
                $lastPlay = $game->getCreationDate();
            }

            $lastPlay->add(new DateInterval('P1D'));
            //on fait la vérification
            if ($lastPlay->getTimestamp() < $now->getTimestamp() ) {
                //On met fin à la partie.
                
                $azulGame = $this->getGame($gameId);

                $this->endGame($azulGame->getPlayerOrdered(), $gameId, null);
            }
        }
    }

    /**
     * Fonction permettant de rechercher dans la table : Player. Les informations sur le joueurs identifier par
     *     - L'Identifiant de l'utilisateur
     *     - L'Identifiant de la partie.
     */
    public function getPlayerEntityAzul($userId, $gameId)
    {
        //On récupère l'ensemble du contenus de la table : Player.
        $players = $this->bDDManager->getRepository('AGORAGameAzulBundle:AzulPlayer');

        //On recherche dans le contenus de la table la ligne liée à l'Identifiant de l'utilisateur et l'Identifiant de la partie.
        $player = $players->findOneBy([
            'userId' => $userId,
            'gameId' => $gameId,
        ]);

        //On retourne la ligne de l'utilisateur.
        return $player;
    }

    public function getPlayerEntityAzulFromPlayerId($playerId, $gameId)
    {
        //On récupère l'ensemble du contenu de la table : AzulPlayer.
        $players = $this->bDDManager->getRepository('AGORAGameAzulBundle:AzulPlayer');
        
        //On recherche dans le contenu de la table la ligne liée à l'Identifiant du joueur et l'Identifiant de la partie.
        $player = $players->findOneBy([
            'id' => $playerId,
            'gameId' => $gameId
        ]);

        //On retourne la ligne de l'utilisateur.
        return $player;
    }


    /**
     * Cette fonction compte le nombre de joueur ayant rejoint la partie dont l'identifiant est donné.
     */
    public function countPlayerJoin($gameId) {
        //On récupère l'ensemble du contenu de la table : AzulPlayer.
        $players = $this->bDDManager->getRepository('AGORAGameAzulBundle:AzulPlayer');

        $playersJoin = $players->findBy([
            'gameId' => $gameId,
        ]);

        return count($playersJoin);
    }

    /**
     * Retourne les informations d'une partie d'azul à partir de son ID.
     * @param int $gameId 
     * @return AzulGame
     */
    public function getGame($gameId) {
        // print_r($this->bDDManager->getRepository("AGORAGameAzulBundle:AzulGame")->find($gameId)->getFabrics());
        return $this->bDDManager->getRepository("AGORAGameAzulBundle:AzulGame")->find($gameId);
    }

    /**
     * Retourne les informations d'une partie d'azul à partir de son ID provenant de la table game.
     */
    public function getAzulGameFromGame($gameId) {
        $games = $this->bDDManager->getRepository("AGORAGameGameBundle:Game");
        $azulGame = $games->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));

        return $azulGame;
    }

    public function getPlayers($gameId) {
        $players = $this->bDDManager->getRepository('AGORAGameAzulBundle:AzulPlayer');
        $playersJoin = $players->findBy(['gameId' => $gameId]);
        return $playersJoin;
    }

    public function print_txt($txt) {
        print($txt);
    }

    public function print_array($arr) {
        print_r($arr);
    }
}
?>