<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\Myrmes\BoardTileMYR;
use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\MyrmesTranslation;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\GardenWorkerMYRRepository;
use App\Repository\Game\Myrmes\AnthillWorkerMYRRepository;
use App\Repository\Game\Myrmes\PheromonMYRRepository;
use App\Repository\Game\Myrmes\PheromonTileMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\PreyMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Repository\Game\Myrmes\TileTypeMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use function Symfony\Component\Translation\t;

class WorkerMYRService
{
    public function __construct(private readonly EntityManagerInterface      $entityManager,
                                private readonly MYRService                  $myrService,
                                private readonly AnthillWorkerMYRRepository  $anthillWorkerMYRRepository,
                                private readonly AnthillHoleMYRRepository    $anthillHoleMYRRepository,
                                private readonly PheromonMYRRepository       $pheromonMYRRepository,
                                private readonly PreyMYRRepository           $preyMYRRepository,
                                private readonly TileMYRRepository           $tileMYRRepository,
                                private readonly PlayerResourceMYRRepository $playerResourceMYRRepository,
                                private readonly ResourceMYRRepository       $resourceMYRRepository,
                                private readonly TileTypeMYRRepository       $tileTypeMYRRepository,
                                private readonly GardenWorkerMYRRepository   $gardenWorkerMYRRepository,
                                private readonly PheromonTileMYRRepository   $pheromonTileMYRRepository
    )
    {}

    /**
     * getNumberOfGardenWorkerOfPlayer: return the number of garden worker of the player
     * @param PlayerMYR $player
     * @return int
     */
    public function getNumberOfGardenWorkerOfPlayer(PlayerMYR $player) : int
    {
        return $player->getGardenWorkerMYRs()->count();
    }

    /**
     * getAvailablePheromones : returns a collection (type, amount, orientations) of all tile types for a player
     *
     * @param PlayerMYR $playerMYR
     * @return ArrayCollection<Int, array<Int, Int, Int>>
     * @throws Exception
     */
    public function getAvailablePheromones(PlayerMYR $playerMYR) : ArrayCollection
    {
        $result = new ArrayCollection();
        $this->getAvailablePheromonesFromAPlayer($playerMYR, MyrmesParameters::PHEROMONE_TYPE_AMOUNT,
        MyrmesParameters::PHEROMONE_TYPE_ZERO,
            MyrmesParameters::PHEROMONE_TYPE_SIX, MyrmesParameters::PHEROMONE_TYPE_ORIENTATIONS,
            $result);
        $this->getAvailablePheromonesFromAPlayer($playerMYR, MyrmesParameters::SPECIAL_TILE_TYPE_AMOUNT,
        MyrmesParameters::SPECIAL_TILE_TYPE_FARM, MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL,
        MyrmesParameters::SPECIAL_TILES_TYPE_ORIENTATIONS, $result);
        return $result;
    }

    /**
     * getAllAvailablePositions : returns every available positions from a tile of a certain type and orientation
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param int         $antCoordX
     * @param int         $antCoordY
     * @param array       $availableTiles
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    public function getAllAvailablePositions(PlayerMYR $player, TileMYR $tile,
        TileTypeMYR $tileType, int $antCoordX, int $antCoordY, array $availableTiles) : ArrayCollection
    {
        $type = $tileType->getType();
        return match ($type) {
            MyrmesParameters::PHEROMONE_TYPE_ZERO
                => $this->getAllAvailablePositionsFromTypeZero($player, $tile, $tileType,
                $antCoordX, $antCoordY, $availableTiles),
            MyrmesParameters::PHEROMONE_TYPE_ONE
                => $this->getAllAvailablePositionsFromTypeOne($player, $tile, $tileType,
                $antCoordX, $antCoordY, $availableTiles),
            MyrmesParameters::SPECIAL_TILE_TYPE_FARM,
            MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY,
            MyrmesParameters::PHEROMONE_TYPE_TWO
                => $this->getAllAvailablePositionsFromTypeTwo($player, $tile, $tileType,
                $antCoordX, $antCoordY, $availableTiles),
            MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL,
            MyrmesParameters::PHEROMONE_TYPE_THREE
                => $this->getAllAvailablePositionsFromTypeThree($player, $tile, $tileType,
                $antCoordX, $antCoordY, $availableTiles),
            MyrmesParameters::PHEROMONE_TYPE_FOUR
                => $this->getAllAvailablePositionsFromTypeFour($player, $tile, $tileType,
                $antCoordX, $antCoordY, $availableTiles),
            MyrmesParameters::PHEROMONE_TYPE_FIVE
                => $this->getAllAvailablePositionsFromTypeFive($player, $tile, $tileType,
                $antCoordX, $antCoordY, $availableTiles),
            MyrmesParameters::PHEROMONE_TYPE_SIX
                => $this->getAllAvailablePositionsFromTypeSix($player, $tile, $tileType,
                $antCoordX, $antCoordY, $availableTiles),
            default => new ArrayCollection(),
        };
    }

    /**
     * canWorkerMove : check if worker can move depend on new tile.
     * @param PlayerMYR $player
     * @param GardenWorkerMYR $gardenWorker
     * @param int $direction
     * @return bool
     */
    public function canWorkerMove(PlayerMYR $player,
                                  GardenWorkerMYR $gardenWorker, int $direction) : bool
    {
        $tile =
            $this->getTileAtDirection($gardenWorker->getTile(), $direction);

        if(!$this->isValidPositionForAnt($tile)) {
            return false;
        }

        $prey = $this->getPreyOnTile($tile, $player->getGameMyr());
        $destinationPheromoneTile = $this->getPheromoneTileOnTile($tile, $player->getGameMyr());
        $originPheromoneTile = $this->getPheromoneTileOnTile($gardenWorker->getTile(), $player->getGameMyr());
        $hasEnoughShiftsCount = $gardenWorker->getShiftsCount() > 0;

        return ($prey == null && $originPheromoneTile == null
                && $destinationPheromoneTile == null && $hasEnoughShiftsCount)
            || ($prey != null && $this->canWorkerAttackPrey($player, $prey) && $hasEnoughShiftsCount)
            || (($originPheromoneTile != null || $destinationPheromoneTile != null)
                && $this->canWorkerWalkAroundPheromone(
                    $player, $originPheromoneTile, $destinationPheromoneTile, $gardenWorker
                ));
    }

    /**
     * getNeededSoldiers: return the needed soldiers for the player to move to the selected tile
     * @param int $coordX
     * @param int $coordY
     * @param GameMYR $gameMYR
     * @param PlayerMYR $player
     * @param array<array<int, int>> $cleanedTiles
     * @return int
     */
    public function getNeededSoldiers(
        int $coordX, int $coordY, GameMYR $gameMYR, PlayerMYR $player, array $cleanedTiles
    ): int
    {
        if (in_array([$coordX, $coordY], $cleanedTiles)) {
            return 0;
        }
        $tile = $this->tileMYRRepository->findOneBy([
            "coordY" => $coordY,
            "coordX" => $coordX
        ]);
        if($tile == null) {
            throw new InvalidArgumentException("Not a valid tile, can't identify if there is a prey on it");
        }
        $prey = $this->getPrey($gameMYR, $tile);
        $pheromoneTile = $this->getPheromoneTileOnTile($tile, $gameMYR);
        $soldiersForPheromone = 0;
        if($pheromoneTile != null) {
            $soldiersForPheromone = $pheromoneTile->getPheromonMYR()->getPlayer() === $player ? 0 : 1;
        }

        return ($prey == null ? 0 : MyrmesParameters::NUMBER_SOLDIERS_FOR_ATTACK_PREY[$prey->getType()])
            + $soldiersForPheromone;
    }

    /**
     * isPreyOnTile: indicate if there is a prey on the tile at the selected coordinates, taking into account
     *               the cleaned tiles
     * @param int $coordX
     * @param int $coordY
     * @param GameMYR $game
     * @param array $cleanedTiles
     * @return bool
     */
    public function isPreyOnTile(int $coordX, int $coordY, GameMYR $game, array $cleanedTiles): bool
    {
        if (in_array([$coordX, $coordY], $cleanedTiles)) {
            return false;
        }
        $tile = $this->tileMYRRepository->findOneBy([
            "coordY" => $coordY,
            "coordX" => $coordX
        ]);
        if($tile == null) {
            throw new InvalidArgumentException("Not a valid tile, can't identify if there is a prey on it");
        }
        $prey = $this->getPrey($game, $tile);
        return $prey != null;
    }

