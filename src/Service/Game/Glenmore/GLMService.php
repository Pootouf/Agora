<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class GLMService
{
    public function __construct(private EntityManagerInterface $entityManager,
        private TileGLMRepository $tileGLMRepository,
        private PlayerGLMRepository $playerGLMRepository) {}

    public function getActivePlayer(GameGLM $gameGLM): PlayerGLM
    {
        return $this->playerGLMRepository->findOneBy(["gameGLM" => $gameGLM->getId(),
            "turnOfPlayer" => true]);
    }

    public function endRoundOfPlayer(GameGLM $gameGLM, PlayerGLM $playerGLM, int $startPosition): void
    {
        $players = $gameGLM->getPlayers();
        foreach ($players as $player) {
            $player->setTurnOfPlayer(false);
            $this->entityManager->persist($player);
        }
        $nextPlayer = null;
        $pointerPosition = $startPosition + 1;
        while ($nextPlayer == null && $startPosition != $pointerPosition) {
            foreach ($players as $player) {
                $playerPosition = $player->getPawn()->getPosition();
                if ($playerPosition == $pointerPosition) {
                    $nextPlayer = $player;
                }
            }
            $pointerPosition = ($pointerPosition + 1) % GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;
        }
        if ($startPosition == $pointerPosition) {
            throw new Exception("Next player unreachable");
        }
        $nextPlayer->setTurnOfPlayer(true);
        $this->entityManager->persist($nextPlayer);
        $this->entityManager->flush();
    }

    /**
     * calculatePointsAtEndOfLevel : adds points to each player in gameGLM
     *
     * @param GameGLM $gameGLM
     * @return void
     */
    public function calculatePointsAtEndOfLevel(GameGLM $gameGLM): void
    {
        $playersWhiskyAmounts = $this->getSortedListResource($gameGLM,
            GlenmoreParameters::$WHISKY_RESOURCE);
        $this->computePoints($playersWhiskyAmounts);
        $playersLeaderAmounts = $this->getSortedListLeader($gameGLM);
        $this->computePoints($playersLeaderAmounts);
        $playersCardAmounts = $this->getSortedListCard($gameGLM);
        $this->computePoints($playersCardAmounts);
        $this->entityManager->flush();
    }

    /**
     * calculatePointsAtEndOfGame : adds points to each player in gameGLM
     *
     * @param GameGLM $gameGLM
     * @return void
     */
    public function calculatePointsAtEndOfGame(GameGLM $gameGLM): void
    {
        //TODO METHODE POUR CHECK LES 3 CARTES SPECIALES
        // TODO AJOUTER POINTS
        $playersMoneyAmount = $this->getSortedListMoney($gameGLM);
        $this->computePoints($playersMoneyAmount);

        $playersTileAmount = $this->getSortedListTile($gameGLM);
        $this->retrievePoints($playersTileAmount);
        $this->entityManager->flush();
    }

    /**
     * getSortedListResource : returns sorted list of (players, resourceAmount) by amount of resources
     *      of resourceType
     *
     * @param GameGLM $gameGLM
     * @param String  $resourceType
     * @return array
     */
    private function getSortedListResource(GameGLM $gameGLM, string $resourceType): array
    {
        $players = $gameGLM->getPlayers();
        $result = array();
        foreach ($players as $player) {
            $personalBoard = $player->getPersonalBoard();
            $playerTiles = $personalBoard->getPlayerTiles();
            $playerResource = 0;
            foreach ($playerTiles as $tile) {
                $resources = $tile->getResources();
                foreach ($resources as $resource) {
                    if ($resource->getType() === $resourceType) {
                        ++$playerResource;
                    }
                }
            }
            $result[] = array($player, $playerResource);
        }
        usort($result, function($x, $y) {
            return $x[1] - $y[1];
        });
        return $result;
    }

    /**
     * getPointsPerDifference : returns points to get per difference
     *
     * @param $difference
     * @return int
     * @throws Exception
     */
    private function getPointsPerDifference($difference): int
    {
        return match ($difference) {
            $difference < 0 => throw new Exception("difference can't be negative"),
            $difference <= 3 => $difference,
            4 => 5,
            default => 8,
        };
    }

    /**
     * getSortedListLeader : returns sorted list of (players, resourceAmount) by amount of leaders
     *
     * @param GameGLM $gameGLM
     * @return array
     */
    private function getSortedListLeader(GameGLM $gameGLM): array
    {
        $players = $gameGLM->getPlayers();
        $result = array();
        foreach ($players as $player) {
            $personalBoard = $player->getPersonalBoard();
            $playerResource = $personalBoard->getLeaderCount();
            $result[] = array($player, $playerResource);
        }
        usort($result, function($x, $y) {
            return $x[1] - $y[1];
        });
        return $result;
    }

    /**
     * getSortedListLeader : returns sorted list of (players, resourceAmount) by amount of cards
     *
     * @param GameGLM $gameGLM
     * @return array
     */
    private function getSortedListCard(GameGLM $gameGLM): array
    {
        $players = $gameGLM->getPlayers();
        $result = array();
        foreach ($players as $player) {
            $personalBoard = $player->getPersonalBoard();
            $playerResource = $personalBoard->getCards()->count();
            $result[] = array($player, $playerResource);
        }
        usort($result, function($x, $y) {
            return $x[1] - $y[1];
        });
        return $result;
    }

    /**
     * getSortedListMoney : returns sorted list of (players, resourceAmount) by amount of money
     *
     * @param GameGLM $gameGLM
     * @return array
     */
    private function getSortedListMoney(GameGLM $gameGLM): array
    {
        $players = $gameGLM->getPlayers();
        $result = array();
        foreach ($players as $player) {
            $personalBoard = $player->getPersonalBoard();
            $playerResource = $personalBoard->getMoney();
            $result[] = array($player, $playerResource);
        }
        usort($result, function($x, $y) {
            return $x[1] - $y[1];
        });
        return $result;
    }

    /**
     * getSortedListTile : returns sorted list of (players, resourceAmount) by amount of tile
     *
     * @param GameGLM $gameGLM
     * @return array
     */
    private function getSortedListTile(GameGLM $gameGLM): array
    {
        $players = $gameGLM->getPlayers();
        $result = array();
        foreach ($players as $player) {
            $personalBoard = $player->getPersonalBoard();
            $playerResource = $personalBoard->getPlayerTiles()->count();
            $result[] = array($player, $playerResource);
        }
        usort($result, function($x, $y) {
            return $x[1] - $y[1];
        });
        return $result;
    }

    /**
     * computePoints : adds points to each player
     *
     * @param $playersResources
     * @return void
     */
    private function computePoints($playersResources): void
    {
        $minResource = $playersResources[0][1];
        for ($i = 1; $i < count($playersResources); ++$i) {
            $player = $playersResources[$i][0];
            $resourceAmount = $playersResources[$i][1];
            $difference = $resourceAmount - $minResource;
            $points = $this->getPointsPerDifference($difference);
            $player->setPoints($player->getPoints() + $points);
            $this->entityManager->persist($player);
        }
    }

    /**
     * retrievePoints : removes points to each player
     *
     * @param $playersResources
     * @return void
     */
    private function retrievePoints($playersResources): void
    {
        $minResource = $playersResources[0][1];
        for ($i = 1; $i < count($playersResources); ++$i) {
            $player = $playersResources[$i][0];
            $resourceAmount = $playersResources[$i][1];
            $difference = $resourceAmount - $minResource;
            $player->setPoints($player->getPoints() - 3 * $difference);
            $this->entityManager->persist($player);
        }
    }
}