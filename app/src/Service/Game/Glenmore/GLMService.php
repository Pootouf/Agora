<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\TileGLM;
use App\Repository\Game\Glenmore\BoardTileGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileResourceGLMRepository;
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
use App\Service\Game\LogService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

class GLMService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
        private readonly TileGLMRepository $tileGLMRepository,
        private readonly TileGLMService $tileGLMService,
        private readonly LogService $logService,
        private readonly DrawTilesGLMRepository $drawTilesGLMRepository,
        private readonly ResourceGLMRepository $resourceGLMRepository,
        private readonly PlayerGLMRepository $playerGLMRepository,
        private readonly BoardTileGLMRepository $boardTileGLMRepository,
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
            $playerResources = 0;
            foreach ($playerTiles as $playerTile) {
                foreach ($playerTile->getPlayerTileResource() as $resource) {
                    if ($resource->getResource()->getType() === GlenmoreParameters::$PRODUCTION_RESOURCE) {
                        $playerResources += $resource->getQuantity();
                    }
                }
            }
            if ($playerResources > $nbResource) {
                $result->clear();
                $result->add($player);
                $nbResource = $playerResources;
            } else if ($playerResources == $nbResource) {
                $result->add($player);
            }
        }
        return $result;
    }

    /**
     * isInMovementPhase : indicates if player is in movement phase
     * @param PlayerGLM $playerGLM
     * @return bool
     */
    public function isInMovementPhase(PlayerGLM $playerGLM): bool
    {
        $phase = $playerGLM->getRoundPhase();
        if ($phase == GlenmoreParameters::$MOVEMENT_PHASE) {
            return true;
        }
        return false;
    }

    /**
     * isInBuyingPhase : indicates if player is in buying phase
     * @param PlayerGLM $playerGLM
     * @return bool
     */
    public function isInBuyingPhase(PlayerGLM $playerGLM): bool
    {
        $phase = $playerGLM->getRoundPhase();
        if ($phase == GlenmoreParameters::$BUYING_PHASE) {
            return true;
        }
        return false;
    }

    /**
     * isInSellingPhase : indicates if player is in selling phase
     * @param PlayerGLM $playerGLM
     * @return bool
     */
    public function isInSellingPhase(PlayerGLM $playerGLM): bool
    {
        $phase = $playerGLM->getRoundPhase();
        if ($phase == GlenmoreParameters::$SELLING_PHASE) {
            return true;
        }
        return false;
    }

    /**
     * isInActivationPhase : indicates if player is in activation phase
     * @param PlayerGLM $playerGLM
     * @return bool
     */
    public function isInActivationPhase(PlayerGLM $playerGLM): bool
    {
        $phase = $playerGLM->getRoundPhase();
        if ($phase == GlenmoreParameters::$ACTIVATION_PHASE) {
            return true;
        }
        return false;
    }


    /**
     * manageEndOfRound : at the end of a player's round, replace the good number of tiles, proceeds
     *  to count points if needed. Finally, ends the game if the game must end
     * @param GameGLM $gameGLM
     * @return void
     * @throws Exception
     */
    public function manageEndOfRound(GameGLM $gameGLM) : void
    {
        $activePlayer = $this->getActivePlayer($gameGLM);
        $mainBoard = $gameGLM->getMainBoard();

        $this->endRoundOfPlayer($gameGLM, $activePlayer, $mainBoard->getLastPosition());

        $newPlayer = $this->getActivePlayer($gameGLM);
        $amountOfTilesToReplace = $this->tileGLMService->getAmountOfTileToReplace($mainBoard);
        $drawTiles = $this->tileGLMService->getActiveDrawTile($gameGLM);
        $oldLevel = $drawTiles->getLevel();
        $newLevel = $oldLevel;

        for ($i = 0; $i < $amountOfTilesToReplace; ++$i) {
            $this->tileGLMService->placeNewTile($newPlayer, $drawTiles);
            $drawTiles = $this->tileGLMService->getActiveDrawTile($gameGLM);
            if ($drawTiles == null) {
                break;
            }
            $newLevel = $drawTiles->getLevel();
        }
        if ($newLevel > $oldLevel) {
            $this->calculatePoints($gameGLM, $newLevel);
        }
        if ($this->isGameEnded($gameGLM)) {
            // TODO RETURN CODE TO PUBLISH WINNERS
            $winners = $this->getWinner($gameGLM);
            $message = "";
            foreach ($winners as $winner) {
                $message .=  $winner->getUsername() . " ";
            }
            $message .= " won the game " . $gameGLM->getId();
            $this->logService->sendSystemLog($gameGLM, $message);
        } else {
            // TODO RETURN CODE TO PUBLISH
        }
        if ($newPlayer->isBot()) {
            $this->manageEndOfRound($gameGLM);
        }
    }


    /**
     * manageEndOfRound : proceeds to count players' points depending on draw tiles level
     * @param GameGLM $gameGLM
     * @param int     $drawLevel
     * @return void
     * @throws Exception
     */
    public function calculatePoints(GameGLM $gameGLM, int $drawLevel) : void
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
            $player->getPersonalBoard()->setActivatedTile(null);
            $player->getPersonalBoard()->setBuyingTile(null);
            $player->setRoundPhase(GlenmoreParameters::$STABLE_PHASE);
            $player->getPersonalBoard()->setActivatedTile(null);
            $player->getPersonalBoard()->setBuyingTile(null);
            $this->entityManager->persist($player);
            $this->entityManager->persist($player->getPersonalBoard());
        }
        $nextPlayer = null;
        $pointerPosition = ($startPosition + 1) % GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
        $found = false;
        while ($nextPlayer == null && $startPosition != $pointerPosition) {
            foreach ($players as $player) {
                $playerPosition = $player->getPawn()->getPosition();
                if ($playerPosition == $pointerPosition) {
                    $nextPlayer = $player;
                    $found = true;
                }
            }
            if ($found) {
                break;
            }
            $pointerPosition = ($pointerPosition + 1) % GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
        }
        if ($startPosition == $pointerPosition) {
            throw new Exception("Next player unreachable");
        }
        $nextPlayer->setTurnOfPlayer(true);
        $nextPlayer->setRoundPhase(GlenmoreParameters::$BUYING_PHASE);
        foreach ($nextPlayer->getPersonalBoard()->getPlayerTiles() as $playerTile) {
            $playerTile->setActivated(false);
            if ($playerTile->getTile()->getType() === GlenmoreParameters::$TILE_TYPE_CASTLE
                || $playerTile->getTile()->getType() === GlenmoreParameters::$TILE_TYPE_VILLAGE) {
                $this->clearMovementPoints($playerTile);
            }
            $this->entityManager->persist($playerTile);
            $this->entityManager->persist($nextPlayer->getPersonalBoard());
        }
        $this->entityManager->persist($nextPlayer);

        if ($nextPlayer->isBot()) {
            $this->manageBotAction($nextPlayer);
        }

        $this->entityManager->flush();
    }

    /**
     * setPhase : set player's phase into selected phase
     * @param PlayerGLM $playerGLM
     * @param int       $phase
     * @return void
     */
    public function setPhase(PlayerGLM $playerGLM, int $phase) : void
    {
        $playerGLM->setRoundPhase($phase);
        $this->entityManager->persist($playerGLM);
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

        $this->computePointsWithMoney($gameGLM);

        $playersTileAmount = $this->getSortedListTile($gameGLM);
        $this->retrievePoints($playersTileAmount);
        $this->entityManager->flush();
    }


    /**
     * initializeNewGame: initialize the game for the first round
     *
     * @param GameGLM $game
     * @return void
     */
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
            $this->initializePlayerBoard($player, $startVillages, $villager);
        }
        $game->getPlayers()->first()->setTurnOfPlayer(true);
        $position = 0;
        foreach ($game->getPlayers() as $player) {
            $this->initializePawn($player, $game, $position);
            $position++;
        }
        if ($game->getPlayers()->count() < GlenmoreParameters::$MINIMUM_NUMBER_PLAYER_FOR_NO_BOT) {
            $this->initializeBot($game, $position, $startVillages, $villager);
            $position++;
        }
        while ($position < GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD - 1) {
            $this->initializeNewTile($game, $position, $tilesLevelZero, $tilesLevelOne);
            $position++;
        }
        $this->initializeDraws($tilesLevelZero, $tilesLevelOne, $tilesLevelTwo, $tilesLevelThree,
                               $drawLevelZero, $drawLevelOne, $drawLevelTwo, $drawLevelThree);
        $this->initializeWarehouse($game);

        $this->entityManager->persist($game->getMainBoard());
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
            if ($player->isBot()) {
                continue;
            }
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
            if ($player->isBot()) {
                continue;
            }
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
            if ($player->isBot()) {
                continue;
            }
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
            if ($player->isBot()) {
                continue;
            }
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
     * computePointsWithMoney : adds to each player one point for each money coin he owns
     *
     * @param GameGLM $gameGLM
     * @return void
     */
    private function computePointsWithMoney(GameGLM $gameGLM): void
    {
        $players = $gameGLM->getPlayers();
        foreach ($players as $player) {
            if ($player->isBot()) {
                continue;
            }
            $resourceAmount = $player->getPersonalBoard()->getMoney();
            $player->setPoints($player->getPoints() + $resourceAmount);
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

    /**
     * addWarehouseLineToWarehouse: create and add a warehouseLine to the warehouse with the selected options
     *
     * @param WarehouseGLM $warehouse
     * @param ResourceGLM $resource
     * @param int $coinNumber
     * @return void
     */
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
     * clearMovementPoints : given a tile of type village or castle, clears remaining movement points
     *  at the end of player's round
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     */
    private function clearMovementPoints(PlayerTileGLM $playerTileGLM) : void
    {
        $playerTileResources = $playerTileGLM->getPlayerTileResource();
        $movement = $this->resourceGLMRepository->findOneBy(["type" => GlenmoreParameters::$MOVEMENT_RESOURCE]);
        foreach ($playerTileResources as $playerTileResource) {
            if ($playerTileResource->getResource() === $movement) {
                $playerTileResource->setQuantity(0);
                $this->entityManager->persist($playerTileResource);
            }
        }
        $this->entityManager->persist($playerTileGLM);
        $this->entityManager->flush();
    }

    /**
     * initializeBot: initialize the bot
     * @param GameGLM $game
     * @param int $position
     * @return void
     */
    private function initializeBot(GameGLM $game, int $position, array &$startVillages, ResourceGLM $villager) : void
    {
        $bot = new PlayerGLM(GlenmoreParameters::$BOT_NAME, $game);
        $bot->setBot(true);
        $personalBoard = new PersonalBoardGLM();
        $personalBoard->setLeaderCount(0);
        $personalBoard->setMoney(0);
        $bot->setPersonalBoard($personalBoard);
        $this->entityManager->persist($personalBoard);
        $dice = new PawnGLM();
        $dice->setColor(GlenmoreParameters::$COLOR_WHITE);
        $dice->setDice(true);
        $dice->setPosition($position);
        $dice->setMainBoardGLM($game->getMainBoard());
        $dice->setPlayerGLM($bot);
        $this->entityManager->persist($dice);
        $bot->setPawn($dice);
        $bot->setPoints(0);
        $bot->setRoundPhase(GlenmoreParameters::$STABLE_PHASE);
        $game->addPlayer($bot);
        $game->getMainBoard()->addPawn($dice);
        $this->initializePlayerBoard($bot, $startVillages, $villager);
        $this->entityManager->persist($game->getMainBoard());
        $this->entityManager->persist($bot);
        $this->entityManager->persist($game);
    }

    /**
     * initializeNewTile: initialize a new tile on the main board
     * @param GameGLM $game
     * @param int $position
     * @param array $tilesLevelZero
     * @param array $tilesLevelOne
     * @return void
     */
    private function initializeNewTile(GameGLM $game, int $position, array &$tilesLevelZero, array &$tilesLevelOne) : void
    {
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
    }

    /**
     * initializePlayerBoard: initialize the personalBoard of a player
     * @param PlayerGLM $player
     * @param array $startVillages
     * @param ResourceGLM $villager
     * @return void
     */
    private function initializePlayerBoard(PlayerGLM $player, array &$startVillages, ResourceGLM $villager) : void
    {
        $tile = array_pop($startVillages);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($villager);
        $playerTileResource->setPlayer($player);
        $playerTileResource->setQuantity(1);
        $playerTile->addPlayerTileResource($playerTileResource);
        $player->getPersonalBoard()->addPlayerTile($playerTile);
        $this->entityManager->persist($playerTileResource);
        $this->entityManager->persist($playerTile);

        $player->getPersonalBoard()->setMoney(GlenmoreParameters::$START_MONEY);
        $this->entityManager->persist($player->getPersonalBoard());
    }

    /**
     * initializePawn: initialize the pawn of the player
     * @param PlayerGLM $player
     * @param GameGLM $game
     * @param int $position
     * @return void
     */
    private function initializePawn(PlayerGLM $player, GameGLM $game, int $position) : void
    {
        $pawn = $player->getPawn();
        $pawn->setPosition($position);
        $game->getMainBoard()->addPawn($pawn);
        $this->entityManager->persist($pawn);
    }


    /**
     * initializeWarehouse: initialize the warehouse
     * @param GameGLM $game
     * @return void
     */
    private function initializeWarehouse(GameGLM $game) : void
    {
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
    }

    /**
     * initializeDraws: initialize the draws of the game
     * @param array $tilesLevelZero
     * @param array $tilesLevelOne
     * @param array $tilesLevelTwo
     * @param array $tilesLevelThree
     * @param DrawTilesGLM $drawLevelZero
     * @param DrawTilesGLM $drawLevelOne
     * @param DrawTilesGLM $drawLevelTwo
     * @param DrawTilesGLM $drawLevelThree
     * @return void
     */
    private function initializeDraws(array &$tilesLevelZero,
                                     array &$tilesLevelOne,
                                     array &$tilesLevelTwo,
                                     array &$tilesLevelThree,
                                     DrawTilesGLM $drawLevelZero,
                                     DrawTilesGLM $drawLevelOne,
                                     DrawTilesGLM $drawLevelTwo,
                                     DrawTilesGLM $drawLevelThree) : void
    {
        foreach ($tilesLevelZero as $tile) $drawLevelZero->addTile($tile);
        foreach ($tilesLevelOne as $tile) $drawLevelOne->addTile($tile);
        foreach ($tilesLevelTwo as $tile) $drawLevelTwo->addTile($tile);
        foreach ($tilesLevelThree as $tile) $drawLevelThree->addTile($tile);
        $this->entityManager->persist($drawLevelZero);
        $this->entityManager->persist($drawLevelOne);
        $this->entityManager->persist($drawLevelTwo);
        $this->entityManager->persist($drawLevelThree);
    }

    /**
     * manageBotAction: manage the movement of the dice of the bot on the mainBoard
     * @param PlayerGLM $bot
     * @return void
     * @throws Exception
     */
    private function manageBotAction(PlayerGLM $bot): void
    {
        $randomValue = rand(1, 6);
        $finalValue = 0;
        switch ($randomValue) {
            case 1 :
            case 2 :
            case 3 : $finalValue = 1;
            break;
            case 4 :
            case 5 : $finalValue = 2;
            break;
            case 6 : $finalValue = 3;
        }

        $position = $bot->getPawn()->getPosition();
        $bot->getPawn()->setPosition($bot->getPawn()->getPosition() + $finalValue);
       if ($bot->getPawn()->getPosition() >= GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD) {
            $bot->getPawn()->setPosition(0);
        }
        $pawns = $bot->getGameGLM()->getMainBoard()->getPawns();
        while ($pawns->filter(function (PawnGLM $pawn) use ($bot) {
            return $pawn->getId() != $bot->getPawn()->getId()
                && $pawn->getPosition() == $bot->getPawn()->getPosition();
        })->count() > 0) {
            $bot->getPawn()->setPosition($bot->getPawn()->getPosition() + 1);
            if ($bot->getPawn()->getPosition() >= GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD) {
                $bot->getPawn()->setPosition(0);
            }
        }
        $this->entityManager->persist($bot->getPawn());
        $tile = $this->boardTileGLMRepository->findOneBy([
            'mainBoardGLM' => $bot->getGameGLM()->getMainBoard()->getId(),
            'position' => $bot->getPawn()->getPosition()
        ]);
        $this->entityManager->remove($tile);
        $bot->getGameGLM()->getMainBoard()->setLastPosition($position);
        $this->entityManager->persist($bot->getGameGLM()->getMainBoard());
        $this->entityManager->flush();

    }
}