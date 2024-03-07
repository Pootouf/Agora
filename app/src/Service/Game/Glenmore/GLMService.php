<?php

namespace App\Service\Game\Glenmore;

use App\Service\Game\Glenmore\CardGLMService;
use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Entity\Game\Glenmore\WarehouseLineGLM;
use App\Repository\Game\Glenmore\DrawTilesGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

class GLMService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
        private readonly TileGLMRepository $tileGLMRepository,
        private readonly DrawTilesGLMRepository $drawTilesGLMRepository,
        private readonly ResourceGLMRepository $resourceGLMRepository,
        private readonly PlayerGLMRepository $playerGLMRepository,
        private readonly CardGLMService $cardGLMService)
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

    /**
     * getActiveDrawTile : returns the draw tile with the lowest level which is not empty
     *                      or null if all draw tiles are empty
     * @param GameGLM $gameGLM
     * @return DrawTilesGLM|null
     */
    public function getActiveDrawTile(GameGLM $gameGLM) : ?DrawTilesGLM
    {
        $mainBoard = $gameGLM->getMainBoard();
        $drawTiles = $mainBoard->getDrawTiles();
        for ($i = GlenmoreParameters::$TILE_LEVEL_ZERO; $i <= GlenmoreParameters::$TILE_LEVEL_THREE; ++$i) {
            $draw = $drawTiles->get($i);
            if ($draw->getTiles()->isEmpty()) {
                return $draw;
            }
        }
        return null;
    }

    /**
     * isGameEnded : checks if a game must end or not
     * @param GameGLM $gameGLM
     * @return bool
     */
    public function isGameEnded(GameGLM $gameGLM) : bool
    {
        return $gameGLM->getMainBoard()->getDrawTiles()->last()->getTiles()->isEmpty();
    }

    /**
     * getWinner : returns the winner(s) of the game
     * @param GameGLM $gameGLM
     * @return ArrayCollection<Int, PlayerGLM>
     */
    public function getWinner(GameGLM $gameGLM) : ArrayCollection
    {
        $winners = new ArrayCollection();
        $players = $gameGLM->getPlayers();
        $maxPoint = 0;
        foreach ($players as $player) {
            if ($player->getPoints() > $maxPoint) {
                $maxPoint = $player->getPoints();
                $winners->clear();
                $winners->add($player);
            } else if ($player->getPoints() == $maxPoint) {
                $winners->add($player);
            }
        }
        if ($winners->count() == 1) {
            return $winners;
        }
        $nbResource = 0;
        $result = new ArrayCollection();
        foreach ($winners as $player) {
            $personalBoard = $player->getPersonalBoard();
            $playerTiles = $personalBoard->getPlayerTiles();
            if ($playerTiles->count() > $nbResource) {
                $nbResource = $playerTiles->count();
                $result->clear();
                $result->add($player);
            } else if ($player->getPoints() == $nbResource) {
                $winners->add($player);
            }
        }
        return $result;
    }

    /**
     * manageEndOfRound : proceeds to count players' points depending on draw tiles level
     * @param GameGLM $gameGLM
     * @param int     $drawLevel
     * @return void
     * @throws Exception
     */
    public function manageEndOfRound(GameGLM $gameGLM, int $drawLevel) : void
    {
        switch ($drawLevel) {
            case 1:
            case 2:
                $this->calculatePointsAtEndOfLevel($gameGLM);
                break;
            case 3:
                $this->calculatePointsAtEndOfLevel($gameGLM);
                $this->calculatePointsAtEndOfGame($gameGLM);
                break;
            default:
                throw new Exception("impossible case");
        }

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
            $pointerPosition = ($pointerPosition + 1) % GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
        }
        if ($startPosition == $pointerPosition) {
            throw new Exception("Next player unreachable");
        }
        $nextPlayer->setTurnOfPlayer(true);
        foreach ($nextPlayer->getPersonalBoard()->getPlayerTiles() as $playerTile) {
            $playerTile->setActivated(false);
            $this->entityManager->persist($playerTile);
            $this->entityManager->persist($nextPlayer->getPersonalBoard());
        }
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
        shuffle($tilesLevelZero);
        shuffle($tilesLevelOne);
        shuffle($tilesLevelTwo);
        shuffle($tilesLevelThree);

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

        $startVillages = $this->tileGLMRepository->findBy(['name' => GlenmoreParameters::$TILE_NAME_START_VILLAGE]);
        $villager = $this->resourceGLMRepository->findOneBy(['type' => GlenmoreParameters::$VILLAGER_RESOURCE]);
        foreach ($game->getPlayers() as $player) {
            $tile = array_pop($startVillages);
            $playerTile = new PlayerTileGLM();
            $playerTile->setTile($tile);
            $playerTile->setCoordX(0);
            $playerTile->setCoordY(0);
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
        $game->getPlayers()->first()->setTurnOfPlayer(true);
        $position = 0;
        foreach ($game->getPlayers() as $player) {
            $pawn = new PawnGLM();
            $pawn->setPlayerGLM($player);
            $pawn->setColor(GlenmoreParameters::$COLOR_FROM_POSITION[$position]);
            $pawn->setPosition($position);
            $game->getMainBoard()->addPawn($pawn);
            $this->entityManager->persist($pawn);
            $position++;
        }
        while ($position < GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD - 1) {
            $tile = new BoardTileGLM();
            $gameTile = null;
            if (!empty($tilesLevelZero)) {
                $gameTile = array_pop($tilesLevelZero);
            } else {
                $gameTile = array_pop($tilesLevelOne);
            }
            $tile->setTile($gameTile);
            $tile->setMainBoardGLM($game->getMainBoard());
            $tile->setPosition($position);
            $game->getMainBoard()->addBoardTile($tile);
            $this->entityManager->persist($tile);
            $position++;
        }

        foreach ($tilesLevelZero as $tile) $drawLevelZero->addTile($tile);
        foreach ($tilesLevelOne as $tile) $drawLevelOne->addTile($tile);
        foreach ($tilesLevelTwo as $tile) $drawLevelTwo->addTile($tile);
        foreach ($tilesLevelThree as $tile) $drawLevelThree->addTile($tile);
        $this->entityManager->persist($drawLevelZero);
        $this->entityManager->persist($drawLevelOne);
        $this->entityManager->persist($drawLevelTwo);
        $this->entityManager->persist($drawLevelThree);

        $green_cube = $this->resourceGLMRepository->findOneBy(
            ['type' => GlenmoreParameters::$PRODUCTION_RESOURCE, 'color' => GlenmoreParameters::$COLOR_GREEN]
        );
        $yellow_cube = $this->resourceGLMRepository->findOneBy(
            ['type' => GlenmoreParameters::$PRODUCTION_RESOURCE, 'color' => GlenmoreParameters::$COLOR_YELLOW]
        );
        $brown_cube = $this->resourceGLMRepository->findOneBy(
            ['type' => GlenmoreParameters::$PRODUCTION_RESOURCE, 'color' => GlenmoreParameters::$COLOR_BROWN]
        );
        $white_cube = $this->resourceGLMRepository->findOneBy(
            ['type' => GlenmoreParameters::$PRODUCTION_RESOURCE, 'color' => GlenmoreParameters::$COLOR_WHITE]
        );
        $grey_cube = $this->resourceGLMRepository->findOneBy(
            ['type' => GlenmoreParameters::$PRODUCTION_RESOURCE, 'color' => GlenmoreParameters::$COLOR_GREY]
        );
        $numberOfCoin = 0;
        if ($game->getPlayers()->count() != GlenmoreParameters::$MAX_NUMBER_OF_PLAYER - 1) {
            $numberOfCoin = 1;
        }
        $warehouse = $game->getMainBoard()->getWarehouse();
        $this->addWarehouseLineToWarehouse($warehouse, $green_cube, $numberOfCoin);
        $this->addWarehouseLineToWarehouse($warehouse, $yellow_cube, $numberOfCoin);
        $this->addWarehouseLineToWarehouse($warehouse, $brown_cube, $numberOfCoin);
        $this->addWarehouseLineToWarehouse($warehouse, $white_cube, $numberOfCoin);
        $this->addWarehouseLineToWarehouse($warehouse, $grey_cube, $numberOfCoin);

        $this->entityManager->persist($warehouse);
        $this->entityManager->persist($game->getMainBoard());
        $this->entityManager->flush();
    }

    private function addWarehouseLineToWarehouse(WarehouseGLM $warehouse, ResourceGLM $resource, int $coinNumber): void
    {
        $warehouseLine = new WarehouseLineGLM();
        $warehouseLine->setWarehouseGLM($warehouse);
        $warehouseLine->setResource($resource);
        $warehouseLine->setCoinNumber($coinNumber);
        $quantity = $coinNumber == GlenmoreParameters::$COIN_NEEDED_FOR_RESOURCE_ONE ? 1 :
                ($coinNumber == GlenmoreParameters::$COIN_NEEDED_FOR_RESOURCE_TWO ? 2 :
                ($coinNumber == GlenmoreParameters::$COIN_NEEDED_FOR_RESOURCE_THREE ? 3 : 0));
        $warehouseLine->setQuantity($quantity);
        $this->entityManager->persist($warehouseLine);
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
                $resources = $tile->getPlayerTileResource();
                foreach ($resources as $resource) {
                    if ($resource->getResource()->getType() === $resourceType) {
                        $playerResource += $resource->getQuantity();
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
        if ($difference < 0) {
            throw new Exception("difference can't be negative");
        }
        return match ($difference) {
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 5,
            default => 8
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
            $leaderCount = $personalBoard->getLeaderCount();
            $playerResource = $this->cardGLMService->applyCastleOfMey($personalBoard, $leaderCount);
            foreach ($personalBoard->getPlayerTiles() as $tile) {
                $resources = $tile->getPlayerTileResource();
                foreach ($resources as $resource) {
                    if($resource->getResource()->getType() == GlenmoreParameters::$HAT_RESOURCE) {
                        $playerResource += $resource->getQuantity();
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
     * getSortedListCard : returns sorted list of (players, resourceAmount) by amount of cards
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
            $playerResource = $personalBoard->getPlayerCardGLM()->count();
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