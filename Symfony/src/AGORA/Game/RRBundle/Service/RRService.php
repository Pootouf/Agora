<?php

namespace AGORA\Game\RRBundle\Service;

use AGORA\Game\GameBundle\Service\GameService;
use AGORA\Game\RRBundle\Entity\RRGame;
use AGORA\Game\RRBundle\Entity\RRBoard;
use AGORA\Game\GameBundle\Entity\Game;
use Doctrine\ORM\EntityManager;
use AGORA\Game\RRBundle\Entity\RRPlayer;
use AGORA\Game\RRBundle\Model\ActionType;
use AGORA\PlatformBundle\Entity\Leaderboard;

/**
 * Cette classe permet de faire le lien entre le controlleur et le modèle.
 * 
 */
class RRService {
    private $Manager;
    private $gameService;
    
    public function __construct(EntityManager $em, GameService $gs) {
        $this->manager = $em;
        $this->gameService = $gs;
        $this->gameInfo = $this->manager->getRepository('AGORAPlatformBundle:GameInfo')->findOneBy(array('gameCode' => "rr"));
    }

    public function createRoom($name, $playersNb) {

        $board = new RRBoard($playersNb);
        $this->manager->persist($board);
        $this->manager->flush();

        $rrGame = new RRGame($playersNb, $board);
        $this->manager->persist($rrGame);
        $this->manager->flush();


        $game = new Game();
        $game->setGameId($rrGame->getId());
        $game->setGameInfoId($this->gameInfo);
        $game->setGameName($name);
        $game->setPlayersNb($playersNb);
        $game->setState("waiting");
        $game->setCreationDate(new \DateTime("now"));
        $this->manager->persist($rrGame);
        $this->manager->flush();
        $this->manager->persist($game);
        $this->manager->flush();

        return $rrGame->getId();
    }

    public function initLeaderboard($user) {
        if (
            $this->manager->getRepository('AGORAPlatformBundle:Leaderboard')
            ->findOneBy(array('userId' => $user->getId(),
                             'gameInfoId' => $this->gameInfo)) == null
        ) {
            $lb = new Leaderboard();
            $lb->setGameInfoId($this->gameInfo);
            $lb->setUserId($user);
            $lb->setElo(2000);
            $lb->setVictoryNb(0);
            $lb->setLoseNb(0);
            $lb->setEqualityNb(0);

            $this->manager->persist($lb);
            $this->manager->flush();
        }
    }


    public function play(int $gameId, int $playerId,int $actionId, array $details){
       $rrGame = $this->getGame($gameId);
       return $rrGame->turn($playerId,$actionId, $details);
    }

    public function joinPlayer($userId, $gameId) {
        //  On récupère le joueur a ajouter
        $user = $this->manager->getRepository("AGORAUserBundle:User")->find($userId);
        //  On récupère la partie en cours 
        $rrGame = $this->getGame($gameId);


        $game = $this->manager->getRepository('AGORAGameGameBundle:Game')
            ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
        $players = $this->manager->getRepository('AGORAGameRRBundle:RRPlayer')
            ->findBy(array('gameId' => $gameId));
        $currentPlayerNb = count($players);
        $expectedPlayerNb = $game->getPlayersNb();
        var_dump("\n" . $currentPlayerNb . "/" . $expectedPlayerNb . "\n");
        if ($currentPlayerNb >= $expectedPlayerNb) {
            return -1;
        }

        $player = new RRPlayer($currentPlayerNb);
        $player->setGameId($rrGame);
        $player->setUserId($user);
        $this->manager->persist($player);
        $this->manager->flush();

        $rrGame->addPlayer($player);
        $this->manager->persist($rrGame);
        $this->manager->flush();
    }

    /*public function canPlay(int $player, int $actionId, $details){
        return $this->game->canPlay($player, $actionId, $details);
    }*/

    public function getPlayer(int $gameId, int $userId){
        $player = $this->manager->getRepository('AGORAGameRRBundle:RRPlayer')
        ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        return $player;
    }

    public function getPlayers(int $gameId) {
        $rrGame = $this->getGame($gameId);
        return $rrGame->getPlayers();
    }

    public function getGameBoard(int $gameId){
        $rrGame = $this->getGame($gameId);
        return $rrGame->getBoard();   
    }

    public function getGame(int $gameId) {
        $rrGame = $this->manager->getRepository('AGORAGameRRBundle:RRGame')->find($gameId);
        return $rrGame;
    }

    public function getActions(int $gameId){
        $rrGame = $this->getGame($gameId);
        return $rrGame->getBoard()->getActionsArray();
        // return array();
    }

    public function print_text($var) {
        print($var);
        print("\n");
    }

    public function print_array($var) {
        print_r($var);
        print("\n");
    }

    public function dump($var) {
        var_dump($var);
    }
}
?>