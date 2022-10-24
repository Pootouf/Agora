<?php

namespace AGORA\Game\Puissance4Bundle\Service;

use AGORA\Game\Puissance4Bundle\Model\Puissance4GameModel;
use AGORA\Game\Puissance4Bundle\Model\Puissance4PlayerModel;
use Doctrine\ORM\EntityManager;
use AGORA\PlatformBundle\Entity\Leaderboard;
use \DateTime;
use \DateInterval;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cette classe fait le lien entre le contrôleur et le modéle de données. Elle donne accès
 * à l'objet EntityManager (voir le fichier Puissance4Bundle/Ressources/Config/services.yml)
 */
class Puissance4Service {


    
    private $BDDManager;
    private $gameService;
    private $puissance4Model;
    private $playerModel;
    private $gameInfo;

    /**
     * Constructeur. L'objet EntityManager (gestion de la BDD) est donné en paramètre.
     */
    public function __construct(EntityManager $em, $gs) {
        $this->BDDManager = $em;
        $this->gameService = $gs;
        $this->puissance4Model = new Puissance4GameModel($this->BDDManager);
        $this->playerModel = new Puissance4PlayerModel($this->BDDManager);
        $this->gameInfo = $this->BDDManager->getRepository('AGORAPlatformBundle:GameInfo')->findOneBy(array('gameCode' => "p4"));
    }

    /**
     * Fonction permettant de créer une partie.
     */
    public function createPuissance4Game($name, $host) {
        return $this->puissance4Model->createPuissance4Game($name, $host);
    }

    /**
     * Fonction permettant à un utilisateur de rejoindre une partie de puissance 4 :
     *  - Si l'utilisateur est déja enregistré dans la partie on lui retourne son identifiant de Joueur de Partie.
     *  - Si l'utilisateur n'est pas déja enregistré:
     *      -Si la partie n'est pas pleine, on enregistre le nouveau utilisateur et on lui assigne un identifiant de Joueur de Partie.
     *      -Si la partie est pleine, on retourne -1.
     */
    public function joinPlayer($user, $gameId) {
        //On essaie de récupèrer les informations du joueur dans la partie si elles existent.
        $player = $this->getPlayerEntityPuissance4($user->getId(), $gameId);

        //Si le joueur n'à pas déjà rejoint la partie.
        if ($player == null) {

            //On essaie de créer un nouveau joueur lié à la partie.
            $idJoueurPartie = $this->playerModel->createPuissance4Player($user, $gameId);

            //Si un joueur à vraiment été ajouté à la partie.
            if($idJoueurPartie != -1){

                $games = $this->BDDManager->getRepository("AGORAGamePuissance4Bundle:Puissance4Game");
                $game = $games->findOneById($gameId);

                $rooms = $this->BDDManager->getRepository("AGORAGameGameBundle:Game");
                $room = $rooms->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
                
                //Si le nombre de joueurs est complété alors la partie démarre.
                if ($this->countPlayerJoin($gameId) == $room->getPlayersNb()) {

                    //On initialise la partie qui démarre.
                    $this->initGame($gameId);
                }
            }

            return $idJoueurPartie;
        }

        return $player->getId();
    }

    /**
     * Fonction permettant d'initialiser le classement du joueur '$user' dans la table 'leaderboard' s'il n'a pas déjà joué.
     */
    public function initLeaderboard($user) {
        if ($this->BDDManager->getRepository('AGORAPlatformBundle:Leaderboard')
                ->findOneBy(array('userId' => $user->getId(), 'gameInfoId' => $this->gameInfo)) == null) {
            $lb = new Leaderboard;
            $lb -> setGameInfoId($this->gameInfo);
            $lb -> setUserId($user);
            $lb -> setElo(2000);
            $lb -> setVictoryNb(0);
            $lb -> setLoseNb(0);
            $lb -> setEqualityNb(0);

            $this->BDDManager->persist($lb);
            $this->BDDManager->flush();
        }
    }

    /**
     * Fonction permettant de quitter la partie identifiée par $gameId si elle est non commencée.
     */
    public function quitGame($user, $gameId) {
        $player = $this->BDDManager->getRepository('AGORAGamePuissance4Bundle:Puissance4Player')
                ->findBy(array('gameId' => $gameId, 'userId' => $user));
        if ($player != null) {
            $game = $this->BDDManager->getRepository('AGORAGameGameBundle:Game')
                    ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
            if ($this->countPlayerJoin($gameId) < $game->getPlayersNb()) {
                $this->supressGame($gameId);
            }
        }
    }

