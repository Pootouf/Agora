<?php

/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 21/04/18
 * Time: 22:11
 */

namespace AGORA\Game\AveCesarBundle\Model;

use AGORA\Game\AveCesarBundle\Service\AveCesarService;

class AveCesarTurnResolver
{

    private $maps;

    /**
     * @var AveCesarService
     */
    private $service;

    public function __construct(AveCesarService $service)
    {
        $this->service = $service;
        $util = new Util();
        $this->maps = $util->createMaps();
    }


    public function move($gameId, $playerId, $position, $card)
    {
        if (!$this->gameStarted($gameId)) return -1;
        $this->service->flush();
        $game = $this->service->getGame($gameId);
        if ($game->getNextPlayer() != $playerId) {
            return -1;
        }
        $players = $this->service->getAllPlayers($gameId);
        $otherPlayers = array();
        $i = 0;
        foreach ($players as $p) {
            if ($p->getId() == $playerId) {
                $player = $p;
            } else {
                $otherPlayers[$i] = $p;
                ++$i;
            }
        }

        $occupiedPositions = array_map(function ($p) {
            return $p->getPosition();
        }, $otherPlayers);

        //$hand = preg_split("/,/", $player->getHand());
        $this->service->flush();
        $hand = $this->service->getHand($playerId);

        if (
            $this->service->isFirstPlayer($playerId, $gameId)
            && $card == 6
            && !$this->firstCanPlaySix($this->service->getGame($gameId)->getBoardId(), $hand, $player->getPosition())
        ) {
            echo "First Player can't play 6 card.\n";
            return -1;
        }
        if (array_search(strval($card), $hand) === false) {
            echo "HAND OF PLAYER : " . count($hand) . "\n";
            echo "card " . $card . " not in hand : " . $player->getHand() . "\n";
            return -1;
        }
        echo "From : " . $player->getPosition() . " to " . $position . "\n";
        if ($this->canMoveTo($gameId, $player->getPosition(), $position, $occupiedPositions, $card)) {
            $drawnCard = $this->service->movePlayer($playerId, $position, $card);
            $this->updateFirstPlayer($gameId, $playerId);
            $finishReturn = $this->updateFinish($player, $game);
            if ($finishReturn) {
                return 0;
            }
            if ($drawnCard == -1) {
                echo "No more card in deck\n";
                return 0;
            }
            $nextPlayer = $this->calculateNextPlayer($players, $playerId);
            $this->service->setNextPlayer($gameId, $nextPlayer->getId());
            return $drawnCard;
        } else {
            echo "cant't move\n";
            return -1;
        }
    }

    public function pass($gameId, $playerId)
    {
        if (!$this->gameStarted($gameId)) return -1;
        $this->service->flush();
        $game = $this->service->getGame($gameId);
        if ($game->getNextPlayer() != $playerId) {
            return -1;
        }
        $players = $this->service->getAllPlayers($gameId);
        $otherPlayers = array();
        $i = 0;
        foreach ($players as $p) {
            if ($p->getId() == $playerId) {
                $player = $p;
            } else {
                $otherPlayers[$i] = $p;
                ++$i;
            }
        }
        $occupiedPositions = array_map(function ($p) {
            return $p->getPosition();
        }, $otherPlayers);
        $hand = preg_split("/,/", $player->getHand());
        if (!$this->canPass($playerId, $gameId, $player->getPosition(), $occupiedPositions, $hand)) {
            return -1;
        }
        $nextPlayer = $this->calculateNextPlayer($players, $playerId);
        $this->service->setNextPlayer($gameId, $nextPlayer->getId());
        $this->nextPlayer = $nextPlayer->getId();
        return 0;
    }

    // TODO - un vrai champ dans la BDD
    public function getNextPlayer($gameId)
    {
        return $this->service->getNextPlayer($gameId);
    }

    private function calculateNextPlayer($players, $currentPlayer)
    {
        $i = 0;
        echo $currentPlayer;
        while ($players[$i]->getId() != $currentPlayer) {
            ++$i;
        }
        $nextPlayer = $players[($i + 1) % count($players)];
        while ($nextPlayer->getFinish() != 0) {
            echo "loop\n";
            $i++;
            $nextPlayer = $players[($i + 1) % count($players)];
        }
        return $nextPlayer;
    }

    private function canPass($playerId, $gameId, $startPosition, $occupiedPositions, $cards)
    {
        $player = $this->service->getPlayer($gameId, $playerId);
        $hand = preg_split("/,/", $player->getHand());
        $firstId = $this->service->getFirstPlayer($gameId)->getId();
        if ($firstId == $playerId && $this->isHandOf($hand, "6") 
                && $this->isStuckPosition($this->service->getGame($gameId)->getBoardId(), $startPosition)) {
            return true;
        }
        $result = true;
        if ($firstId == $playerId) {
            foreach ($cards as $c) {
                if ($c != "6") {
                    $result = $result && !$this->canMove($gameId, $startPosition, $occupiedPositions, $c);
                }
            }
        } else {
            foreach ($cards as $c) {
                $result = $result && !$this->canMove($gameId, $startPosition, $occupiedPositions, $c);
            }
        }
        return $result;
    }

