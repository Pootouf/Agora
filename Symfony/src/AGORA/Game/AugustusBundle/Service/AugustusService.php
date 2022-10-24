<?php

namespace AGORA\Game\AugustusBundle\Service;

use AGORA\Game\AugustusBundle\Model\AugustusGameModel;
use AGORA\Game\AugustusBundle\Model\AugustusPlayerModel;
use AGORA\Game\AugustusBundle\Model\AugustusCardModel;


use Doctrine\ORM\EntityManager;
use AGORA\PlatformBundle\Entity\Leaderboard;

class AugustusService {

    public $manager;
    public $gameService;
    public $gameModel;
    public $playerModel;
    public $cardModel;

    // $em est passé en argument dans services.yml
    public function __construct(EntityManager $em, $gs) {
        $this->manager = $em;
        $this->gameService = $gs;
        $this->gameModel = new AugustusGameModel($em);
        $this->playerModel = new AugustusPlayerModel($em);
        $this->cardModel = new AugustusCardModel($em);
        $this->gameInfo = $this->manager->getRepository('AGORAPlatformBundle:GameInfo')->findOneBy(array('gameCode' => "aug"));
    }

    public function createRoom($name, $playersNb, $user) {
        return $this->gameModel->createGame($name, $playersNb, $user);
    }

    public function initLeaderboard($user) {
        if ($this->manager->getRepository('AGORAPlatformBundle:Leaderboard')
                ->findOneBy(array('userId' => $user->getId(), 'gameInfoId' => $this->gameInfo)) == null) {
            $lb = new Leaderboard;
            $lb -> setGameInfoId($this->gameInfo);
            $lb -> setUserId($user);
            $lb -> setElo(2000);
            $lb -> setVictoryNb(0);
            $lb -> setLoseNb(0);
            $lb -> setEqualityNb(0);

            $this->manager->persist($lb);
            $this->manager->flush();
        }
    }

    //Fonction qui récupere le jeu en bdd
    public function getGame($gameId) {
        $game = $this->manager
            ->getRepository('AugustusBundle:AugustusGame')
            ->findOneById($gameId);
        return $game;
    }

    //Fonction qui récupere le jeu en bdd
    public function getPlayerFromUser($user, $gameId) {
        $players = $this->manager->getRepository('AugustusBundle:AugustusPlayer');
        $player = $players->findOneBy([
            'userId' => $user->getId(),
            'game' => $gameId,
        ]);
        return $player;
    }

    public function getPlayerFromId($playerId, $gameId) {
        $players = $this->manager->getRepository('AugustusBundle:AugustusPlayer');
        $player = $players->findOneBy([
            'id' => $playerId
        ]);
        return $player;
    }

    public function getPlayers($gameId) {
        return $this->getGame($gameId)->getPlayers();
    }

    public function areAllPlayersReady($gameId) {
        return $this->gameModel->allOk($gameId);
    }

    public function setLastMovePlayedForPlayer($playerId) {
        $player = $this->manager->getRepository('AugustusBundle:AugustusPlayer')->find($playerId);
        $player->setLastMovePlayed(new \DateTime("now"));
        $this->manager->persist($player);
        $this->manager->flush();
    }

    /**
     * Retourne les informations d'une partie d'augustus à partir de son ID provenant de la table game.
     */
    public function getAugustusGameFromGame($gameId) {
        $games = $this->manager->getRepository("AGORAGameGameBundle:Game");
        $augGame = $games->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));

        return $augGame;
    }

    public function joinPlayer($user, $gameId) {
        $player = $this->getPlayerFromUser($user, $gameId);

        if ($player == null) {
            $retId = $this->playerModel->createPlayer($user, $gameId);

            if ($retId != -1) {
                $games = $this->manager->getRepository("AugustusBundle:AugustusGame");
                $game = $games->findOneById($gameId);
                $room = $this->getAugustusGameFromGame($gameId);

                $this->initLeaderboard($user);

                if (count($game->getPlayers()) == $room->getPlayersNb()) {
                    $this->gameModel->initGame($gameId);
                    $room->setState("started");
                    $this->manager->persist($room);
                    $this->manager->flush();
                }
            }

            return $retId;
        }

        return $player->getId();
    }

    /**
     * Termine la partie en calculant le classement
     */
    public function endGame($players, $gameId, $winner) {
        $this->gameService->computeELO($players, $gameId, $this->gameInfo, $winner);
    }

    /**
     * Fonction permettant de quitter la partie identifiée par $gameId si elle est non commencée.
     */
    public function quitGame($user, $gameId) {
        $player = $this->getPlayerFromUser($user, $gameId);
        if ($player != null) {
            $game = $this->manager->getRepository('AGORAGameGameBundle:Game')
                ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
            $playersNb = count($this->getPlayers($gameId));
            
            if ($playersNb == 1 || $player->getUserId()->getId() == $game->getHostId()->getId()) {
                $this->supressGame($gameId);
            } elseif (1 < $playersNb && $playersNb < $game->getPlayersNb()) {
                $augGame = $this->manager->getRepository('AugustusBundle:AugustusGame')->find($gameId);
                $board = $augGame->getBoard();
                $cards = $this->manager->getRepository('AugustusBundle:AugustusCard')
                        ->findBy(array('board' => $board->getId(), 'player' => $player));
                foreach ($cards as $card) {
                    $card->setPlayer(null);
                    $this->manager->flush($card);
                    $card->setPlayerCtrl(null);
                    $this->manager->flush($card);
                }
                
                $player->clearCards();
                $this->manager->flush($player);
                $player->clearCtrlCards();
                $this->manager->flush($player);
    
                $this->manager->remove($player);
                $this->manager->flush($player);
            } else {
                return;
            }
        }
    }

    public function supressGame($gameId) {
        $game = $this->manager->getRepository('AugustusBundle:AugustusGame')->find($gameId);
        $players = $this->manager->getRepository('AugustusBundle:AugustusPlayer')->findBy(array('game' => $gameId));

        $board = $game->getBoard();
        $cards = $this->manager->getRepository('AugustusBundle:AugustusCard')->findBy(array('board' => $board->getId()));
        foreach ($cards as $card) {
            $board->removeDeck($card);
            $this->manager->flush($board);
            $card->setPlayer(null);
            $this->manager->flush($card);
            $card->setPlayerCtrl(null);
            $this->manager->flush($card);
            $this->manager->remove($card);
            $this->manager->flush($card);
        }

        $board->clearTokenBag();
        $this->manager->flush($board);
        $board->clearDeck();
        $this->manager->flush($board);

        foreach ($players as $player) {
            $player->clearCards();
            $this->manager->flush($player);
            $player->clearCtrlCards();
            $this->manager->flush($player);

            $game->removePlayer($player);
            $this->manager->flush($game);

            $this->manager->remove($player);
            $this->manager->flush($player);
        }

        $game->setBoard(null);
        $this->manager->flush($game);
        $board->setGame(null);
        $this->manager->flush($board);

        $this->manager->remove($board);
        $this->manager->flush($board);

        $g = $this->manager->getRepository('AGORAGameGameBundle:Game')
                ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
        $this->manager->remove($g);
        $this->manager->flush($g);

        $this->manager->remove($game);
        $this->manager->flush($game);
    }
}