    /**
     * getNeededMovementPoints : return the needed movement points for the player to move from the tile on
     *                                  coordX1, coordY1 position to coordX2, coordY2 position
     * @param int $coordX1
     * @param int $coordY1
     * @param int $coordX2
     * @param int $coordY2
     * @param GameMYR $gameMYR
     * @return int
     * @throws InvalidArgumentException
     */
    public function getNeededMovementPoints(int $coordX1, int $coordY1, int $coordX2, int $coordY2,
                                            GameMYR $gameMYR) : int
    {
        $tile1 = $this->tileMYRRepository->findOneBy([
            "coordY" => $coordY1,
            "coordX" => $coordX1
        ]);
        $tile2 = $this->tileMYRRepository->findOneBy([
            "coordY" => $coordY2,
            "coordX" => $coordX2
        ]);
        if ($tile1 == null || $tile2 == null) {
            throw new InvalidArgumentException("Not a valid tile, can't identify if there is a prey on it");
        }
        $pheromoneTile1 = $this->pheromonTileMYRRepository->findOneBy([
            "tile" => $tile1,
            "mainBoard" => $gameMYR->getMainBoardMYR()
        ]);
        $pheromoneTile2 = $this->pheromonTileMYRRepository->findOneBy([
            "tile" => $tile2,
            "mainBoard" => $gameMYR->getMainBoardMYR()
        ]);
        if ($pheromoneTile1 != null && $pheromoneTile2 != null
            && $pheromoneTile1->getPheromonMYR() == $pheromoneTile2->getPheromonMyr()
        ) {
            return 0;
        }
        return 1;
    }

    /**
     * isValidPositionForAnt : is the tile valid to place a ant on it
     * @param TileMYR|null $tile
     * @return bool
     */
    public function isValidPositionForAnt(?TileMYR $tile): bool
    {
        if ($tile == null
            || $tile->getType() == MyrmesParameters::WATER_TILE_TYPE)
        {
            return false;
        }
        return true;
    }

    /**
     * canCleanPheromone : indicate if the given player can clean the pheromone on the given tile
     * @param PheromonMYR $pheromone
     * @param int $playerDirtQuantity
     * @return bool
     */
    public function canCleanPheromone(PheromonMYR $pheromone, int $playerDirtQuantity): bool
    {
        $type = $pheromone->getType();
        if (in_array($type->getType(), MyrmesParameters::SPECIAL_TILE_TYPES)) {
            return false;
        }

        $pheromoneTiles = $pheromone->getPheromonTiles();
        foreach($pheromoneTiles as $tile) {
            if($tile->getResource() != null) {
                return false;
            }
        }
        if($playerDirtQuantity < 1) {
            return false;
        }
        return true;
    }

    /**
     * getPheromoneFromTile : return the pheromone if it exists with the given tile
     * @param GameMYR $game
     * @param TileMYR $tile
     * @return ?PheromonMYR
     */
    public function getPheromoneFromTile(GameMYR $game, TileMYR $tile): ?PheromonMYR
    {
        $pheromoneTile = $this->getPheromoneTileOnTile($tile, $game);
        return $pheromoneTile?->getPheromonMYR();
    }

    /**
     * getStringCoordsOfPheromoneTiles : return a formatted string containing the coordinates of each pheromone tiles
     *          of the given pheromone.
     * @param PheromonMYR $pheromone
     * @return string
     */
    public function getStringCoordsOfPheromoneTiles(PheromonMYR $pheromone): string
    {
        $result = "";
        foreach ($pheromone->getPheromonTiles() as $pheromonTile) {
            $result .= ($pheromonTile->getTile()->getCoordX() . "_" . $pheromonTile->getTile()->getCoordY() . " ");
        }
        return $result;
    }

    /**
     * getAllCoordinatesFromTileType : returns every coordinate a pheromone would cover
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param array<array<Int, Int>>       $availablePositions (optional)
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    public function getAllCoordinatesFromTileType(PlayerMYR $player,
        TileMYR $tile, TileTypeMYR $tileType, array $availablePositions = []) : ArrayCollection
    {
        switch ($tileType->getType()) {
            case MyrmesParameters::PHEROMONE_TYPE_ZERO:
                $result = $this->getAllCoordinatesOfPheromoneTypeZero($player, $tile, $tileType, $availablePositions);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_ONE:
                $result = $this->getAllCoordinatesOfPheromoneTypeOne($player, $tile, $tileType, $availablePositions);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_TWO:
                $result = $this->getAllCoordinatesOfPheromoneTypeTwo($player, $tile, $tileType, $availablePositions);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_THREE:
                $result = $this->getAllCoordinatesOfPheromoneTypeThree($player, $tile, $tileType, $availablePositions);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_FOUR:
                $result = $this->getAllCoordinatesOfPheromoneTypeFour($player, $tile, $tileType, $availablePositions);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_FIVE:
                $result = $this->getAllCoordinatesOfPheromoneTypeFive($player, $tile, $tileType, $availablePositions);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_SIX:
                $result = $this->getAllCoordinatesOfPheromoneTypeSix($player, $tile, $tileType, $availablePositions);
                break;
            case MyrmesParameters::SPECIAL_TILE_TYPE_FARM:
                if ($this->getAllCoordinatesOfPheromoneFarm($player)) {
                    $result = $this->getAllCoordinatesOfPheromoneTypeTwo(
                        $player, $tile, $tileType, $availablePositions
                    );
                } else {
                    $result = new ArrayCollection();
                }
                break;
            case MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY:
                if ($this->getAllCoordinatesOfPheromoneQuarry($player)) {
                    $result = $this->getAllCoordinatesOfPheromoneTypeTwo(
                        $player, $tile, $tileType, $availablePositions
                    );
                } else {
                    $result = new ArrayCollection();
                }
                break;
            case MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL:
                if ($this->getAllCoordinatesOfPheromoneSubanthill($player)) {
                    $result = $this->getAllCoordinatesOfPheromoneTypeThree(
                        $player, $tile, $tileType, $availablePositions
                    );
                } else {
                    $result = new ArrayCollection();
                }
                break;
            default:
                $result = new ArrayCollection();
        }
        return $result;
    }

    /**
     * getPlayerMovementPoints: return the player movement points
     * @param PlayerMYR $player
     * @return int
     */
    public function getPlayerMovementPoints(PlayerMYR $player): int
    {
        return MyrmesParameters::DEFAULT_MOVEMENT_NUMBER
        + ($player->getPersonalBoardMYR()->getBonus() == MyrmesParameters::BONUS_MOVEMENT ? 3 : 0);
    }

    /**
     * cleanPheromone : retrieve the pheromone from the main board; the action cost a dirt resource
     * @param PheromonMYR $pheromone
     * @param PlayerMYR $player
     * @return void
     * @throws InvalidArgumentException
     */
    public function cleanPheromone(PheromonMYR $pheromone, PlayerMYR $player) : void
    {
        $dirtResource = $player->getPersonalBoardMYR()->getPlayerResourceMYRs()->filter(
            function (PlayerResourceMYR $playerResourceMYR)
            {
                return $playerResourceMYR->getResource()->getDescription() == MyrmesParameters::DIRT_TILE_TYPE;
            }
        )->first();
        if(!$this->canCleanPheromone($pheromone, $dirtResource->getQuantity())) {
            throw new InvalidArgumentException("Can't clean the pheromone");
        }

        $dirtResource->setQuantity($dirtResource->getQuantity() - 1);
        $this->entityManager->persist($dirtResource);

        if ($pheromone->getPlayer() !== $player) {
            $player->setScore($player->getScore()
                + MyrmesParameters::PHEROMONE_TYPE_LEVEL[$pheromone->getType()->getType()]);
            $this->entityManager->persist($player);
        }

        foreach($pheromone->getPheromonTiles() as $pheromonTile) {
            $this->entityManager->remove($pheromonTile);
        }
        $this->entityManager->remove($pheromone);
        $this->entityManager->flush();
    }


