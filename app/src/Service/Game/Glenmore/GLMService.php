<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Repository\Game\Glenmore\DrawTilesGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class GLMService
{
    public function __construct(private EntityManagerInterface $entityManager,
        private TileGLMRepository $tileGLMRepository,
        private DrawTilesGLMRepository $drawTilesGLMRepository,
        private ResourceGLMRepository $resourceGLMRepository,
        private PlayerGLMRepository $playerGLMRepository,
        private CardGLMService $cardGLMService)
    {}


    public function getActivePlayer(GameGLM $gameGLM): PlayerGLM
    {
        return $this->playerGLMRepository->findOneBy(["gameGLM" => $gameGLM->getId(),
            "turnOfPlayer" => true]);
    }

    /**
     * getPlayerFromNameAndGame : return the player associated with a username and a game
     * @param GameGLM $game
     * @param string  $name
     * @return ?PlayerGLM
     */
    public function getPlayerFromNameAndGame(GameGLM $game, string $name): ?PlayerGLM
    {
        return $this->playerGLMRepository->findOneBy(['gameGLM' => $game->getId(), 'username' => $name]);
    }


    /**
     * getTilesFromGame : return the tiles from the board with the given game
     * @param GameGLM $game
     * @return Collection
     */
    public function getTilesFromGame(GameGLM $game): Collection
    {
        return $game->getMainBoard()->getBoardTiles();
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
        $this->cardGLMService->applyIonaAbbey($gameGLM);
        $this->cardGLMService->applyDuartCastle($gameGLM);
        $this->cardGLMService->applyLochMorar($gameGLM);

        $playersMoneyAmount = $this->getSortedListMoney($gameGLM);
        $this->computePoints($playersMoneyAmount);

        $playersTileAmount = $this->getSortedListTile($gameGLM);
        $this->retrievePoints($playersTileAmount);
        $this->entityManager->flush();
    }


    public function initializeNewGame(GameGLM $game) : void
    {
        $tilesLevelZero = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_ZERO]);
        $tilesLevelOne = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_ONE]);
        $tilesLevelTwo = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_TWO]);
        $tilesLevelThree = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_THREE]);

        $drawLevelZero = $this->drawTilesGLMRepository->findOneBy(
            ['mainBoardGLM' => $game->getMainBoard()->getId(),
                'level' => GlenmoreParameters::$TILE_LEVEL_ZERO]);
        $drawLevelOne = $this->drawTilesGLMRepository->findOneBy(
            ['mainBoardGLM' => $game->getMainBoard()->getId(),
                'level' => GlenmoreParameters::$TILE_LEVEL_ONE]);
        $drawLevelTwo = $this->drawTilesGLMRepository->findOneBy(
            ['mainBoardGLM' => $game->getMainBoard()->getId(),
                'level' => GlenmoreParameters::$TILE_LEVEL_TWO]);
        $drawLevelThree = $this->drawTilesGLMRepository->findOneBy(
            ['mainBoardGLM' => $game->getMainBoard()->getId(),
                'level' => GlenmoreParameters::$TILE_LEVEL_THREE]);

        foreach ($tilesLevelZero as $tile) $drawLevelZero->addTile($tile);
        foreach ($tilesLevelOne as $tile) $drawLevelOne->addTile($tile);
        foreach ($tilesLevelTwo as $tile) $drawLevelTwo->addTile($tile);
        foreach ($tilesLevelThree as $tile) $drawLevelThree->addTile($tile);
        $this->entityManager->persist($drawLevelZero);
        $this->entityManager->persist($drawLevelOne);
        $this->entityManager->persist($drawLevelTwo);
        $this->entityManager->persist($drawLevelThree);

        $startVillages = $this->tileGLMRepository->findBy(['name' => GlenmoreParameters::$TILE_NAME_START_VILLAGE]);
        $villager = $this->resourceGLMRepository->findOneBy(['type' => GlenmoreParameters::$VILLAGER_RESOURCE]);
        foreach ($game->getPlayers() as $player) {
            $tile = array_pop($startVillages);
            $playerTile = new PlayerTileGLM();
            $playerTile->setTile($tile);
            $playerTileResource = new PlayerTileResourceGLM();
            $playerTileResource->setResource($villager);
            $playerTileResource->setQuantity(1);
            $playerTile->addPlayerTileResource($playerTileResource);
            $player->getPersonalBoard()->addPlayerTile($playerTile);
            $this->entityManager->persist($playerTileResource);
            $this->entityManager->persist($playerTile);

            $player->getPersonalBoard()->setMoney(GlenmoreParameters::$START_MONEY);
            $this->entityManager->persist($player->getPersonalBoard());
        }

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
            $playerResource = $this->cardGLMService->applyCastleOfMey($personalBoard, $playerResource);
            foreach ($personalBoard->getPlayerTiles() as $tile) {
                $resources = $tile->getResources();
                foreach ($resources as $resource) {
                    if($resource->getType() == GlenmoreParameters::$HAT_RESOURCE) {
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