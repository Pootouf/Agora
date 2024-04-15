<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\Myrmes\BoardTileMYR;
use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
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

class WorkerMYRService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly MYRService $MYRService,
                                private readonly AnthillWorkerMYRRepository $anthillWorkerMYRRepository,
                                private readonly AnthillHoleMYRRepository $anthillHoleMYRRepository,
                                private readonly PheromonMYRRepository $pheromonMYRRepository,
                                private readonly PreyMYRRepository $preyMYRRepository,
                                private readonly TileMYRRepository $tileMYRRepository,
                                private readonly PlayerResourceMYRRepository $playerResourceMYRRepository,
                                private readonly ResourceMYRRepository $resourceMYRRepository,
                                private readonly TileTypeMYRRepository $tileTypeMYRRepository,
                                private readonly GardenWorkerMYRRepository $gardenWorkerMYRRepository,
                                private readonly PheromonTileMYRRepository $pheromonTileMYRRepository
    )
    {}

    /**
     * getAvailablePheromones : returns a collection of couples (type, amount) of all tile types for a player
     * @param PlayerMYR $playerMYR
     * @return ArrayCollection
     */
    public function getAvailablePheromones(PlayerMYR $playerMYR) : ArrayCollection
    {
        $result = new ArrayCollection();
        for ($i = MyrmesParameters::PHEROMONE_TYPE_ZERO; $i <= MyrmesParameters::PHEROMONE_TYPE_SIX; ++$i) {
            $tileType = $this->tileTypeMYRRepository->findOneBy(["type" => $i]);
            $remaining = MyrmesParameters::PHEROMONE_TYPE_AMOUNT[$i] - $this->getPheromoneCountOfType($playerMYR, $tileType);
            if ($remaining > 0) {
                $result->add([$i, $remaining]);
            }
        }
        for ($i = MyrmesParameters::SPECIAL_TILE_TYPE_FARM; $i <= MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL; ++$i) {
            $tileType = $this->tileTypeMYRRepository->findOneBy(["type" => $i]);
            $remaining = MyrmesParameters::SPECIAL_TILE_TYPE_AMOUNT[$i] - $this->getPheromoneCountOfType($playerMYR, $tileType);
            if ($remaining > 0) {
                $result->add([$i, $remaining]);
            }
        }
        return $result;
    }

    /**
     * getAllAvailablePositions : returns every available positions from a tile of a certain type and orientation
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    public function getAllAvailablePositions(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $type = $tileType->getType();
        switch ($type) {
            case MyrmesParameters::PHEROMONE_TYPE_ZERO:
                return $this->getAllAvailablePositionsFromTypeZero($player, $tile, $tileType);
            case MyrmesParameters::PHEROMONE_TYPE_ONE:
                return $this->getAllAvailablePositionsFromTypeOne($player, $tile, $tileType);
            case MyrmesParameters::SPECIAL_TILE_TYPE_FARM:
            case MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY:
            case MyrmesParameters::PHEROMONE_TYPE_TWO:
                return $this->getAllAvailablePositionsFromTypeTwo($player, $tile, $tileType);
            case MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL:
            case MyrmesParameters::PHEROMONE_TYPE_THREE:
                return $this->getAllAvailablePositionsFromTypeThree($player, $tile, $tileType);
            case MyrmesParameters::PHEROMONE_TYPE_FOUR:
                return $this->getAllAvailablePositionsFromTypeFour($player, $tile, $tileType);
            case MyrmesParameters::PHEROMONE_TYPE_FIVE:
                return $this->getAllAvailablePositionsFromTypeFive($player, $tile, $tileType);
            case MyrmesParameters::PHEROMONE_TYPE_SIX:
                return $this->getAllAvailablePositionsFromTypeSix($player, $tile, $tileType);
            default:
                return new ArrayCollection();
        }
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

        return ($prey == null && $originPheromoneTile == null && $destinationPheromoneTile == null && $hasEnoughShiftsCount)
            || ($prey != null && $this->canWorkerAttackPrey($player, $prey) && $hasEnoughShiftsCount)
            || (($originPheromoneTile != null || $destinationPheromoneTile != null)
                && $this->canWorkerWalkAroundPheromone($player, $originPheromoneTile, $destinationPheromoneTile, $gardenWorker));
    }

    /**
     * @param int $coordX
     * @param int $coordY
     * @param GameMYR $gameMYR
     * @return int
     * @throws Exception
     */
    public function getNeededSoldiers(int $coordX, int $coordY, GameMYR $gameMYR, PlayerMYR $player): int
    {
        $tile = $this->tileMYRRepository->findOneBy([
            "coord_Y" => $coordY,
            "coord_X" => $coordX
        ]);
        if($tile == null) {
            throw new Exception("Not a valid tile, can't identify if there is a prey on it");
        }
        $prey = $this->getPrey($gameMYR, $tile);
        $pheromoneTile = $this->pheromonTileMYRRepository->findOneBy([
            "tile" => $tile,
            "mainBoard" => $gameMYR->getMainBoardMYR()
        ]);
        return ($prey == null ? 0 : MyrmesParameters::NUMBER_SOLDIERS_FOR_ATTACK_PREY[$prey->getType()])
            + ($pheromoneTile == null ? 0 : ($pheromoneTile->getPlayer() == $player ? 0 : 1));
    }

    /**
     * getNeededMovementPoints : return the needed movement points for the player to move from the tile on
     *                                  coordX1, coordY1 position to coordX2, coordY2 position
     * @param int $coordX1
     * @param int $coordY1
     * @param int $coordX2
     * @param int $coordY2
     * @param GameMYR $gameMYR
     * @param PlayerMYR $player
     * @return int
     * @throws Exception
     */
    public function getNeededMovementPoints(int $coordX1, int $coordY1, int $coordX2, int $coordY2,
                                            GameMYR $gameMYR, PlayerMYR $player) : int
    {
        $tile1 = $this->tileMYRRepository->findOneBy([
            "coord_Y" => $coordY1,
            "coord_X" => $coordX1
        ]);
        $tile2 = $this->tileMYRRepository->findOneBy([
            "coord_Y" => $coordY2,
            "coord_X" => $coordX2
        ]);
        if ($tile1 == null || $tile2 == null) {
            throw new Exception("Not a valid tile, can't identify if there is a prey on it");
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
            && $pheromoneTile1->getPheromonMyr()->getPlayer() == $player
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
        if (in_array($type, MyrmesParameters::SPECIAL_TILE_TYPES)) {
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
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    public function getAllCoordinatesFromTileType(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        switch ($tileType->getType()) {
            case MyrmesParameters::PHEROMONE_TYPE_ZERO:
                return $this->getAllCoordinatesOfPheromoneTypeZero($player, $tile, $tileType);
            case MyrmesParameters::PHEROMONE_TYPE_ONE:
                return $this->getAllCoordinatesOfPheromoneTypeOne($player, $tile, $tileType);
            case MyrmesParameters::PHEROMONE_TYPE_TWO:
                return $this->getAllCoordinatesOfPheromoneTypeTwo($player, $tile, $tileType);
            case MyrmesParameters::PHEROMONE_TYPE_THREE:
                return $this->getAllCoordinatesOfPheromoneTypeThree($player, $tile, $tileType);
            case MyrmesParameters::PHEROMONE_TYPE_FOUR:
                return $this->getAllCoordinatesOfPheromoneTypeFour($player, $tile, $tileType);
            case MyrmesParameters::PHEROMONE_TYPE_FIVE:
                return $this->getAllCoordinatesOfPheromoneTypeFive($player, $tile, $tileType);
            case MyrmesParameters::PHEROMONE_TYPE_SIX:
                return $this->getAllCoordinatesOfPheromoneTypeSix($player, $tile, $tileType);
            case MyrmesParameters::SPECIAL_TILE_TYPE_FARM:
                if ($this->getAllCoordinatesOfPheromoneFarm($player)) {
                    return $this->getAllCoordinatesOfPheromoneTypeTwo($player, $tile, $tileType);
                } else {
                    return new ArrayCollection();
                }
            case MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY:
                if ($this->getAllCoordinatesOfPheromoneQuarry($player)) {
                    return $this->getAllCoordinatesOfPheromoneTypeTwo($player, $tile, $tileType);
                } else {
                    return new ArrayCollection();
                }
            case MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL:
                if ($this->getAllCoordinatesOfPheromoneSubanthill($player)) {
                    return $this->getAllCoordinatesOfPheromoneTypeThree($player, $tile, $tileType);
                } else {
                    return new ArrayCollection();
                }
            default:
                return new ArrayCollection();
        }
    }

    /**
     * cleanPheromone : retrieve the pheromone from the main board; the action cost a dirt resource
     * @param PheromonMYR $pheromone
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
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
            throw new Exception("Can't clean the pheromone");
        }

        $dirtResource->setQuantity($dirtResource->getQuantity() - 1);
        $this->entityManager->persist($dirtResource);

        if ($pheromone->getPlayer() !== $player) {
            $player->setScore($player->getScore()
                + MyrmesParameters::PHEROMONE_TYPE_LEVEL[$pheromone->getType()]);
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
     * @return void
     * @throws Exception if invalid floor or no more free ants
     */
    public function placeAntInAnthill(PersonalBoardMYR $personalBoard, int $anthillFloor) : void
    {
        $maxFloor = $personalBoard->getAnthillLevel();
        $isAnthillLevelIncreased = $personalBoard->getBonus() == MyrmesParameters::BONUS_LEVEL;
        if ($maxFloor + ($isAnthillLevelIncreased ? 1 : 0) < $anthillFloor) {
            throw new Exception('Invalid floor level');
        }
        $ant = $this->anthillWorkerMYRRepository->findOneBy([
            'player' => $personalBoard->getPlayer(),
            'workFloor' => MyrmesParameters::NO_WORKFLOOR
        ]);
        if(!$ant) {
            throw new Exception('No more free ants');
        }
        $ant->setWorkFloor($anthillFloor);
        $this->entityManager->persist($ant);
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
            'player' => $personalBoard->getPlayer(),
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
        $gardenWorker->setShiftsCount(MyrmesParameters::DEFAULT_MOVEMENT_NUMBER
            + ($personalBoard->getBonus() == MyrmesParameters::BONUS_MOVEMENT ? 3 : 0));
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
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return bool
     */
    public function canPlacePheromone(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : bool
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
            $tiles = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
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
     * @return void
     * @throws Exception
     */
    public function placePheromone(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : void
    {
        if (!$this->canPlacePheromone($player, $tile, $tileType)) {
            throw new Exception("this pheromone can't be placed there");
        }
        $tiles = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
        $this->createAndPlacePheromone($tiles, $player, $tileType);
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
        } else if ($destinationPheromone != null
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
            "coord_X" => $coordX,
            "coord_Y" => $coordY
        ]);
    }

    /**
     * getAllAvailablePositionsFromTypeZero : returns all available positions for a pheromone of type Zero
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeZero(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        switch ($orientation) {
            case 0:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 1:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, + 1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 2:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, + 2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 3:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, 1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 4:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 5:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, - 2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            default:
                return new ArrayCollection();
        }
    }

    /**
     * getAllAvailablePositionsFromTypeOne : returns all available positions for a pheromone of type One
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeOne(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        switch ($orientation) {
            case 0:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, -1], [1, 1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 1:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, +1], [1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 2:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, +2], [0, -2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            default:
                return new ArrayCollection();
        }
    }

    /**
     * getAllAvailablePositionsFromTypeTwo : returns all available positions for a pheromone of type Two
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeTwo(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        switch ($orientation) {
            case 0:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, 1], [-1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 1:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, +1], [0, 2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 2:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, +2], [1, 1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 3:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, 1], [1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 4:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, 1], [0, -2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 5:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, -2], [-1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            default:
                return new ArrayCollection();
        }
    }

    /**
     * getAllAvailablePositionsFromTypeThree : returns all available positions for a pheromone of type Three
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeThree(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        switch ($orientation) {
            case 0:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, -2], [-1, -1], [-1, -3]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 1:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, -1], [-1, 1], [-2, 0]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 2:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, 1], [1, 3], [0, 2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 3:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, 2], [1, 3], [1, 1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 4:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [2, 0], [1, 1], [1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 5:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, -2], [1, -3], [1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            default:
                return new ArrayCollection();
        }
    }

    /**
     * getAllAvailablePositionsFromTypeFour : returns all available positions for a pheromone of type Four
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeFour(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        switch ($orientation) {
            case 0:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, -1], [-1, 1], [-2, -2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 1:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, 2], [-1, 1], [-2, 2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 2:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, 2], [0, 4], [1, 1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 3:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, -1], [1, 1], [2, 2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 4:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, -2], [1, -1], [2, -2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 5:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, -2], [0, -4], [-1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 6:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, -2], [-1, -1], [-2, -2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 7:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, -1], [-1, 1], [-2, 2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 8:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, 1], [0, 2], [0, 4]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 9:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, 2], [1, 1], [2, 2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 10:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, 1], [1, -1], [2, -2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 11:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, -1], [0, -2], [0, -4]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            default:
                return new ArrayCollection();
        }
    }

    /**
     * getAllAvailablePositionsFromTypeFive : returns all available positions for a pheromone of type Five
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeFive(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        switch ($orientation) {
            case 0:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, -2], [-1, -3], [-1, -1], [-1, 1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 1:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, -1], [-2, 0], [1, 1], [0, 2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 2:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, 1], [-1, 3], [0, 2], [1, 1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 3:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, 2], [1, 3], [1, 1], [1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 4:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, 1], [0, 2], [1, -1], [0, -2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 5:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, -1], [1, -3], [0, -2], [-1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            default:
                return new ArrayCollection();
        }
    }

    /**
     * getAllAvailablePositionsFromTypeSix : returns all available positions for a pheromone of type Six
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromTypeSix(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $orientation = $tileType->getOrientation();
        switch ($orientation) {
            case 0:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, 1], [-1, -1], [-2, +2], [-2, 0], [-2, -2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 1:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [-1, 1], [-2, 2], [-1, +3], [0, 4], [0, 2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 2:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, 2], [0, 4], [1, +3], [2, 2], [1, 1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 3:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, 1], [2, 2], [2, 0], [2, -2], [1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 4:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [1, -1], [2, -2], [1, -3], [0, -4], [0, -2]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            case 5:
                $coords = $this->getAllCoordinatesFromTileType($player, $tile, $tileType);
                $translations = [[0, 0], [0, -2], [0, -4], [-1, -3], [-2, -2], [-1, -1]];
                return $this->getAllAvailablePositionsFromOrientation($player, $tile, $tileType, $coords, $translations);
            default:
                return new ArrayCollection();
        }
    }

    /**
     * getAllAvailablePositionsFromOrientation : returns a list of lists of tiles where player can place the pheromone
     *
     * @param PlayerMYR                                       $player
     * @param TileMYR                                         $tile
     * @param TileTypeMYR                                     $tileType
     * @param ArrayCollection<Int, ArrayCollection<Int, Int>> $coords
     * @param Array<Int, Array<Int>>                          $translations
     * @return ArrayCollection<Int, ArrayCollection<Int, BoardTileMYR>>
     * @throws Exception
     */
    private function getAllAvailablePositionsFromOrientation(PlayerMYR $player, TileMYR $tile,
        TileTypeMYR $tileType, ArrayCollection $coords, Array $translations) : ArrayCollection
    {
        $game = $player->getGameMyr();
        $result = new ArrayCollection();
        $isPivot = true;
        foreach ($translations as $translation) {
            /** @var ArrayCollection<Int, BoardTileMYR> $tileList */
            $tileList = new ArrayCollection();
            $translationX = $translation[0];
            $translationY = $translation[1];
            $correctPlacement = true;
            foreach ($coords as $coord) {
                $coordX = $coord->getCoordX() + $translationX;
                $coordY = $coord->getCoordY() + $translationY;
                $newTile = $this->getTileAtCoordinate($coordX, $coordY);
                if (!($this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile))) {
                    $correctPlacement = false;
                    break;
                }
                $boardTile = new BoardTileMYR($newTile, $isPivot);
                if ($isPivot) {
                    $isPivot = false;
                }
                $tileList->add($boardTile);
            }
            if ($correctPlacement && $this->containsAnt($player, $tileList)) {
                $result->add($tileList);
            }
        }
        return $result;
    }

    /**
     * containsAnt : checks if any of the tile in the list contains an ant
     * @param PlayerMYR       $player
     * @param ArrayCollection<Int, BoardTileMYR> $tileList
     * @return bool
     */
    private function containsAnt(PlayerMYR $player, ArrayCollection $tileList) : bool
    {
        foreach ($tileList as $tile) {
            if ($this->gardenWorkerMYRRepository->findOneBy(
                ["tile" => $tile->getTile(), "player" => $player]
            ) != null) {
               return true;
            }
        }
        return false;
    }

    /**
     * getPreyOnTile : return prey on the tile or null
     * @param TileMYR $tile
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
        "coord_X" =>  $x,
        "coord_Y" => $y]);
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
        $player->setScore($player->getScore()
            + MyrmesParameters::VICTORY_GAIN_BY_ATTACK_PREY[
            $prey->getType()
            ]);

        // Manage quantity of resource
        $playerResource = $this->MYRService->getPlayerResourceOfType(
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
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeZero(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            return new ArrayCollection();
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[0, 0], [1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 1:
                $coords = [[0, 0], [1, -1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 2:
                $coords = [[0, 0], [0, -2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 3:
                $coords = [[0, 0], [-1, -1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 4:
                $coords = [[0, 0], [-1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 5:
                $coords = [[0, 0], [0, 2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            default:
                throw new Exception("impossible case");
        }
    }

    /**
     * getAllCoordinatesOfPheromoneTypeOne : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeOne(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[0, 0], [-1, -1], [1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 1:
                $coords = [[0, 0], [-1, 1], [1, -1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 2:
                $coords = [[0, 0], [0, -2], [0, 2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * getAllCoordinatesOfPheromoneTypeTwo : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeTwo(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[0, 0], [1, -1], [1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 1:
                $coords = [[0, 0], [-1, -1], [0, -2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 2:
                $coords = [[0, 0], [0, -2], [-1, -1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 3:
                $coords = [[0, 0], [-1, -1], [-1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 4:
                $coords = [[0, 0], [-1, -1], [0, 2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 5:
                $coords = [[0, 0], [0, 2], [1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * getAllCoordinatesOfPheromoneTypeThree : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeThree(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[0, 0], [0, 2], [1, 1], [1, 3]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 1:
                $coords = [[0, 0], [1, 1], [1, -1], [2, 0]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 2:
                $coords = [[0, 0], [1, -1], [-1, -3], [0, -2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 3:
                $coords = [[0, 0], [0, -2], [-1, -3], [-1, -1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 4:
                $coords = [[0, 0], [-2, 0], [-1, -1], [-1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 5:
                $coords = [[0, 0], [0, 2], [-1, 3], [-1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            default:
                throw new Exception("can't place this tile");
        }
    }


    /**
     * getAllCoordinatesOfPheromoneTypeFour : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeFour(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[0, 0], [1, 1], [1, -1], [2, 2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 1:
                $coords = [[0, 0], [0, -2], [1, -1], [2, -2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 2:
                $coords = [[0, 0], [0, -2], [0, -4], [-1, -1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 3:
                $coords = [[0, 0], [-1, 1], [-1, -1], [-2, -2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 4:
                $coords = [[0, 0], [0, 2], [-1, 1], [-2, 2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 5:
                $coords = [[0, 0], [0, 2], [0, 4], [1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 6:
                $coords = [[0, 0], [0, 2], [1, 1], [2, 2]];
                 return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 7:
                $coords = [[0, 0], [1, 1], [1, -1], [2, -2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 8:
                $coords = [[0, 0], [1, -1], [0, -2], [0, -4]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 9:
                $coords = [[0, 0], [0, -2], [-1, -1], [-2, -2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 10:
                $coords = [[0, 0], [-1, -1], [-1, 1], [-2, 2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 11:
                $coords = [[0, 0], [-1, 1], [0, 2], [0, 4]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * getAllCoordinatesOfPheromoneTypeFive : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeFive(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[0, 0], [0, 2], [1, 3], [1, 1], [1, -1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 1:
                $coords = [[0, 0], [1, 1], [2, 0], [-1, -1], [0, -2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 2:
                $coords = [[0, 0], [1, -1], [1, -3], [0, -2], [-1, -1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 3:
                $coords = [[0, 0], [0, -2], [-1, -3], [-1, -1], [-1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 4:
                $coords = [[0, 0], [-1, -1], [0, -2], [-1, 1], [0, 2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 5:
                $coords = [[0, 0], [-1, 1], [-1, 3], [0, 2], [1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * getAllCoordinatesOfPheromoneTypeSix : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllCoordinatesOfPheromoneTypeSix(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[0, 0], [1, 1], [2, 2], [2, 0], [2, -2], [1, -1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 1:
                $coords = [[0, 0], [1, -1], [2, -2], [1, -3], [0, -4], [0, -2]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 2:
                $coords = [[0, 0], [0, -2], [0, -4], [-1, -3], [-2, -2], [-1, -1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 3:
                $coords = [[0, 0], [-1, -1], [-2, -2], [-2, 0], [-2, 2], [-1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 4:
                $coords = [[0, 0], [-1, 1], [-2, 2], [0, 2], [0, 4], [-1, 3]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            case 5:
                $coords = [[0, 0], [0, 2], [0, 4], [1, 3], [2, 2], [1, 1]];
                return $this->getAllTiles($coords, $game, $player, $tileType, $coordX, $coordY);
            default:
                throw new Exception("can't place this tile");
        }
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
        $personalBoard = $player->getPersonalBoardMYR();
        $grass = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $stone = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_STONE]);
        $dirt = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_DIRT]);
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
        if ($player->getPersonalBoardMYR()->getBonus() === MyrmesParameters::BONUS_PHEROMONE) {
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
                    if($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS ||
                        $playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_STONE ||
                        $playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_DIRT){
                        if($playerResource->getQuantity() < 1) {
                            return false;
                        }
                    }
                }
                break;
            case MyrmesParameters::SPECIAL_TILE_TYPE_FARM:
                foreach ($playerResources as $playerResource) {
                    if($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_STONE){
                        if($playerResource->getQuantity() < 1) {
                            return false;
                        }
                    }
                }
                break;
            case MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY:
                foreach ($playerResources as $playerResource) {
                    if($playerResource->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS){
                        if($playerResource->getQuantity() < 1) {
                            return false;
                        }
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
        $pheromone->setHarvested(false);
        foreach ($tiles as $tile) {
            $pheromoneTile = new PheromonTileMYR();
            $pheromoneTile->setTile($tile);
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
        if ($playerMYR->getPersonalBoardMYR()->getBonus() == MyrmesParameters::BONUS_POINT) {
            ++$points;
        }
        $playerMYR->setScore($playerMYR->getScore() + $points);
        $this->entityManager->persist($playerMYR);
        $gardenWorker = $this->gardenWorkerMYRRepository->findOneBy(["player" => $playerMYR->getId()]);
        $this->entityManager->remove($gardenWorker);
        $this->entityManager->flush();
    }

    /**
     * getAllTiles : get all tiles covered by a pheromone
     *
     * @param array       $coords all the coords of the neighbors tiles of the asked tile
     * @param GameMYR     $game
     * @param PlayerMYR   $player
     * @param TileTypeMYR $tileType
     * @param int         $x      the abscissa of asked tile
     * @param int         $y      the ordinate of asked tile
     * @return ArrayCollection<Int, TileMYR>
     * @throws Exception
     */
    private function getAllTiles(Array $coords, GameMYR $game, PlayerMYR $player,
        TileTypeMYR $tileType, int $x, int $y) : ArrayCollection
    {
        $tiles = new ArrayCollection();
        foreach ($coords as $coord) {
            $coordX = $coord[0];
            $coordY = $coord[1];
            $newTile = $this->tileMYRRepository->findOneBy(
                ["coord_X" => $coordX + $x, "coord_Y" => $coordY + $y]
            );
            $result = $this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile);
            if (!$result) {
                throw new Exception("can't place this tile");
            }
            $tiles->add($newTile);
        }
        return $tiles;
    }
}