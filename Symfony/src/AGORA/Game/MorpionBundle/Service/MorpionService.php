<?php

namespace AGORA\Game\MorpionBundle\Service;

use AGORA\Game\MorpionBundle\Model\MorpionGameModel;
use AGORA\Game\MorpionBundle\Model\MorpionPlayerModel;
use Doctrine\ORM\EntityManager;
use AGORA\PlatformBundle\Entity\Leaderboard;
use \DateTime;
use \DateInterval;

/**
 * Cette classe fait le lien entre le contrôleur et le modéle de données. Elle donne accès
 * à l'objet EntityManager (voir le fichier MorpionBundle/Ressources/Config/services.yml)
 */
class MorpionService {

    private $BDDManager;
    private $gameService;
    private $morpionModel;
    private $playerModel;
    private $gameInfo;

    /**
     * Constructeur. L'objet EntityManager (gestion de la BDD) est donné en paramètre.
     */
    public function __construct(EntityManager $em, $gs) {
        $this->BDDManager = $em;
        $this->gameService = $gs;
        $this->morpionModel = new MorpionGameModel($this->BDDManager);
        $this->playerModel = new MorpionPlayerModel($this->BDDManager);
        $this->gameInfo = $this->BDDManager->getRepository('AGORAPlatformBundle:GameInfo')->findOneBy(array('gameCode' => "mor"));
    }

    /**
     * Fonction permettant de créer une partie.
     */
    public function createMorpionGame($name, $host) {
        return $this->morpionModel->createMorpionGame($name, $host);
    }