    /**
     * Fonction supprimant la partie de Puissance4 identifiée par $gameId.
     */
    public function supressGame($gameId) {
        $game = $this->BDDManager->getRepository('AGORAGamePuissance4Bundle:Puissance4Game')->find($gameId);
        if ($game == null) {
            return;
        }
		$players = $this->BDDManager->getRepository('AGORAGamePuissance4Bundle:Puissance4Player')->findBy(array('gameId' => $gameId));

		foreach ($players as $player) {
            $this->BDDManager->remove($player);
            $this->BDDManager->flush($player);
		}
        
        $g = $this->BDDManager ->getRepository('AGORAGameGameBundle:Game')
            ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
		$this->BDDManager ->remove($g);
		$this->BDDManager ->flush($g);
	
		$this->BDDManager ->remove($game);
		$this->BDDManager ->flush($game);
    }

    /**
     * Fonction terminant une partie de Puissance 4 en calculant le classement et en supprimant la partie.
     */
    public function endGame($players, $gameId, $winner) {
        $this->gameService->computeELO($players, $gameId, $this->gameInfo, $winner);
        $this->supressGame($gameId);
    }

    /**
     * Fonction permettant de kicker un joueur n'ayant pas joué depuis 24h.
     */
    public function kickPlayer($gameId) {
        $game = $this->getPuissance4GameFromGame($gameId);
        if ($game->getState() == "started") {
            $lastPlay = null;
            $currentPlayerId = $this->getCurrentPlayer($gameId)->getId();
            if($currentPlayerId == $this->getFirstPlayer($gameId)->getId()){
                $lastPlay = $this->getSecondPlayer($gameId)->getLastMovePlayed();
            }
            else if($currentPlayerId == $this->getSecondPlayer($gameId)->getId()){
                $lastPlay = $this->getFirstPlayer($gameId)->getLastMovePlayed();

            }
            $now = new DateTime("now");
            if ($lastPlay == null) {
                $lastPlay = $game->getCreationDate();
            }
            $lastPlay->add(new DateInterval('P1D'));
            if ($lastPlay->getTimestamp() < $now->getTimestamp() ) {
                $this->nextPlayer($gameId);
                $this->BDDManager->clear();
                $userRepository = $this->BDDManager->getRepository('AGORAUserBundle:User');
                $P1UserId = $this->getFirstPlayer($gameId)->getUserId();
                $P1UserName = $userRepository->findOneBy(array('id' => $P1UserId))->getUsername();
                $secondPlayer = $this->getSecondPlayer($gameId);
                if ($secondPlayer != null) {
                    $P2UserName = $userRepository->findOneBy(array('id' => $secondPlayer->getUserId()))->getUsername();
                } else {
                    $P2UserName = "";
                }
                $game = $this->getGame($gameId);
                $grille = $game->getBoard();
                // $grilleArray = explode(";", $grille);

                $puissance4Game = $this->getPuissance4GameFromGame($gameId);
                $puissance4Game->setState("finished");
                $this->BDDManager->persist($puissance4Game);
                $this->BDDManager->flush();
                
                $dataTab = [
                    'action' => 'win',
                    'finished' => true,
                    'gameId' => $gameId,
                    'player1' => $P1UserName,
                    'player2' => $P2UserName,
                    'currentPlayer' => $this->getCurrentPlayerID($gameId),
                    'grille' => $game->getBoard(),
                ];
            } else {
                $dataTab = [
                    'action' => 'kick',
                    'time' => ($lastPlay->getTimestamp() - $now->getTimestamp())
                ];
                $lastPlay->sub(new DateInterval('P1D'));
            }
        } else {
            $dataTab = [
                'action' => 'begin'
            ];
        }
        return $dataTab;
    }
    /**
     * Fonction permettant de rechercher dans la table : Puissance4Player. Les informations sur le joueurs identifier par
     *     - L'Identifiant de l'utilisateur
     *     - L'Identifiant de la partie.
     */
    public function getPlayerEntityPuissance4($userId, $gameId)
    {
        //On récupère l'ensemble du contenus de la table : Puissance4Player.
        $players = $this->BDDManager->getRepository('AGORAGamePuissance4Bundle:Puissance4Player');

        //On recherche dans le contenus de la table la $ligne liée à l'Identifiant de l'utilisateur et l'Identifiant de la partie.
        $player = $players->findOneBy([
            'userId' => $userId,
            'gameId' => $gameId,
        ]);

        //On retourne la $ligne de l'utilisateur.
        return $player;
    }

    public function getPlayerEntityPuissance4FromPlayerId($playerId, $gameId)
    {
        //On récupère l'ensemble du contenu de la table : Puissance4Player.
        $players = $this->BDDManager->getRepository('AGORAGamePuissance4Bundle:Puissance4Player');

        //On recherche dans le contenu de la table la $ligne liée à l'Identifiant du joueur et l'Identifiant de la partie.
        $player = $players->findOneBy([
            'id' => $playerId,
            'gameId' => $gameId,
        ]);

        //On retourne la $ligne de l'utilisateur.
        return $player;
    }