    /**
     * placeAntInAnthill: place a free worker ant in the selected area of the anthill
     * @param PersonalBoardMYR $personalBoard
     * @param int $anthillFloor
     * @param String $lvlTwoResource
     * @return void
     * @throws Exception if invalid floor or no more free ants
     */
    public function placeAntInAnthill(PersonalBoardMYR $personalBoard, int $anthillFloor, ?String $lvlTwoResource) : void
    {
        $maxFloor = $personalBoard->getAnthillLevel();
        $isAnthillLevelIncreased = $personalBoard->getBonus() == MyrmesParameters::BONUS_LEVEL;
        if ($maxFloor + ($isAnthillLevelIncreased ? 1 : 0) < $anthillFloor) {
            throw new Exception('Invalid floor level');
        }
        $ant = $this->anthillWorkerMYRRepository->findOneBy([
            'personalBoardMYR' => $personalBoard,
            'workFloor' => MyrmesParameters::NO_WORKFLOOR
        ]);
        if(!$ant) {
            throw new Exception('No more free ants');
        }
        $antInFloor = $this->anthillWorkerMYRRepository->findOneBy([
            'personalBoardMYR' => $personalBoard,
            'workFloor' => $anthillFloor
        ]);
        if ($antInFloor) {
            throw new Exception('Already an ant at this position');
        }
        $ant->setWorkFloor($anthillFloor);
        $this->entityManager->persist($ant);
        $this->giveColonyLevelBonus($personalBoard, $anthillFloor, $lvlTwoResource);
        $this->entityManager->flush();
    }

    /**
     * takeOutAnt: allow the player to transform his ant into a garden worker ant
     * @param PersonalBoardMYR $personalBoard
     * @param AnthillHoleMYR $exitHole
     * @return void
     * @throws Exception if the hole is not an anthill hole of the player,
     *                  or if the player doesn't have anymore free ants,
     *                  or if there is already an ant at this location
     */
    public function takeOutAnt(PersonalBoardMYR $personalBoard, AnthillHoleMYR $exitHole) : void
    {
        if ($exitHole->getPlayer() !== $personalBoard->getPlayer()) {
            throw new Exception('Not an anthill hole of the player');
        }
        $ant = $this->anthillWorkerMYRRepository->findOneBy([
            'personalBoardMYR' => $personalBoard,
            'workFloor' => MyrmesParameters::NO_WORKFLOOR
        ]);
        if(!$ant) {
            throw new Exception('No more free ants');
        }
        $gardenWorker = $this->gardenWorkerMYRRepository->findOneBy([
            'mainBoardMYR' => $personalBoard->getPlayer()->getGameMyr()->getMainBoardMYR()->getId(),
            'tile' => $exitHole->getTile()
        ]);
        if ($gardenWorker != null) {
            throw new Exception('There is already an ant at this hole');
        }
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($exitHole->getTile());
        $gardenWorker->setPlayer($personalBoard->getPlayer());
        $gardenWorker->setMainBoardMYR($personalBoard->getPlayer()->getGameMyr()->getMainBoardMYR());
        $gardenWorker->setShiftsCount($this->getPlayerMovementPoints($personalBoard->getPlayer()));
        $this->entityManager->persist($gardenWorker);
        $this->entityManager->remove($ant);
        $this->entityManager->flush();
    }

    /**
     * placeAnthillHole : if player can place an anthill hole, it place it on the tile
     * @param PlayerMYR $playerMYR
     * @param TileMYR $tileMYR
     * @return void
     * @throws Exception
     */
    public function placeAnthillHole(PlayerMYR $playerMYR, TileMYR $tileMYR) : void
    {
        if(!$this->isPositionAvailable($playerMYR->getGameMyr(), $tileMYR)) {
            throw new Exception("Can't place anthill hole here");
        }
        $anthillHole = new AnthillHoleMYR();
        $anthillHole->setPlayer($playerMYR);
        $anthillHole->setTile($tileMYR);
        $anthillHole->setMainBoardMYR($playerMYR->getGameMyr()->getMainBoardMYR());
        $playerMYR->addAnthillHoleMYR($anthillHole);
        $this->entityManager->persist($anthillHole);
        $this->entityManager->persist($playerMYR);
        $this->entityManager->flush();
    }


