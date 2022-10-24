<?php

/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 21/04/18
 * Time: 22:21
 */

namespace AGORA\Game\AveCesarBundle\Service;


use AGORA\Game\AveCesarBundle\Entity\AveCesarGame;
use AGORA\Game\AveCesarBundle\Entity\AveCesarPlayer;
use AGORA\Game\GameBundle\Entity\Game;
use Doctrine\ORM\EntityManager;
use AGORA\PlatformBundle\Entity\Leaderboard;
use FOS\UserBundle\Model\UserInterface;

class AveCesarService
{
    protected $manager;
    protected $gameService;
    private $gameInfo;

    //On construit notre api avec un entity manager permettant l'accès à la base de données
    public function __construct(EntityManager $em, $gs)
    {
        $this->manager = $em;
        $this->gameService = $gs;
        $this->gameInfo = $this->manager->getRepository('AGORAPlatformBundle:GameInfo')->findOneBy(array('gameCode' => "avc"));
    }

    public function createRoom($name, $playersNb, $user)
    {
        $boardId = $playersNb > 4 ? 1 : 2;
        $avcGame = new AveCesarGame($boardId);
        $avcGame->setNextplayer(-1);
        $avcGame->setFirstplayer(0);
        $this->manager->persist($avcGame);
        $this->manager->flush();

        $game = new Game();
        $game->setGameId($avcGame->getId());
        $game->setGameInfoId($this->gameInfo);
        $game->setGameName($name);
        $game->setPlayersNb($playersNb);
        $game->setHostId($user);
        $game->setState("waiting");
        $game->setCreationDate(new \DateTime("now"));
        $game->setCurrentPlayer($user->getId());
        $this->manager->persist($game);
        $this->manager->flush();

        return $avcGame->getId();
    }

    public function initLeaderboard($user)
    {
        if (
            $this->manager->getRepository('AGORAPlatformBundle:Leaderboard')
            ->findOneBy(array('userId' => $user->getId(), 'gameInfoId' => $this->gameInfo)) == null
        ) {
            $lb = new Leaderboard;
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

    public function createPlayer(UserInterface $user, int $gameId)
    {
        $avcgame = $this->manager->getRepository('AGORAGameAveCesarBundle:AveCesarGame')->find($gameId);
        if ($avcgame == null) {
            throw new \Exception();
        }

        $game = $this->manager->getRepository('AGORAGameGameBundle:Game')
            ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
        $players = $this->manager->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findBy(array('gameId' => $gameId));
        $currentPlayerNb = count($players);
        $expectedPlayerNb = $game->getPlayersNb();

        if ($currentPlayerNb >= $expectedPlayerNb) {
            return -1;
        }

        $player = new AveCesarPlayer();
        $player->setGameId($avcgame);
        $player->setHand("");

        // Génération de la prochaine position de départ
        $player->setPosition("0" . chr(ord('b') + $currentPlayerNb));
        $player->setLap(1);
        $player->setUserId($user);
        $player->setCesar(false);
        //$player->setDeck($this->newDeck());
        $player->setFinish(0);

        $deck = preg_split("/,/", $this->newDeck());
        $hand = array_splice($deck, -3);
        $player->setHand($this->arrayToString($hand));
        $player->setDeck($this->arrayToString($deck));

        $this->manager->persist($player);
        $this->manager->flush();
        $this->setFirstPlayer($player->getId(), $gameId);

        if ($this->getNextPlayer($gameId) == -1) {
            $this->setNextPlayer($gameId, $player->getId());
        }

        /* if (($currentPlayerNb + 1) == $expectedPlayerNb) {
            //$this->initPlayers($gameId);
            $game->setState("started");
            $this->manager->persist($game);
            $this->manager->flush();
        } */
        $this->flush();
        return $player->getId();
    }

    public function getHand(int $playerId)
    {
        $player = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->find($playerId);
        $hand = preg_split("/,/", $player->getHand());
        return $hand;
    }

    public function getPlayerFromUserId(int $gameId, int $userId)
    {
        $player = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        return $player;
    }

    public function getPlayer(int $gameId, int $playerId)
    {
        $player = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findOneBy(array('gameId' => $gameId, 'id' => $playerId));
        return $player;
    }

    public function getPlayerName(int $playerId)
    {
        $player = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->find($playerId);
        return $player->getUserId()->getUsername();
    }

    public function getAllPlayers(int $gameId)
    {
        $this->flush();
        $players = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findBy(array('gameId' => $gameId));
        return $players;
    }

    public function getPlayers(int $gameId)
    {
        return $this->getAllPlayers($gameId);
    }

    public function playerAlreadyCreated(int $gameId, int $userId)
    {
        $player = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findOneBy(array('gameId' => $gameId, 'userId' => $userId));
        return $player != null;
    }

    public function getMaxPlayer(int $gameId)
    {
        $game = $this->manager
            ->getRepository('AGORAGameGameBundle:Game')
            ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
        return $game->getPlayersNb();
    }

    public function getGame(int $gameId): AveCesarGame
    {
        $game = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarGame')
            ->find($gameId);
        return $game;
    }

    public function getGameName(int $gameId)
    {
        $game = $this->manager->getRepository('AGORAGameGameBundle:Game')
            ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));

        return $game->getGameName();
    }