    /**
     * Cette fonction compte le nombre de joueur ayant rejoint la partie dont l'identifiant est donné.
     */
    public function countPlayerJoin($gameId) {
        //On récupère l'ensemble du contenu de la table : Puissance4Player.
        $players = $this->BDDManager->getRepository('AGORAGamePuissance4Bundle:Puissance4Player');

        $playersJoin = $players->findBy([
            'gameId' => $gameId,
        ]);

        return count($playersJoin);
    }

    /**
     * Retourne les informations d'une partie de puissance4 à partir de son ID.
     */
    public function getGame($gameId) {
        $games = $this->BDDManager->getRepository("AGORAGamePuissance4Bundle:Puissance4Game");
        $game = $games->findOneById($gameId);
        return $game;
    }

    /**
     * Retourne les informations d'une partie de puissance4 à partir de son ID provenant de la table game.
     */
    public function getPuissance4GameFromGame($gameId) {
        $games = $this->BDDManager->getRepository("AGORAGameGameBundle:Game");
        $puissance4Game = $games->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));

        return $puissance4Game;
    }

    public function getPlayers($gameId) {
        $players = $this->BDDManager->getRepository('AGORAGamePuissance4Bundle:Puissance4Player');
        $playersJoin = $players->findBy(['gameId' => $gameId]);
        return $playersJoin;
    }

    public function getCurrentPlayerID($gameId){
        return $this->getGame($gameId)->getCurrentPlayerId();
    }

    public function getCurrentPlayer($gameId){
        $currentPlayerId = $this->getCurrentPlayerID($gameId);
        $player = $this->BDDManager->getRepository('AGORAGamePuissance4Bundle:Puissance4Player')->findOneById($currentPlayerId);
        return $player;
    }

    /**
     * Fonction qui modifie le champs Current_Player_ID du modele Puissance4Game.
     */
    public function nextPlayer($gameId){
        $puissance4Game = $this->getGame($gameId);

        if($this->getCurrentPlayerID($gameId) == $this->getFirstPlayer($gameId)->getId()){
            $puissance4Game->setCurrentPlayerId($this->getSecondPlayer($gameId)->getId());
            $this->getPuissance4GameFromGame($gameId)->setCurrentPlayer($this->getSecondPlayer($gameId)->getUserId()->getId());
        }
        else if($this->getCurrentPlayerID($gameId) == $this->getSecondPlayer($gameId)->getId()){
            $puissance4Game->setCurrentPlayerId($this->getFirstPlayer($gameId)->getId());
            $this->getPuissance4GameFromGame($gameId)->setCurrentPlayer($this->getFirstPlayer($gameId)->getUserId()->getId());
        }

        //Dans le cas ou Current_Player_ID == -1 (partie non commencée)
        //Pas très générique pour le nb de joueurs
        else{
            if(rand(0,1) == 0){
                $puissance4Game->setCurrentPlayerId($this->getFirstPlayer($gameId)->getId());
                $this->getPuissance4GameFromGame($gameId)->setCurrentPlayer($this->getFirstPlayer($gameId)->getUserId()->getId());
                
            } else{
                $puissance4Game->setCurrentPlayerId($this->getSecondPlayer($gameId)->getId());
                $this->getPuissance4GameFromGame($gameId)->setCurrentPlayer($this->getSecondPlayer($gameId)->getUserId()->getId());

            }
        }
        $this->BDDManager->persist($puissance4Game);
        $this->BDDManager->flush();
    }

    public function getFirstPlayer($gameId){
        return $this->getPlayers($gameId)[0];
    }
    //Bizzarerie syntaxique
    public function getSecondPlayer($gameId){
        if($this->countPlayerJoin($gameId) > 1){
            return $this->getPlayers($gameId)[1];
        }
        return null;
    }

    public function jouerCase($column, $gameId, $playerId, $symbole){
        $this->BDDManager->clear();

        $game = $this->getPuissance4GameFromGame($gameId);
        if ($game->getState() == "started") {
            $puissance4Game = $this->getGame($gameId);
            $grille = $puissance4Game->getBoard();

            if ($grille[$column][0] == "none") {
                for ($line = 5; $line >= 0; $line--) {
                    if ($grille[$column][$line] == "none") {
                        $grille[$column][$line] = $symbole;
                        break;
                    }
                }

                $puissance4Game->setBoard($grille);
                $this->BDDManager->persist($puissance4Game);
                $this->BDDManager->flush();

                $player = $this->getPlayerEntityPuissance4FromPlayerId($playerId, $gameId);
                $player->setLastMovePlayed(new \DateTime("now"));
                $this->BDDManager->persist($player);
                $this->BDDManager->flush();

                //Si la partie est finie suite à ce coup, on ne change pas de joueur. Le joueur courant est le gagnant.
                if($this->gameIsFinish($grille, $puissance4Game->getId()) == -1){
                    $this->nextPlayer($gameId);
                }
            }
        }
    }


    /**
     * Initialise la partie identifié par idGame.
     */
    public function initGame($gameId) {
        $games = $this->BDDManager->getRepository("AGORAGameGameBundle:Game");
        $puissance4Game = $games->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));

        $puissance4Game->setState("started");

        $this->BDDManager->persist($puissance4Game);
        $this->BDDManager->flush();

        $this->nextPlayer($gameId);
    }

    /**
     * 
     */
    public function getArrayForRefreshDatas($gameId){

        $this->BDDManager->clear();

        $userRepository = $this->BDDManager->getRepository('AGORAUserBundle:User');

        $P1UserId = $this->getFirstPlayer($gameId)->getUserId();
        $P1UserName = $userRepository->findOneBy(array('id' => $P1UserId))->getUsername();


        $secondPlayer = $this->getSecondPlayer($gameId);
        if($secondPlayer != null){
            $P2UserName = $userRepository->findOneBy(array('id' => $secondPlayer->getUserId()))->getUsername();
        }
        else{
            $P2UserName = "";
        }

        $game = $this->getGame($gameId);

        $grille = $game->getBoard();

        $isFinish = $this->gameIsFinish($grille, $game->getId());

        if( $isFinish == 0 ){
            $dataTab = [
                'action' => 'equality',
                'finished' => true
            ];
        }
        else if( $isFinish == 1){
            $dataTab = [
                'action' => 'win',
                'finished' => true
            ];
        }
        else{
            $dataTab = [
                'action' => 'refresh',
                'finished' => false
            ];
        }

        $dataTab = $dataTab + [
            'gameId' => $gameId,

            'player1' => $P1UserName,
            'player2' => $P2UserName,
            'currentPlayer' => $this->getCurrentPlayerID($gameId),
            'grille' => $game->getBoard(),
        ];

        return $dataTab;
    }

    function gameIsFinish($board, $gameId){
        //Un Gagnant
        if($this->isWinning($board)){
            $puissance4Game = $this->getPuissance4GameFromGame($gameId);
            $puissance4Game->setState("finished");
            $this->BDDManager->persist($puissance4Game);
            $this->BDDManager->flush();
            return 1;
        }

        //Egalite
        if($this->boardComplete($board)){
            $puissance4Game = $this->getPuissance4GameFromGame($gameId);
            $puissance4Game->setState("finished");
            $this->BDDManager->persist($puissance4Game);
            $this->BDDManager->flush();
            return 0;
        }

        //La partie continue
        return -1;
    }


    function boardComplete($board)
    {
	
		
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 6; $j++) {
                if($board[$i][$j] == "none"){
					return false;
                }
            }
        }

        return true;
    } 
    
	function isWinning($grid)
    {
        $ligMax = 6; 
        $colMax = 7;
        for($indiceCol=0; $indiceCol<$colMax; $indiceCol++)
            {
                for($indiceLig=0; $indiceLig<$ligMax; $indiceLig++)
                {
                    if (!($grid[$indiceCol][$indiceLig] == "none") )
                    {
                        
                        if (  $indiceCol<= $colMax && $this->countTokens(  $grid,  $indiceLig,  $indiceCol, 1, 1) == 4 
                                     // diagonale : vers le bas et à droite 
                            ||   $indiceCol<= $colMax && $this->countTokens(  $grid,  $indiceLig,  $indiceCol, -1, 1) == 4
                                    // vers le haut et à droite
                            ||  $indiceLig<= $ligMax && $this->countTokens(  $grid,  $indiceLig,  $indiceCol, 0, 1) == 4
                                    // horizontal vers la droite
                            ||  $indiceLig<= $ligMax && $this->countTokens(  $grid,  $indiceLig,  $indiceCol, 1, 0) == 4
                                // vertical du haut vers le bas
                            )
                        {
                            return true;
                        }
                    }
                }
            }

        return false;
    }



    function countTokens($grid,  $lig,  $col,  $ligDir,  $colDir) {

        $cpt  = 0;         // compte le nombre de jeton a$ligné
        $lig_cpt = $lig;    // s'occupe de la direction de la $ligne (nord ou sud) du comptage des jetons
        $col_cpt = $col;     // s'occupe de la direction la $colonne (ouest ou est) du comptage des jetons

        while(   $lig_cpt >= 0 &&  $lig_cpt < 6 &&  $col_cpt >= 0 &&  $col_cpt < 7)
        {
            
            if ($grid[$col_cpt][$lig_cpt] != "none" && $grid[$col_cpt][$lig_cpt] == $grid[$col][$lig] ) {
                ++$cpt;
            }
            else {
                break;
            }

             $lig_cpt +=  $ligDir; 
             $col_cpt +=  $colDir;

         }

        return  $cpt;
    }

}