    private function canMove($gameId, $position, $occupiedPositions, $moveCount)
    {
        echo $position . "\n";
        if ($this->isOccupied($position, $occupiedPositions)) {
            return false;
        }
        if ($moveCount == 0) {
            return true;
        }
        $result = false;
        foreach ($this->maps[$this->boardId($gameId)]["$position"] as $nextPosition) {
            $result = $result || $this->canMove($gameId, $nextPosition, $occupiedPositions, $moveCount - 1);
        }
        return $result;
    }

    private function canMoveTo($gameId, $position, $targetPosition, $occupiedPositions, $moveCount)
    {
        echo $position . "\n";
        if ($this->isOccupied($position, $occupiedPositions)) {
            return false;
        }
        if ($position == $targetPosition && $moveCount == 0) {
            return true;
        }
        if ($moveCount == 0) {
            return false;
        }
        $result = false;
        foreach ($this->maps[$this->boardId($gameId)]["$position"] as $nextPosition) {
            $result = $result || $this->canMoveTo($gameId, $nextPosition, $targetPosition, $occupiedPositions, $moveCount - 1);
        }
        return $result;
    }

    /**
     * @param $position position a tester
     * @param $occupiedPositions tableau des poisitions occupées
     * @return bool, si la position est occupé
     */
    private function isOccupied($position, $occupiedPositions)
    {
        return in_array($position, $occupiedPositions);
    }

    /**
     * Met à jour FirstPlayer si cela est necessaire
     * @param $newPlayer
     */
    private function updateFirstPlayer($gameId, $playerId)
    {
        $player = $this->service->getPlayer($gameId, $playerId);
        $firstPlayer = $this->service->getFirstPlayer($gameId);
        if ($firstPlayer == null) {
            $this->service->setFirstPlayer($playerId, $gameId);
            return;
        }
        if ($player->getId() == $firstPlayer->getId()) {
            // No Update
            return;
        }
        if ($player->getLap() < $firstPlayer->getLap()) {
            // No Update
            return;
        }
        $newPosition = substr($player->getPosition(), 0, -1);
        $firstPosition = substr($firstPlayer->getPosition(), 0, -1);
        if ($player->getLap() == $firstPlayer->getLap()) {
            if ($newPosition <= $firstPosition) {
                // No Update
                return;
            }
        }
        $this->service->setFirstPlayer($playerId, $gameId);
    }

    /**
     * Met a jour le rank du joueur si il a fini
     */
    private function updateFinish($player, $game)
    {
        if ($player->getLap() >= 4) {
            $players = $this->service->getAllPlayers($game->getId());
            $ranks = array_map(function ($p) {
                return $p->getFinish();
            }, $players);
            $max = array_reduce($ranks, function ($a, $b) {
                return max($a, $b);
            }, 0);
            $player->setFinish($max + 1);
            $nbFinish = array_reduce(
                $ranks,
                function ($carry, $rank) {
                    if ($rank > 0) {
                        return $carry + 1;
                    }
                    return $carry;
                },
                0
            );
            if ($nbFinish + 1 >= count($players) - 1) {
                $this->service->setGameState($game->getId(), "finished");
            }
        }
    }

    /**
     * Si la position est une position bloquante.
     * @param int $gameId
     * @param string $position
     * @return bool
     */
    private function isStuckPosition(int $boardId, string $position): bool
    {
        if ($boardId == 1) {
            return in_array($position, array("7a", "17a", "25a"));
        } else if ($boardId == 2) {
            return in_array($position, array("6a", "11a", "14b", "19b", "23a", "25a"));
        }
        return false;
    }

    /**
     * Indique si un joueur peut jouer avec que des 6 en mains.
     * @param int $gameId
     * @param $hand du joueurs
     * @param $position position actuel de joueur
     * @return bool si le joueur peux jouer
     */
    private function firstCanPlaySix(int $boardId, $hand, $position)
    {
        if ($this->isStuckPosition($boardId, $position)) {
            $occurence = array_count_values($hand);
            if (isset($occurence['6'])) {
                return $occurence['6'] == count($hand);
            }
        }
        return count($hand) == 0;
    }

    private function isHandOf($hand, $card)
    {
        $occurence = array_count_values($hand);
        if (isset($occurence[$card])) {
            return $occurence[$card] == count($hand);
        }
        return count($hand) == 0;
    }

    private function gameStarted(int $gameId): bool {
        return count($this->service->getPlayers($gameId)) == $this->service->getMaxPlayer($gameId);
    }

    private function boardId(int $gameId): int {
        return $this->service->getGame($gameId)->getBoardId();
    }
}