    public function movePlayer(int $playerId, string $position, $card)
    {
        /** @var AveCesarPlayer $player */
        $player = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->find($playerId);
        $oldPosition = $player->getPosition();
        $deck = preg_split("/,/", $player->getDeck());
        $hand = preg_split("/,/", $player->getHand());
        $player->setPosition($position);
        if (count($deck) == 0) {
            return -1;
        }
        if ($player->getLap() < 3 && !$player->getCesar()) {
            if ($this->isCesarWay($oldPosition, $position, $card)) {
                $player->setCesar(true);
            }
        }
        if ($this->isNextLap($oldPosition, $position)) {
            $lap = $player->getLap();
            $player->setLap($lap + 1);
        }
        $drawnCard = array_splice($deck, -1)[0];
        $hand[array_search($card, $hand)] = $drawnCard;
        $player->setHand($this->arrayToString($hand));
        $player->setDeck($this->arrayToString($deck));
        $this->manager->persist($player);
        $this->manager->flush();
        return intval($drawnCard);
    }

    public function initPlayers(int $gameId)
    {
        $players = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findBy(array('gameId' => $gameId));
        //$i = 'a';
        foreach ($players as $p) {
            /*$deck = preg_split("/,/", $p->getDeck());
            $hand = array_splice($deck, -3);
            $p->setHand($this->arrayToString($hand));
            $p->setDeck($this->arrayToString($deck));*/
            //$p->setPosition('1' . $i);
            $this->manager->persist($p);
            $this->manager->flush();
            //++$i;
        }
        $this->manager->flush();
    }

    public function setNextPlayer(int $gameId, int $playerId)
    {
        $game = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarGame')
            ->find($gameId);

        $games = $this->manager->getRepository("AGORAGameGameBundle:Game");
        $avcGame = $games->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
        $nextPlayer = $this->getNextPlayer($gameId);
        $avcGame->setCurrentPlayer($nextPlayer);
        $game->setNextplayer($playerId);
        $this->manager->persist($game);
        $this->manager->flush();
    }

    public function getNextPlayer(int $gameId)
    {
        $game = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarGame')
            ->find($gameId);
        $player = $game->getNextplayer();
        return $player;
    }

    private function newDeck()
    {
        $deck = array();
        $k = 0;
        for ($i = 1; $i <= 6; ++$i) {
            for ($j = 0; $j < 4; ++$j) {
                $deck[$k] = $i;
                ++$k;
            }
        }
        for ($i = 23; $i > 0; $i--) {
            $j = random_int(0, 23);
            $x = $deck[$i];
            $deck[$i] = $deck[$j];
            $deck[$j] = $x;
        }
        return $this->arrayToString($deck);
    }

    public function isFirstPlayer(int $playerId, int $gameId)
    {
        $game = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarGame')
            ->find($gameId);
        $player = $game->getFirstplayer();
        return $player == $playerId;
    }

    public function setFirstPlayer(int $playerId, int $gameId)
    {
        $game = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarGame')
            ->find($gameId);
        $game->setFirstplayer($playerId);
        $this->manager->persist($game);
        $this->manager->flush();
    }

    public function getFirstPlayer(int $gameId)
    {
        $game = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarGame')
            ->find($gameId);
        $firstPlayerId = $game->getFirstplayer();
        return $this->getPlayer($gameId, $firstPlayerId);
    }

    private function arrayToString($cards)
    {
        $string = "";
        foreach ($cards as $c) {
            $string .= $c . ",";
        }
        return trim($string, ", ");
    }

    private function isNextLap($oldPosition, $newPosition)
    {
        $oldPosition = substr($oldPosition, 0, -1);
        $newPosition = substr($newPosition, 0, -1);
        $result = false;
        if ($oldPosition >= 24 && $oldPosition <= 29 && $newPosition >= 0 && $newPosition < 7) {
            $result = true;
        }
        return $result;
    }