    /**
     * canPlacePheromone : checks if a player can place a pheromone of a type on a tile
     *
     * @param PlayerMYR              $player
     * @param TileMYR                $tile
     * @param int                    $antCoordX
     * @param int                    $antCoordY
     * @param TileTypeMYR            $tileType
     * @param array<array<Int, Int>> $availablePositions (optional)
     * @return bool
     */
    public function canPlacePheromone(PlayerMYR $player, TileMYR $tile, int $antCoordX, int $antCoordY,
        TileTypeMYR $tileType, array $availablePositions = []) : bool
    {
        $pheromoneCount = $this->getPheromoneCountOfType($player, $tileType);
        try {
            if (!$this->canChoosePheromone($player, $tileType, $pheromoneCount)) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        if(!$this->canPlaceSpecialTile($player, $tileType)){
            return false;
        }
        try {
            $tiles = $this->getAllAvailablePositions($player, $tile, $tileType, $antCoordX, $antCoordY, $availablePositions);
        } catch (Exception $e) {
            return false;
        }
        return !$tiles->isEmpty();
    }

    /**
     * placePheromone : player tries to place a pheromone or a special tile on the selected tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param int         $antCoordX
     * @param int         $antCoordY
     * @return void
     * @throws Exception
     */
    public function placePheromone(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType, int $antCoordX, int $antCoordY) : void
    {
        if (!$this->canPlacePheromone($player, $tile, $antCoordX, $antCoordY, $tileType)) {
            throw new Exception("this pheromone can't be placed there");
        }
        $tiles = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
        $this->createAndPlacePheromone($tiles, $player, $tileType);
    }

    /**
     * killPlayerGardenWorker: remove the garden worker of the player
     * @param PlayerMYR $player
     * @return void
     */
    public function killPlayerGardenWorker(PlayerMYR $player) : void
    {
        $gardenWorker = $player->getGardenWorkerMYRs()->first();
        $this->entityManager->remove($gardenWorker);
        $this->entityManager->flush();
    }

    /**
     * workerMove : garden worker move on main board
     * @param PlayerMYR $player
     * @param GardenWorkerMYR $gardenWorker
     * @param int $direction
     * @return void
     * @throws Exception
     */
    public function workerMove(PlayerMYR $player,
       GardenWorkerMYR $gardenWorker, int $direction) : void
    {
        if (!$this->canWorkerMove($player, $gardenWorker, $direction))
        {
            throw new Exception("Cannot move ant in this direction,
                    maybe there is water or out of board, or maybe not enough resources");
        }
        $tile =
            $this->getTileAtDirection($gardenWorker->getTile(), $direction);
        $gardenWorker->setTile($tile);

        $prey = $this->getPreyOnTile($tile, $player->getGameMyr());
        $destinationPheromone = $this->getPheromoneTileOnTile($tile, $player->getGameMyr());
        $startPheromone = $this->getPheromoneTileOnTile($gardenWorker->getTile(), $player->getGameMyr());

        if ($prey != null)
        {
            $this->attackPrey($player, $prey);
        } elseif ($destinationPheromone != null
            && $destinationPheromone->getPheromonMYR()->getPlayer() !== $player)
        {
            $personalBoard = $player->getPersonalBoardMYR();

            $personalBoard->setWarriorsCount(
                $personalBoard->getWarriorsCount() - 1
            );
        }

        $this->entityManager->persist($gardenWorker);

        if (!($startPheromone != null
            && $destinationPheromone != null
            && $startPheromone->getPheromonMYR()->getPlayer() === $player
            && $destinationPheromone === $startPheromone))
        {
            $gardenWorker->setShiftsCount(
                $gardenWorker->getShiftsCount() - 1
            );
            $this->entityManager->persist($gardenWorker);
        }
        $this->entityManager->flush();
    }

    /**
     * getTileFromCoordinates : return the tile based on the given coordinates
     * @param int $coordX
     * @param int $coordY
     * @return TileMYR|null
     */
    public function getTileFromCoordinates(int $coordX, int $coordY): ?TileMYR
    {
        return $this->tileMYRRepository->findOneBy([
            "coordX" => $coordX,
            "coordY" => $coordY
        ]);
    }

    /**
     * getAllAvailablePositionsFromTypeZero : returns all available positions for a pheromone of type Zero
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param int         $antCoordX
     * @param int         $antCoordY
     * @param array       $availableTiles
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeZero(PlayerMYR $player, TileMYR $tile,
        TileTypeMYR $tileType, int $antCoordX, int $antCoordY, array $availableTiles) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        $translations = match ($orientation) {
            0 => [[0, 0], [-1, -1]],
            1 => [[0, 0], [-1, +1]],
            2 => [[0, 0], [0, +2]],
            3 => [[0, 0], [1, 1]],
            4 => [[0, 0], [1, -1]],
            5 => [[0, 0], [0, -2]],
            default => null,
        };
        if ($translations == null) {
            return new ArrayCollection();
        }
        $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType, $availableTiles);
        return $this->getAllAvailablePositionsFromOrientation($player, $coords,
            $translations, $antCoordX, $antCoordY, $availableTiles);
    }

    /**
     * getAllAvailablePositionsFromTypeOne : returns all available positions for a pheromone of type One
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param int         $antCoordX
     * @param int         $antCoordY
     * @param array       $availableTiles
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeOne(PlayerMYR $player, TileMYR $tile,
        TileTypeMYR $tileType, int $antCoordX, int $antCoordY, array $availableTiles) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        $translations = match ($orientation) {
            0 => [[0, 0], [-1, -1], [1, 1]],
            1 => [[0, 0], [0, +2], [0, -2]],
            2 => [[0, 0], [-1, +1], [1, -1]],
            default => null,
        };
        if ($translations == null) {
            return new ArrayCollection();
        }
        $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
        return $this->getAllAvailablePositionsFromOrientation($player, $coords,
            $translations, $antCoordX, $antCoordY, $availableTiles);
    }

    /**
     * getAllAvailablePositionsFromTypeTwo : returns all available positions for a pheromone of type Two
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param int         $antCoordX
     * @param int         $antCoordY
     * @param array       $availableTiles
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeTwo(PlayerMYR $player, TileMYR $tile,
        TileTypeMYR $tileType, int $antCoordX, int $antCoordY, array $availableTiles) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        $translations = match ($orientation) {
            0 => [[0, 0], [-1, 1], [-1, -1]],
            1 => [[0, 0], [-1, +1], [0, 2]],
            2 => [[0, 0], [0, +2], [1, 1]],
            3 => [[0, 0], [1, 1], [1, -1]],
            4 => [[0, 0], [1, -1], [0, -2]],
            5 => [[0, 0], [0, -2], [-1, -1]],
            default => null,
        };
        if ($translations == null) {
            return new ArrayCollection();
        }
        $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
        return $this->getAllAvailablePositionsFromOrientation($player, $coords,
            $translations, $antCoordX, $antCoordY, $availableTiles);
    }

    /**
     * getAllAvailablePositionsFromTypeThree : returns all available positions for a pheromone of type Three
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param int         $antCoordX
     * @param int         $antCoordY
     * @param array       $availableTiles
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeThree(PlayerMYR $player, TileMYR $tile,
        TileTypeMYR $tileType, int $antCoordX, int $antCoordY, array $availableTiles) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        $translations = match ($orientation) {
            0 => [[0, 0], [-2, 0], [-1, -1], [-1, 1]],
            1 => [[0, 0], [-1, 3], [-1, 1], [0, 2]],
            2 => [[0, 0], [0, 2], [1, 1], [1, 3]],
            3 => [[0, 0], [2, 0], [1, -1], [1, 1]],
            4 => [[0, 0], [0, -2], [1, -3], [1, -1]],
            5 => [[0, 0], [0, -2], [-1, -3], [-1, -1]],
            default => null,
        };
        if ($translations == null) {
            return new ArrayCollection();
        }
        $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
        return $this->getAllAvailablePositionsFromOrientation($player, $coords,
            $translations, $antCoordX, $antCoordY, $availableTiles);
    }

    /**
     * getAllAvailablePositionsFromTypeFour : returns all available positions for a pheromone of type Four
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param int         $antCoordX
     * @param int         $antCoordY
     * @param array       $availableTiles
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeFour(PlayerMYR $player, TileMYR $tile,
        TileTypeMYR $tileType, int $antCoordX, int $antCoordY, array $availableTiles) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        $translations = match ($orientation) {
            0 => [[0, 0], [-1, -1], [-2, -2], [0, -2]],
            1 => [[0, 0], [-2, 2], [-1, 1], [-1, -1]],
            2 => [[0, 0], [0, 2], [0, 4], [-1, 1]],
            3 => [[0, 0], [0, 4], [-1, 1], [0, 2]],
            4 => [[0, 0], [1, 1], [1, -1], [2, -2]],
            5 => [[0, 0], [0, -2], [0, -4], [1, -1]],
            6 => [[0, 0], [0, 2], [-1, 1], [-2, 2]],
            7 => [[0, 0], [1, 1], [0, 4], [0, 2]],
            8 => [[0, 0], [1, 1], [1, -1], [2, 2]],
            9 => [[0, 0], [0, -2], [1, -1], [2, -2]],
            10 => [[0, 0], [0, -2], [0, -4], [-1, -1]],
            11 => [[0, 0], [-1, 1], [-1, -1], [-2, -2]],
            default => null,
        };
        if ($translations == null) {
            return new ArrayCollection();
        }
        $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
        return $this->getAllAvailablePositionsFromOrientation($player, $coords,
            $translations, $antCoordX, $antCoordY, $availableTiles);
    }

    /**
     * getAllAvailablePositionsFromTypeFive : returns all available positions for a pheromone of type Five
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param int         $antCoordX
     * @param int         $antCoordY
     * @param array       $availableTiles
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeFive(PlayerMYR $player, TileMYR $tile,
        TileTypeMYR $tileType, int $antCoordX, int $antCoordY, array $availableTiles) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        $translations = match ($orientation) {
            0 => [[0, 0], [0, -2], [-1, -3], [-1, -1], [-1, 1]],
            1 => [[0, 0], [-1, -1], [-2, 0], [-1, 1], [0, 2]],
            2 => [[0, 0], [-1, 1], [-1, 3], [0, 2], [1, 1]],
            3 => [[0, 0], [0, 2], [1, 3], [1, 1], [1, -1]],
            4 => [[0, 0], [1, 1], [0, -2], [1, -1], [2, 0]],
            5 => [[0, 0], [1, -1], [1, -3], [0, -2], [-1, -1]],
            default => null,
        };
        if ($translations == null) {
            return new ArrayCollection();
        }
        $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
        return $this->getAllAvailablePositionsFromOrientation($player, $coords,
            $translations, $antCoordX, $antCoordY, $availableTiles);
    }

    /**
     * getAllAvailablePositionsFromTypeSix : returns all available positions for a pheromone of type Six
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param int         $antCoordX
     * @param int         $antCoordY
     * @param array       $availableTiles
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeSix(PlayerMYR $player, TileMYR $tile,
        TileTypeMYR $tileType, int $antCoordX, int $antCoordY, array $availableTiles) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        $translations = match ($orientation) {
            0 => [[0, 0], [-1, 1], [-1, -1], [-2, +2], [-2, 0], [-2, -2]],
            1 => [[0, 0], [-1, 1], [-2, 2], [-1, +3], [0, 4], [0, 2]],
            2 => [[0, 0], [0, 2], [0, 4], [1, +3], [2, 2], [1, 1]],
            3 => [[0, 0], [1, 1], [2, 2], [2, 0], [2, -2], [1, -1]],
            4 => [[0, 0], [1, -1], [2, -2], [1, -3], [0, -4], [0, -2]],
            5 => [[0, 0], [0, -2], [0, -4], [-1, -3], [-2, -2], [-1, -1]],
            default => null,
        };
        if ($translations == null) {
            return new ArrayCollection();
        }
        $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
        return $this->getAllAvailablePositionsFromOrientation($player, $coords,
            $translations, $antCoordX, $antCoordY, $availableTiles);
    }

    /**
     * getAllAvailablePositionsFromOrientation : returns a list of lists of tiles where player can place the pheromone
     *
     * @param PlayerMYR                                       $player
     * @param ArrayCollection<Int, TileMYR> $coords
     * @param Array<Int, Array<Int, Int>>                     $translations
     * @param int                                             $antCoordX
     * @param int                                             $antCoordY
     * @param array<array<Int, Int>>                          $availableTiles
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromOrientation(
        PlayerMYR $player, ArrayCollection $coords, array $translations,
        int $antCoordX, int $antCoordY, array $availableTiles
    ) : ArrayCollection
    {
        $game = $player->getGameMyr();
        $result = new ArrayCollection();
        foreach ($translations as $translation) {
            $isPivot = true;
            /** @var ArrayCollection<Int, BoardTileMYR> $tileList */
            $tileList = new ArrayCollection();
            $translationX = $translation[0];
            $translationY = $translation[1];
            $correctPlacement = true;
            foreach ($coords as $coord) {
                $coordX = $coord[0] + $translationX;
                $coordY = $coord[1] + $translationY;
                $newTile = $this->getTileAtCoordinate($coordX, $coordY);
                if ($newTile == null) {
                    $correctPlacement = false;
                    continue;
                }
                if (!($this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile))) {
                    $found = false;
                    foreach ($availableTiles as $availableTile) {
                        $availableTileX = $availableTile[0];
                        $availableTileY = $availableTile[1];
                        if ($availableTileX === $coordX && $availableTileY === $coordY) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $correctPlacement = false;
                        continue;
                    }
                }
                $boardTile = new BoardTileMYR($newTile, $isPivot);
                if ($isPivot) {
                    $isPivot = false;
                }
                $tileList->add($boardTile);
            }
            if ($correctPlacement && $this->containsAnt($tileList, $antCoordX, $antCoordY)) {
                $result->add($tileList);
            }
        }
        return $result;
    }

    /**
     * containsAnt : checks if any of the tile in the list contains an ant
     *
     * @param ArrayCollection<Int, BoardTileMYR> $tileList
     * @param int                                $antCoordX
     * @param int                                $antCoordY
     * @return bool
     */
    private function containsAnt(ArrayCollection $tileList, int $antCoordX, int $antCoordY) : bool
    {
        foreach ($tileList as $tile) {
            if ($tile->getTile()->getCoordX() === $antCoordX && $tile->getTile()->getCoordY() === $antCoordY) {
                return true;
            }
        }
        return false;
    }

    /**
     * getPreyOnTile : return prey on the tile or null
     * @param TileMYR $tile
     * @param GameMYR $game
     * @return PreyMYR|null
     */
    private function getPreyOnTile(TileMYR $tile, GameMYR $game) : ?PreyMYR
    {
        return $this->preyMYRRepository->findOneBy([
            "tile" => $tile,
            "mainBoardMYR" => $game->getMainBoardMYR()
        ]);
    }

    /**
     * getPheromoneTileOnTile : return pheromone on the tile or null
     * @param TileMYR $tile
     * @param GameMYR $game
     * @return PheromonTileMYR|null
     */
    private function getPheromoneTileOnTile(TileMYR $tile, GameMYR $game) : ?PheromonTileMYR
    {
        return $this->pheromonTileMYRRepository->findOneBy(
            [
                "tile" => $tile,
                "mainBoard" => $game->getMainBoardMYR()
            ]
        );
    }

    /**
     * canWorkerAttackPrey : check if worker can attack prey depend on soldiers of player
     * @param PlayerMYR $player
     * @param PreyMYR $prey
     * @return bool
     */
    private function canWorkerAttackPrey(PlayerMYR $player,
         PreyMYR $prey) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();
        $needSoldiers =
            MyrmesParameters::NUMBER_SOLDIERS_FOR_ATTACK_PREY[
                $prey->getType()
            ];

        return $personalBoard->getWarriorsCount() >= $needSoldiers;
    }

    /**
     * canWorkerWalkAroundPheromone : check if player can move his garden worker on tile
     * @param PlayerMYR $player
     * @param PheromonTileMYR|null $originPheromoneTile
     * @param PheromonTileMYR|null $destinationPheromoneTile
     * @param GardenWorkerMYR $gardenWorker
     * @return bool
     */
    private function canWorkerWalkAroundPheromone(PlayerMYR $player, ?PheromonTileMYR $originPheromoneTile,
                                                  ?PheromonTileMYR $destinationPheromoneTile,
                                                  GardenWorkerMYR $gardenWorker) : bool
    {
        if($originPheromoneTile != null && $destinationPheromoneTile != null
           && $originPheromoneTile->getPheromonMYR() === $destinationPheromoneTile->getPheromonMYR()) {
            return true;
        }
        if(($destinationPheromoneTile != null && $destinationPheromoneTile->getPheromonMYR()->getPlayer() === $player)
                || $destinationPheromoneTile == null) {
            return $gardenWorker->getShiftsCount() >= 1;
        }
        return $gardenWorker->getShiftsCount() >= 1 && $player->getPersonalBoardMYR()->getWarriorsCount() >= 1;
    }

    /**
     * getTileAtDirection : return tile which is on the given direction or null when tile not exists
     * @param TileMYR $tile
     * @param int $direction
     * @return TileMYR|null
     */
    private function getTileAtDirection(TileMYR $tile
        , int $direction) : ?TileMYR
    {
        $abscissa = $tile->getCoordX();
        $ordinate = $tile->getCoordY();

        return match ($direction) {
            MyrmesParameters::DIRECTION_NORTH_WEST =>
            $this->getTileAtCoordinate($abscissa - 1, $ordinate - 1),
            MyrmesParameters::DIRECTION_NORTH_EAST =>
            $this->getTileAtCoordinate($abscissa - 1, $ordinate + 1),
            MyrmesParameters::DIRECTION_EAST =>
            $this->getTileAtCoordinate($abscissa, $ordinate + 2),
            MyrmesParameters::DIRECTION_SOUTH_WEST =>
            $this->getTileAtCoordinate($abscissa + 1, $ordinate - 1),
            MyrmesParameters::DIRECTION_SOUTH_EAST =>
            $this->getTileAtCoordinate($abscissa + 1, $ordinate + 1),
            MyrmesParameters::DIRECTION_WEST =>
            $this->getTileAtCoordinate($abscissa, $ordinate - 2),
            default => null,
        };
    }

    /**
     * getTileAtCoordinate : return tile at coordinate
     * @param int $x
     * @param int $y
     * @return TileMYR|null
     */
    private function getTileAtCoordinate(int $x, int $y) : ?TileMYR
    {
        return $this->tileMYRRepository->findOneBy([
        "coordX" =>  $x,
        "coordY" => $y]);
    }

    /**
     * isPositionAvailable : checks if any player can place something on the tile in parameters
     * @param GameMYR $gameMYR
     * @param TileMYR $tileMYR
     * @return bool
     */
    private function isPositionAvailable(GameMYR $gameMYR, TileMYR $tileMYR) : bool
    {
        if($tileMYR->getType() == MyrmesParameters::WATER_TILE_TYPE) {
            return false;
        }
        $players = $gameMYR->getPlayers();
        foreach ($players as $player) {
            $anthill = $this->anthillHoleMYRRepository->findOneBy(["tile" => $tileMYR,
                "player" => $player]);
            if($anthill != null) {
                return false;
            }
            $playerPheromones = $this->pheromonMYRRepository->findBy(["player" => $player]);
            foreach ($playerPheromones as $playerPheromone) {
                $pheromoneTiles = $playerPheromone->getPheromonTiles();
                foreach ($pheromoneTiles as $pheromoneTile) {
                    if($pheromoneTile->getTile() === $tileMYR) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * attackPrey : soldiers of players attack prey
     * @param PlayerMYR $player
     * @param PreyMYR $prey
     * @return void
     */
    private function attackPrey(PlayerMYR $player, PreyMYR $prey) : void
    {
        $player->addPreyMYR($prey);
        $prey->setTile(null);

        // Manage count of soldiers
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setWarriorsCount($personalBoard->getWarriorsCount()
            - MyrmesParameters::NUMBER_SOLDIERS_FOR_ATTACK_PREY[
                $prey->getType()
            ]);

        // Manage score of player
        $points = MyrmesParameters::VICTORY_GAIN_BY_ATTACK_PREY[
        $prey->getType()];
        if ($points > 0) {
            ++$points;
        }
        $player->setScore($player->getScore() + $points);

        // Manage quantity of resource
        $playerResource = $this->myrService->getPlayerResourceOfType(
            $player,
            MyrmesParameters::RESOURCE_TYPE_GRASS);

        $playerResource->setQuantity($playerResource->getQuantity()
            + MyrmesParameters::FOOD_GAIN_BY_ATTACK_PREY[$prey->getType()]
        );

        // Update
        $this->entityManager->persist($playerResource);
        $this->entityManager->persist($player);
        $this->entityManager->persist($prey);
        $this->entityManager->persist($personalBoard);
    }

    /**
     * getAllCoordinatesOfPheromoneTypeZero : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param array<array<Int, Int>>       $availablePositions (optional)
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeZero(PlayerMYR $player,
        TileMYR $tile, TileTypeMYR $tileType, array $availablePositions = []) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();

        $coords = match ($tileType->getOrientation()) {
            0 => [[0, 0], [1, 1]],
            1 => [[0, 0], [1, -1]],
            2 => [[0, 0], [0, -2]],
            3 => [[0, 0], [-1, -1]],
            4 => [[0, 0], [-1, 1]],
            5 => [[0, 0], [0, 2]],
            default => throw new Exception("impossible case"),
        };
        return $this->getAllTiles($coords, $game, $coordX, $coordY, $availablePositions);
    }

    /**
     * getAllCoordinatesOfPheromoneTypeOne : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param array<array<Int, Int>>      $availablePositions (optional)
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeOne(PlayerMYR $player,
        TileMYR $tile, TileTypeMYR $tileType, array $availablePositions = []) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();

        $coords = match ($tileType->getOrientation()) {
            0 => [[0, 0], [-1, -1], [1, 1]],
            1 => [[0, 0], [0, -2], [0, 2]],
            2 => [[0, 0], [-1, 1], [1, -1]],
            default => throw new Exception(MyrmesTranslation::ERROR_CANNOT_PLACE_TILE),
        };
        return $this->getAllTiles($coords, $game, $coordX, $coordY, $availablePositions);
    }

    /**
     * getAllCoordinatesOfPheromoneTypeTwo : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param array<array<Int, Int>>       $availablePositions
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeTwo(PlayerMYR $player,
        TileMYR $tile, TileTypeMYR $tileType, array $availablePositions = []) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();

        $coords = match ($tileType->getOrientation()) {
            0 => [[0, 0], [1, -1], [1, 1]],
            1 => [[0, 0], [1, -1], [0, -2]],
            2 => [[0, 0], [0, -2], [-1, -1]],
            3 => [[0, 0], [-1, -1], [-1, 1]],
            4 => [[0, 0], [-1, 1], [0, 2]],
            5 => [[0, 0], [0, 2], [1, 1]],
            default => throw new Exception(MyrmesTranslation::ERROR_CANNOT_PLACE_TILE),
        };
        return $this->getAllTiles($coords, $game, $coordX, $coordY, $availablePositions);
    }

    /**
     * getAllCoordinatesOfPheromoneTypeThree : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param array<array<Int, Int>>       $availablePositions (optional)
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeThree(PlayerMYR $player,
        TileMYR $tile, TileTypeMYR $tileType, array $availablePositions = []) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();

        $coords = match ($tileType->getOrientation()) {
            0 => [[0, 0], [1, 1], [1, -1], [2, 0]],
            1 => [[0, 0], [0, -2], [1, -1], [1, -3]],
            2 => [[0, 0], [-1, -1], [-1, -3], [0, -2]],
            3 => [[0, 0], [-2, 0], [-1, -1], [-1, 1]],
            4 => [[0, 0], [0, 2], [-1, 3], [-1, 1]],
            5 => [[0, 0], [1, 1], [1, 3], [0, 2]],
            default => throw new Exception(MyrmesTranslation::ERROR_CANNOT_PLACE_TILE),
        };
        return $this->getAllTiles($coords, $game, $coordX, $coordY, $availablePositions);
    }


    /**
     * getAllCoordinatesOfPheromoneTypeFour : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param array<array<Int, Int>>       $availablePositions (optional)
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeFour(PlayerMYR $player,
        TileMYR $tile, TileTypeMYR $tileType, array $availablePositions = []) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();

        $coords = match ($tileType->getOrientation()) {
            0 => [[0, 0], [0, 2], [1, 1], [2, 2]],
            1 => [[0, 0], [1, -1], [1, 1], [2, -2]],
            2 => [[0, 0], [0, -2], [0, -4], [1, -1]],
            3 => [[0, 0], [0, -2], [-1, -1], [-2, -2]],
            4 => [[0, 0], [-1, -1], [-1, 1], [-2, 2]],
            5 => [[0, 0], [0, 2], [0, 4], [-1, 1]],
            6 => [[0, 0], [0, -2], [1, -1], [2, -2]],
            7 => [[0, 0], [0, -2], [0, -4], [-1, -1]],
            8 => [[0, 0], [-1, -1], [-1, 1], [-2, -2]],
            9 => [[0, 0], [0, 2], [-1, 1], [-2, 2]],
            10 => [[0, 0], [0, 2], [0, 4], [1, 1]],
            11 => [[0, 0], [1, -1], [1, 1], [2, 2]],
            default => throw new Exception(MyrmesTranslation::ERROR_CANNOT_PLACE_TILE),
        };
        return $this->getAllTiles($coords, $game, $coordX, $coordY, $availablePositions);
    }

    /**
     * getAllCoordinatesOfPheromoneTypeFive : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param array<array<Int, Int>>       $availablePositions
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeFive(PlayerMYR $player,
        TileMYR $tile, TileTypeMYR $tileType, array $availablePositions = []) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();

        $coords = match ($tileType->getOrientation()) {
            0 => [[0, 0], [0, 2], [1, 3], [1, 1], [1, -1]],
            1 => [[0, 0], [1, 1], [2, 0], [1, -1], [0, -2]],
            2 => [[0, 0], [1, -1], [1, -3], [0, -2], [-1, -1]],
            3 => [[0, 0], [0, -2], [-1, -3], [-1, -1], [-1, 1]],
            4 => [[0, 0], [-1, -1], [0, 2], [-1, 1], [-2, 0]],
            5 => [[0, 0], [-1, 1], [-1, 3], [0, 2], [1, 1]],
            default => throw new Exception(MyrmesTranslation::ERROR_CANNOT_PLACE_TILE),
        };
        return $this->getAllTiles($coords, $game, $coordX, $coordY, $availablePositions);
    }

    /**
     * getAllCoordinatesOfPheromoneTypeSix : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @param array<array<Int, Int>>      $availablePositions (optional)
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeSix(PlayerMYR $player,
        TileMYR $tile, TileTypeMYR $tileType, array $availablePositions = []) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();

        $coords = match ($tileType->getOrientation()) {
            0 => [[0, 0], [1, 1], [2, 2], [2, 0], [2, -2], [1, -1]],
            1 => [[0, 0], [1, -1], [2, -2], [1, -3], [0, -4], [0, -2]],
            2 => [[0, 0], [0, -2], [0, -4], [-1, -3], [-2, -2], [-1, -1]],
            3 => [[0, 0], [-1, -1], [-2, -2], [-2, 0], [-2, 2], [-1, 1]],
            4 => [[0, 0], [-1, 1], [-2, 2], [0, 2], [0, 4], [-1, 3]],
            5 => [[0, 0], [0, 2], [0, 4], [1, 3], [2, 2], [1, 1]],
            default => throw new Exception(MyrmesTranslation::ERROR_CANNOT_PLACE_TILE),
        };
        return $this->getAllTiles($coords, $game, $coordX, $coordY, $availablePositions);
    }

    /**
     * containsPrey : checks if the tile contains a prey
     * @param GameMYR $game
     * @param TileMYR      $tile
     * @return bool
     */
    private function containsPrey(GameMYR $game, TileMYR $tile) : bool
    {
        return $this->getPrey($game, $tile) != null;
    }

    /**
     * getPrey : return the prey on the given tile
     * @param GameMYR $game
     * @param TileMYR $tile
     * @return PreyMYR|null
     */
    private function getPrey(GameMYR $game, TileMYR $tile) : ?PreyMYR
    {
        return $this->preyMYRRepository->findOneBy(
                ["mainBoardMYR" => $game->getMainBoardMYR(), "tile" => $tile]
            );
    }

    /**
     * getAllCoordinatesOfPheromoneFarm : checks if the player can place a farm
     * @param PlayerMYR   $player
     * @return bool
     */
    private function getAllCoordinatesOfPheromoneFarm(PlayerMYR $player) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();
        $playerResource = null;
        foreach ($personalBoard->getPlayerResourceMYRs() as $playerResourceMYR){
            if($playerResourceMYR->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_STONE) {
                $playerResource = $playerResourceMYR;
            }
        }
       return $playerResource != null && $playerResource->getQuantity() > 0;
    }

    /**
     * getAllCoordinatesOfPheromoneQuarry : checks if the player can place a quarry
     * @param PlayerMYR   $player
     * @return bool
     */
    private function getAllCoordinatesOfPheromoneQuarry(PlayerMYR $player) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();
        $playerResource = null;
        foreach ($personalBoard->getPlayerResourceMYRs() as $playerResourceMYR){
            if($playerResourceMYR->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS) {
                $playerResource = $playerResourceMYR;
            }
        }
        return $playerResource != null && $playerResource->getQuantity() > 0;
    }

    /**
     * getAllCoordinatesOfPheromoneSubanthill : checks if the player can place a subanthill
     * @param PlayerMYR   $player
     * @return bool
     */
    private function getAllCoordinatesOfPheromoneSubanthill(PlayerMYR $player) : bool
    {
        $playerDirt = null;
        $playerStone = null;
        $playerGrass = null;
        $personalBoard = $player->getPersonalBoardMYR();
        foreach ($personalBoard->getPlayerResourceMYRs() as $playerResourceMYR){
            if($playerResourceMYR->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_DIRT) {
                $playerDirt = $playerResourceMYR;
            }
        }
        foreach ($personalBoard->getPlayerResourceMYRs() as $playerResourceMYR){
            if($playerResourceMYR->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_STONE) {
                $playerStone = $playerResourceMYR;
            }
        }
        foreach ($personalBoard->getPlayerResourceMYRs() as $playerResourceMYR){
            if($playerResourceMYR->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS) {
                $playerGrass = $playerResourceMYR;
            }
        }
        return $playerDirt != null && $playerStone != null && $playerGrass != null
            && $playerDirt->getQuantity() > 0 && $playerStone->getQuantity() > 0 && $playerGrass->getQuantity() > 0;
    }

    /**
     * getPheromoneCountOfType : returns the amount of pheromones from a type a player already has placed
     *
     * @param PlayerMYR   $player
     * @param TileTypeMYR $type
     * @return int
     */
    private function getPheromoneCountOfType(PlayerMYR $player, TileTypeMYR $type) : int
    {
        $pheromones = $player->getPheromonMYRs();
        $result = 0;
        foreach ($pheromones as $pheromone) {
            if ($pheromone->getType()->getType() == $type->getType()) {
                ++$result;
            }
        }
        return $result;
    }

    /**
     * canChoosePheromone : checks if player can choose a pheromone of this type
     * @param PlayerMYR $player
     * @param TileTypeMYR $tileType
     * @param int $pheromoneCount
     * @return bool
     * @throws Exception
     */
    private function canChoosePheromone(PlayerMYR $player, TileTypeMYR $tileType, int $pheromoneCount) : bool
    {
        $anthillLevel = $player->getPersonalBoardMYR()->getAnthillLevel();
        $pheromoneSize = match ($tileType->getType()) {
            MyrmesParameters::PHEROMONE_TYPE_ZERO => 2,
            MyrmesParameters::PHEROMONE_TYPE_ONE, MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY,
            MyrmesParameters::SPECIAL_TILE_TYPE_FARM, MyrmesParameters::PHEROMONE_TYPE_TWO => 3,
            MyrmesParameters::PHEROMONE_TYPE_THREE, MyrmesParameters::PHEROMONE_TYPE_FOUR,
                MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL => 4,
            MyrmesParameters::PHEROMONE_TYPE_FIVE => 5,
            MyrmesParameters::PHEROMONE_TYPE_SIX => 6,
            default => throw new Exception("pheromone type unknown"),
        };
        $allowedSize = $anthillLevel + 2;
        if ($player->getPersonalBoardMYR()->getBonus() === MyrmesParameters::BONUS_LEVEL ||
            ($player->getPersonalBoardMYR()->getBonus() === MyrmesParameters::BONUS_PHEROMONE
                && $tileType->getType() >= MyrmesParameters::PHEROMONE_TYPE_ZERO
                && $tileType->getType() <= MyrmesParameters::PHEROMONE_TYPE_SIX)) {
            ++$allowedSize;
        }
        if ($pheromoneSize > $allowedSize) {
            return false;
        }
        if ($tileType->getType() === MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL) {
            return $anthillLevel >= 3;
        }
        if($tileType->getType() < MyrmesParameters::SPECIAL_TILE_TYPE_FARM) {
            return MyrmesParameters::PHEROMONE_TYPE_AMOUNT[$tileType->getType()] >= $pheromoneCount;
        }
        return MyrmesParameters::SPECIAL_TILE_TYPE_AMOUNT[$tileType->getType()] >= $pheromoneCount;
    }

    /**
     * canPlaceSpecialTile : Indicates if the player can choose the special tile
     * @param PlayerMYR $player
     * @param TileTypeMYR $tileType
     * @return bool
     */
    private function canPlaceSpecialTile(PlayerMYR $player, TileTypeMYR $tileType) : bool
    {
        $playerResources = $player->getPersonalBoardMYR()->getPlayerResourceMYRs();
        switch ($tileType->getType()) {
            case MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL:
                foreach ($playerResources as $playerResource) {
                    if(($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS ||
                        $playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_STONE ||
                        $playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_DIRT)
                        && $playerResource->getQuantity() < 1 ){
                        return false;
                    }
                }
                break;
            case MyrmesParameters::SPECIAL_TILE_TYPE_FARM:
                foreach ($playerResources as $playerResource) {
                    if($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_STONE
                        && $playerResource->getQuantity() < 1
                    ){
                        return false;
                    }
                }
                break;
            case MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY:
                foreach ($playerResources as $playerResource) {
                    if($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS
                        && $playerResource->getQuantity() < 1
                    ){
                        return false;
                    }
                }
                break;
            default:
                return true;
        }
        return true;
    }

    /**
     * placeResourceOnTile : places the correct resource on the tile
     * @param PheromonTileMYR $tile
     * @return void
     */
    private function placeResourceOnTile(PheromonTileMYR $tile) : void
    {
        if ($tile->getPheromonMYR()->getType()->getType() >= MyrmesParameters::SPECIAL_TILE_TYPE_FARM) {
            return;
        }
        $grass = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $stone = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_STONE]);
        $dirt = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_DIRT]);
        switch ($tile->getTile()->getType()) {
            case MyrmesParameters::DIRT_TILE_TYPE:
                $tile->setResource($dirt);
                break;
            case MyrmesParameters::GRASS_TILE_TYPE:
                $tile->setResource($grass);
                break;
            case MyrmesParameters::STONE_TILE_TYPE:
                $tile->setResource($stone);
                break;
            default:
        }
    }

    /**
     * createAndPlacePheromone : creates and places a pheromone on selected tile, owned by the player
     *
     * @param ArrayCollection<Int, TileMYR> $tiles
     * @param PlayerMYR                     $playerMYR
     * @param TileTypeMYR                   $tileTypeMYR
     * @return void
     * @throws Exception
     */
    private function createAndPlacePheromone(ArrayCollection $tiles,
            PlayerMYR $playerMYR,
            TileTypeMYR $tileTypeMYR) : void
    {
        if ($tiles->isEmpty()) {
            throw new Exception("invalid placement");
        }
        $playerResources = $playerMYR->getPersonalBoardMYR()->getPlayerResourceMYRs();
        switch ($tileTypeMYR->getType()) {
            case MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL:
                foreach ($playerResources as $playerResource) {
                    if($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS ||
                        $playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_STONE ||
                        $playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_DIRT
                    ) {
                        $playerResource->setQuantity($playerResource->getQuantity() - 1);
                        $this->entityManager->persist($playerResource);
                    }
                }
                break;
            case MyrmesParameters::SPECIAL_TILE_TYPE_FARM:
                foreach ($playerResources as $playerResource) {
                    if($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_STONE){
                        $playerResource->setQuantity($playerResource->getQuantity() - 1);
                        $this->entityManager->persist($playerResource);
                    }
                }
                break;
            case MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY:
                foreach ($playerResources as $playerResource) {
                    if($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS){
                        $playerResource->setQuantity($playerResource->getQuantity() - 1);
                        $this->entityManager->persist($playerResource);
                    }
                }
                break;
            default:
                break;
        }
        $pheromone = new PheromonMYR();
        $pheromone->setPlayer($playerMYR);
        $pheromone->setType($tileTypeMYR);
        if ($tileTypeMYR->getType() === MyrmesParameters::SPECIAL_TILE_TYPE_FARM ||
            $tileTypeMYR->getType() === MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL) {
            $pheromone->setHarvested(true);
        } else {
            $pheromone->setHarvested(false);
        }
        foreach ($tiles as $tile) {
            $coordX = $tile[0];
            $coordY = $tile[1];
            $tileToPlace = $this->getTileAtCoordinate($coordX, $coordY);
            $pheromoneTile = new PheromonTileMYR();
            $pheromoneTile->setTile($tileToPlace);
            $pheromoneTile->setPheromonMYR($pheromone);
            $this->placeResourceOnTile($pheromoneTile);
            $pheromoneTile->setMainBoard($playerMYR->getGameMyr()->getMainBoardMYR());
            $this->entityManager->persist($pheromoneTile);
            $pheromone->addPheromonTile($pheromoneTile);
        }
        $this->entityManager->persist($pheromone);
        $playerMYR->addPheromonMYR($pheromone);
        $type = $tileTypeMYR->getType();
        if ($type <= MyrmesParameters::PHEROMONE_TYPE_SIX) {
            $points = MyrmesParameters::PHEROMONE_TYPE_LEVEL[$tileTypeMYR->getType()];
        } else {
            $points = MyrmesParameters::SPECIAL_TILES_TYPE_LEVEL[$tileTypeMYR->getType()];
        }
        if ($playerMYR->getPersonalBoardMYR()->getBonus() == MyrmesParameters::BONUS_POINT && $points > 0) {
            ++$points;
        }
        $playerMYR->setScore($playerMYR->getScore() + $points);
        $this->entityManager->persist($playerMYR);
        $gardenWorker = $this->gardenWorkerMYRRepository->findOneBy(["player" => $playerMYR->getId()]);
        $this->entityManager->remove($gardenWorker);
        $this->entityManager->flush();
    }

    /**
     * getAvailablePheromonesFromAPlayer : add every (type, amount, orientations) to the result list
     *  if this type is available
     * @param PlayerMYR       $player
     * @param array<Int, Int>           $pheromoneAmount
     * @param int             $start
     * @param int             $end
     * @param array<Int, Int>           $nbOrientations
     * @param ArrayCollection<Int, Array<Int, Int, Int>> $result
     * @return void
     * @throws Exception
     */
    private function getAvailablePheromonesFromAPlayer(PlayerMYR $player, array $pheromoneAmount,
        int $start, int $end, array $nbOrientations, ArrayCollection &$result) : void
    {
        for ($i = $start; $i <= $end; ++$i) {
            $tileType = $this->tileTypeMYRRepository->findOneBy(["type" => $i]);
            $remaining = $pheromoneAmount[$i] - $this->getPheromoneCountOfType($player, $tileType);
            $pheromoneCount = $this->getPheromoneCountOfType($player, $tileType);
            if ($remaining > 0 && $this->canChoosePheromone($player, $tileType, $pheromoneCount)) {
                $result->add([$i, $remaining, $nbOrientations[$i]]);
            }
        }
    }

    /**
     * getAllTiles : get all tiles covered by a pheromone
     *
     * @param array       $coords all the coords of the neighbors tiles of the asked tile
     * @param GameMYR     $game
     * @param int         $x      the abscissa of asked tile
     * @param int         $y      the ordinate of asked tile
     * @param array<array<Int, Int>>       $availablePositions (optional))
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllTiles(
        array $coords, GameMYR $game, int $x, int $y, array $availablePositions = []
    ) : ArrayCollection
    {
        $tiles = new ArrayCollection();
        foreach ($coords as $coord) {
            $tmp = array();
            $coordX = $coord[0];
            $coordY = $coord[1];
            /** @var TileMYR $newTile */
            $newTile = $this->tileMYRRepository->findOneBy(
                ["coordX" => $coordX + $x, "coordY" => $coordY + $y]
            );
            $tmp[0] = $coordX + $x;
            $tmp[1] = $coordY + $y;
            $tiles->add($tmp);
        }
        return $tiles;
    }


    /**
     * getPheromonesFromListOfIds: return a Collection of Entity PheromonMYR retrieved from an array of ids
     *
     * @param string[] $pheromoneIds
     * @return ArrayCollection
     */
    public function getPheromonesFromListOfIds(array $pheromoneIds): ArrayCollection
    {
        return new ArrayCollection($this->pheromonMYRRepository->findBy(['id' => $pheromoneIds]));
    }

    public function getPlayerPheromones(PlayerMYR $playerMYR): ArrayCollection
    {
        $pheromoneTypes = $this->tileTypeMYRRepository->findBy(['type' => MyrmesParameters::PHEROMONE_TYPES]);
        return new ArrayCollection(
            $this->pheromonMYRRepository->findBy(['player' => $playerMYR, 'type' => $pheromoneTypes]));
    }

    /**
     * giveColonyLevelBonus: gives bonus to the player whenever he places a worker into anthill
     * @param PersonalBoardMYR $personalBoard
     * @param int $anthillFloor
     * @param String $lvlTwoResource
     * @return void
     * @throws Exception
     */
    private function giveColonyLevelBonus(PersonalBoardMYR $personalBoard, int $anthillFloor, ?String $lvlTwoResource) : void
    {
        $food = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $playerFood = $this->playerResourceMYRRepository->findOneBy(
            ["personalBoard" => $personalBoard, "resource" => $food]
        );

        $stone = $this->resourceMYRRepository->findOneBy(["description" => MYRmesParameters::RESOURCE_TYPE_STONE]);
        $playerStone = $this->playerResourceMYRRepository->findOneBy(
            ["personalBoard" => $personalBoard, "resource" => $stone]
        );

        $dirt = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_DIRT]);
        $playerDirt = $this->playerResourceMYRRepository->findOneBy(
            ["personalBoard" => $personalBoard, "resource" => $dirt]
        );
        switch ($anthillFloor) {
            case 0 :
                $personalBoard->setLarvaCount($personalBoard->getLarvaCount() + 1);
                $this->entityManager->persist($personalBoard);
                return;
            case 1:
                $playerFood->setQuantity($playerFood->getQuantity() + 1);
                $this->entityManager->persist($playerFood);
                return;
            case 2:
                if ($lvlTwoResource == MyrmesParameters::RESOURCE_TYPE_DIRT) {
                    $playerDirt->setQuantity($playerDirt->getQuantity() + 1);
                    $this->entityManager->persist($playerDirt);
                } elseif ($lvlTwoResource == MyrmesParameters::RESOURCE_TYPE_STONE ) {
                    $playerStone->setQuantity($playerStone->getQuantity() + 1);
                    $this->entityManager->persist($playerStone);
                }
                return;
            case 3:
                if ($playerFood->getQuantity() <= 0) {
                    throw new Exception("not enough food");
                }
                $playerFood->setQuantity($playerFood->getQuantity() - 1);
                $this->entityManager->persist($playerFood);
                $player = $personalBoard->getPlayer();
                $score = $player->getScore();
                $points = 2;
                if ($personalBoard->getBonus() === MyrmesParameters::BONUS_POINT) {
                    ++$points;
                }
                $player->setScore($score + $points);
                $this->entityManager->persist($player);
                return;
        }
    }

}