    /**
     * Fonction permettant à un utilisateur de rejoindre une partie de Morpion :
     *  - Si l'utilisateur est déja enregistré dans la partie on lui retourne son identifiant de Joueur de Partie.
     *  - Si l'utilisateur n'est pas déja enregistré:
     *      -Si la partie n'est pas pleine, on enregistre le nouveau utilisateur et on lui assigne un identifiant de Joueur de Partie.
     *      -Si la partie est pleine, on retourne -1.
     */
    public function joinPlayer($user, $gameId) {
        //On essaie de récupèrer les informations du joueur dans la partie si elles existent.
        $player = $this->getPlayerEntityMorpion($user->getId(), $gameId);

        //Si le joueur n'à pas déjà rejoint la partie.
        if ($player == null) {

            //On essaie de créer un nouveau joueur lié à la partie.
            $idJoueurPartie = $this->playerModel->createMorpionPlayer($user, $gameId);

            //Si un joueur à vraiment été ajouté à la partie.
            if($idJoueurPartie != -1){

                $games = $this->BDDManager->getRepository("AGORAGameMorpionBundle:MorpionGame");
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
        $player = $this->BDDManager->getRepository('AGORAGameMorpionBundle:MorpionPlayer')
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
     * Fonction supprimant la partie de Morpion identifiée par $gameId.
     */
    public function supressGame($gameId) {
        $game = $this->BDDManager->getRepository('AGORAGameMorpionBundle:MorpionGame')->find($gameId);
        if ($game == null) {
            return;
        }
		$players = $this->BDDManager->getRepository('AGORAGameMorpionBundle:MorpionPlayer')->findBy(array('gameId' => $gameId));

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
     * Fonction terminant une partie de Morpion en calculant le classement et en supprimant la partie.
     */
    public function endGame($players, $gameId, $winner) {
        $this->gameService->computeELO($players, $gameId, $this->gameInfo, $winner);
        $this->supressGame($gameId);
    }

    /**
     * Fonction permettant de kicker un joueur n'ayant pas joué depuis 24h.
     */
    public function kickPlayer($gameId) {
        $game = $this->getMorpionGameFromGame($gameId);
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
                $grilleArray = explode(";", $grille);

                $morpionGame = $this->getMorpionGameFromGame($gameId);
                $morpionGame->setState("finished");
                $this->BDDManager->persist($morpionGame);
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
     * Fonction permettant de rechercher dans la table : MorpionPlayer. Les informations sur le joueurs identifier par
     *     - L'Identifiant de l'utilisateur
     *     - L'Identifiant de la partie.
     */
    public function getPlayerEntityMorpion($userId, $gameId)
    {
        //On récupère l'ensemble du contenus de la table : MorpionPlayer.
        $players = $this->BDDManager->getRepository('AGORAGameMorpionBundle:MorpionPlayer');

        //On recherche dans le contenus de la table la ligne liée à l'Identifiant de l'utilisateur et l'Identifiant de la partie.
        $player = $players->findOneBy([
            'userId' => $userId,
            'gameId' => $gameId,
        ]);

        //On retourne la ligne de l'utilisateur.
        return $player;
    }

    public function getPlayerEntityMorpionFromPlayerId($playerId, $gameId)
    {
        //On récupère l'ensemble du contenu de la table : MorpionPlayer.
        $players = $this->BDDManager->getRepository('AGORAGameMorpionBundle:MorpionPlayer');

        //On recherche dans le contenu de la table la ligne liée à l'Identifiant du joueur et l'Identifiant de la partie.
        $player = $players->findOneBy([
            'id' => $playerId,
            'gameId' => $gameId,
        ]);

        //On retourne la ligne de l'utilisateur.
        return $player;
    }


    /**
     * Cette fonction compte le nombre de joueur ayant rejoint la partie dont l'identifiant est donné.
     */
    public function countPlayerJoin($gameId) {
        //On récupère l'ensemble du contenu de la table : MorpionPlayer.
        $players = $this->BDDManager->getRepository('AGORAGameMorpionBundle:MorpionPlayer');

        $playersJoin = $players->findBy([
            'gameId' => $gameId,
        ]);

        return count($playersJoin);
    }

    /**
     * Retourne les informations d'une partie de morpion à partir de son ID.
     */
    public function getGame($gameId) {
        $games = $this->BDDManager->getRepository("AGORAGameMorpionBundle:MorpionGame");
        $game = $games->findOneById($gameId);
        return $game;
    }

    /**
     * Retourne les informations d'une partie de morpion à partir de son ID provenant de la table game.
     */
    public function getMorpionGameFromGame($gameId) {
        $games = $this->BDDManager->getRepository("AGORAGameGameBundle:Game");
        $morpionGame = $games->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));

        return $morpionGame;
    }

    public function getPlayers($gameId) {
        $players = $this->BDDManager->getRepository('AGORAGameMorpionBundle:MorpionPlayer');
        $playersJoin = $players->findBy(['gameId' => $gameId]);
        return $playersJoin;
    }

    public function getCurrentPlayerID($gameId){
        return $this->getGame($gameId)->getCurrentPlayerId();
    }

    public function getCurrentPlayer($gameId){
        $currentPlayerId = $this->getCurrentPlayerID($gameId);
        $player = $this->BDDManager->getRepository('AGORAGameMorpionBundle:MorpionPlayer')->findOneById($currentPlayerId);
        return $player;
    }

    /**
     * Fonction qui modifie le champs Current_Player_ID du modele MorpionGame.
     */
    public function nextPlayer($gameId){
        $morpionGame = $this->getGame($gameId);

        if($this->getCurrentPlayerID($gameId) == $this->getFirstPlayer($gameId)->getId()){
            $morpionGame->setCurrentPlayerId($this->getSecondPlayer($gameId)->getId());
            $this->getMorpionGameFromGame($gameId)->setCurrentPlayer($this->getSecondPlayer($gameId)->getUserId()->getId());
        }
        else if($this->getCurrentPlayerID($gameId) == $this->getSecondPlayer($gameId)->getId()){
            $morpionGame->setCurrentPlayerId($this->getFirstPlayer($gameId)->getId());
            $this->getMorpionGameFromGame($gameId)->setCurrentPlayer($this->getFirstPlayer($gameId)->getUserId()->getId());
        }

        //Dans le cas ou Current_Player_ID == -1 (partie non commencée)
        else{
            if(random_int(0,1) == 0){
                $morpionGame->setCurrentPlayerId($this->getFirstPlayer($gameId)->getId());
                $this->getMorpionGameFromGame($gameId)->setCurrentPlayer($this->getFirstPlayer($gameId)->getUserId()->getId());
                
            } else{
                $morpionGame->setCurrentPlayerId($this->getSecondPlayer($gameId)->getId());
                $this->getMorpionGameFromGame($gameId)->setCurrentPlayer($this->getSecondPlayer($gameId)->getUserId()->getId());

            }
        }
        $this->BDDManager->persist($morpionGame);
        $this->BDDManager->flush();
    }

    public function getFirstPlayer($gameId){
        return $this->getPlayers($gameId)[0];
    }

    public function getSecondPlayer($gameId){
        if($this->countPlayerJoin($gameId) > 1){
            return $this->getPlayers($gameId)[1];
        }
        return null;
    }

    public function jouerCase($case, $gameId, $playerId, $symbole){
        $this->BDDManager->clear();

        $game = $this->getMorpionGameFromGame($gameId);
        if ($game->getState() == "started") {
            $morpionGame = $this->getGame($gameId);
            $grille = $morpionGame->getBoard();
            $grilleArray = explode(";", $grille);

            if ($grilleArray[$case] == ""){
                $grilleArray[$case] = $symbole;
                $grille = implode(";", $grilleArray);

                $morpionGame->setBoard($grille);
                $this->BDDManager->persist($morpionGame);
                $this->BDDManager->flush();

                $player = $this->getPlayerEntityMorpionFromPlayerId($playerId, $gameId);
                $player->setLastMovePlayed(new \DateTime("now"));
                $this->BDDManager->persist($player);
                $this->BDDManager->flush();

                //Si la partie est finie suite à ce coup, on ne change pas de joueur. Le joueur courant est le gagnant.
                if($this->gameIsFinish($grilleArray, $morpionGame->getId()) == -1){
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
        $morpionGame = $games->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));

        $morpionGame->setState("started");

        $this->BDDManager->persist($morpionGame);
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
        $grilleArray = explode(";", $grille);

        $isFinish = $this->gameIsFinish($grilleArray, $game->getId());

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
        if($this->diagWin($board) or $this->lineWin($board) or $this->colWin($board)){
            $morpionGame = $this->getMorpionGameFromGame($gameId);
            $morpionGame->setState("finished");
            $this->BDDManager->persist($morpionGame);
            $this->BDDManager->flush();
            return 1;
        }

        //Egalite
        if($this->boardComplete($board)){
            $morpionGame = $this->getMorpionGameFromGame($gameId);
            $morpionGame->setState("finished");
            $this->BDDManager->persist($morpionGame);
            $this->BDDManager->flush();
            return 0;
        }

        //La partie continue
        return -1;
    }



    // 0 1 2
    // 3 4 5
    // 6 7 8

    function diagWin($board){

        if(    $board[0] == $board[4] and $board[4] == $board[8] and "" != $board[8]
            or $board[6] == $board[4] and $board[4] == $board[2] and "" != $board[2])
        {
            return true;
        }

        return false;

    }

    function lineWin($board)
    {
        if(    $board[0] == $board[1] and $board[1] == $board[2] and "" != $board[2]
            or $board[3] == $board[4] and $board[4] == $board[5] and "" != $board[5]
            or $board[6] == $board[7] and $board[7] == $board[8] and "" != $board[8])
        {
            return true;
        }

        return false;
    }

    function colWin($board)
    {
        if(    $board[0] == $board[3] and $board[3] == $board[6] and "" != $board[6]
            or $board[1] == $board[4] and $board[4] == $board[7] and "" != $board[7]
            or $board[2] == $board[5] and $board[5] == $board[8] and "" != $board[8])
        {
            return true;
        }

        return false;
    }

    function boardComplete($board)
    {
        for ($i = 0; $i < 9; $i++) {
            if($board[$i] == ""){
                return false;
            }
        }

        return true;
    }

/*
    public function areAllPlayersReady($gameId) {
        return $this->gameModel->allOk($gameId);
    }
*/
}