    private function isCesarWay($oldPosition, $newPosition, $moveCount)
    {
        $cesarWay = array("30a", "31a", "0a", "1a", "2a");
        if (in_array($newPosition, $cesarWay)) {
            return true;
        }
        if ($oldPosition == "29a" && $newPosition == "3a" && $moveCount == 6) {
            return true;
        }
        return false;
    }

    public function finishPlayer(int $playerId, int $gameId)
    {
        $player = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findOneBy(array('gameId' => $gameId, 'id' => $playerId));
        $player->setFinish(true);
        $this->manager->persist($player);
        $this->manager->flush();
    }

    public function setGameState(int $gameId, $state)
    {
        $game = $this->manager->getRepository('AGORAGameGameBundle:Game')
            ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
        $game->setState($state);
        $this->manager->persist($game);
        $this->manager->flush();
    }

    public function getGameState(int $gameId)
    {
        $game = $this->manager->getRepository('AGORAGameGameBundle:Game')
            ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
        return $game->getState();
    }

    /**
     * Fonction permettant de quitter la partie identifiée par $gameId si elle est non commencée.
     */
    public function quitGame($user, int $gameId)
    {
        $player = $this->getPlayerFromUserId($gameId, $user->getId());
        if ($player != null) {
            $game = $this->manager->getRepository('AGORAGameGameBundle:Game')
                ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
            $playersNb = count($this->getPlayers($gameId));

            if ($playersNb == 1 || $player->getUserId()->getId() == $game->getHostId()->getId()) {
                $this->supressGame($gameId);
            } elseif (1 < $playersNb && $playersNb < $this->getMaxPlayer($gameId)) {
                $this->manager->remove($player);
                $this->manager->flush($player);
            } else {
                return;
            }
        }
    }

    public function supressGame(int $gameId)
    {
        $game = $this->manager->getRepository('AGORAGameAveCesarBundle:AveCesarGame')->find($gameId);
        $players = $this->manager->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')->findBy(array('gameId' => $gameId));
        foreach ($players as $player) {
            $this->manager->remove($player);
            $this->manager->flush($player);
        }

        $g = $this->manager->getRepository('AGORAGameGameBundle:Game')
            ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));
        $this->manager->remove($g);
        $this->manager->flush($g);

        $this->manager->remove($game);
        $this->manager->flush($game);
    }

    public function finishGame(int $gameId)
    {
        // Calcul de l'ELO
        $players = $this->manager->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findBy(array('gameId' => $gameId), array('finish' => 'ASC'));
        $winner = null;
        foreach ($players as $player) {
            $rank = $player->getFinish();
            // On attribue la dernière place au perdant car elle reste à 0
            if ($rank == 0) {
                $player->setFinish($this->getMaxPlayer($gameId));
                $this->manager->persist($player);
                $this->manager->flush();
                // on récupère le gagnant
            } else if ($rank == 1) {
                $winner = $player;
            }
        }
        $players = $this->manager->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findBy(array('gameId' => $gameId), array('finish' => 'ASC'));
        $this->gameService->computeELO($players, $gameId, $this->gameInfo, $winner);

        $this->supressGame($gameId);
    }

    public function isFinishPlayer(int $gameId, int $playerId)
    {
        return $this->getPlayer($gameId, $playerId)->getFinish() != 0;
    }

    public function isFinishGame(int $gameId)
    {
        $game = $this->manager->getRepository('AGORAGameGameBundle:Game')
            ->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $this->gameInfo));

        return $game->getState() == "finished";
    }

    public function getRanking(int $gameId)
    {
        $players = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findBy(array('gameId' => $gameId), array('finish' => 'ASC'));
        return array_map(function ($p) {
            return array($p->getUserId()->getUserName());
        }, $players);
    }

    public function isCesar(int $gameId, int $playerId)
    {
        $player = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findOneBy(array('gameId' => $gameId, 'id' => $playerId));
        return $player->getCesar();
    }

    public function flush()
    {
        $this->manager->flush();
    }

    public function getLap(int $gameId, int $playerId)
    {
        $player = $this->manager
            ->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')
            ->findOneBy(array('gameId' => $gameId, 'id' => $playerId));
        return $player->getLap();
    }

    public function setLastMovePlayedForPlayer(int $playerId)
    {
        $player = $this->manager->getRepository('AGORAGameAveCesarBundle:AveCesarPlayer')->find($playerId);
        $player->setLastMovePlayed(new \DateTime("now"));
        $this->manager->persist($player);
        $this->manager->flush();
    }
}
