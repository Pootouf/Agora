<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\PheromonMYRRepository;
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
                                private readonly AnthillHoleMYRRepository $anthillHoleMYRRepository,
                                private readonly PheromonMYRRepository $pheromonMYRRepository,
                                private readonly PreyMYRRepository $preyMYRRepository,
                                private readonly TileMYRRepository $tileMYRRepository,
                                private readonly PlayerResourceMYRRepository $playerResourceMYRRepository,
                                private readonly ResourceMYRRepository $resourceMYRRepository,
                                private readonly TileTypeMYRRepository $tileTypeMYRRepository
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
        if (!$this->isWorkerOnTile($player, $tile)) {
            throw new Exception("no garden worker on this tile");
        }
        $pheromoneCount = $this->getPheromoneCountOfType($player, $tileType);
        if (!$this->canChoosePheromone($player, $tileType, $pheromoneCount)) {
            throw new Exception("player can't place more pheromones of this type");
        }
        switch ($tileType->getType()) {
            case MyrmesParameters::PHEROMONE_TYPE_ZERO:
                $this->placePheromoneTypeZero($player, $tile, $tileType);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_ONE:
                $this->placePheromoneTypeOne($player, $tile, $tileType);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_TWO:
                $this->placePheromoneTypeTwo($player, $tile, $tileType);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_THREE:
                $this->placePheromoneTypeThree($player, $tile, $tileType);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_FOUR:
                $this->placePheromoneTypeFour($player, $tile, $tileType);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_FIVE:
                $this->placePheromoneTypeFive($player, $tile, $tileType);
                break;
            case MyrmesParameters::PHEROMONE_TYPE_SIX:
                $this->placePheromoneTypeSix($player, $tile, $tileType);
                break;
            case MyrmesParameters::SPECIAL_TILE_TYPE_FARM:
                if ($this->placePheromoneFarm($player)) {
                    $this->placePheromoneTypeTwo($player, $tile, $tileType);
                    break;
                } else {
                    throw new Exception("cant place this tile");
                }
            case MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY:
                if ($this->placePheromoneQuarry($player)) {
                    $this->placePheromoneTypeTwo($player, $tile, $tileType);
                    break;
                } else {
                    throw new Exception("cant place this tile");
                }
            case MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL:
                if ($this->placePheromoneSubanthill($player)) {
                    $this->placePheromoneTypeThree($player, $tile, $tileType);
                    break;
                } else {
                    throw new Exception("cant place this tile");
                }
            default:
                throw new Exception("unknown tile type");
        }
    }

    /**
     * workerMove : garden worker move on main board
     * @param PlayerMYR $player
     * @param GardenWorkerMYR $gardenWorker
     * @param int $direction
     * @return void
     */
    public function workerMove(PlayerMYR $player,
       GardenWorkerMYR $gardenWorker, int $direction) : void
    {
        if ($this->canWorkerMove($player, $gardenWorker, $direction))
        {
            $tile =
                $this->getTileAtDirection($gardenWorker->getTile(), $direction);
            $gardenWorker->setTile($tile);

            $prey = $this->getPreyOnTile($tile);
            $pheromone = $this->getPheromoneOnTile($tile);
            if ($prey != null)
            {
                $this->attackPrey($player, $prey);
            } else if ($pheromone != null
                && $pheromone->getPheromonMYR()->getPlayer() !== $player)
            {
                $personalBoard = $player->getPersonalBoardMYR();

                $personalBoard->setWarriorsCount(
                    $personalBoard->getWarriorsCount() - 1
                );
            }

            $this->entityManager->persist($gardenWorker);

            if ($pheromone->getPheromonMYR()->getPlayer() !== $player)
            {
                $gardenWorker->setShiftsCount(
                    $gardenWorker->getShiftsCount() - 1
                );
                $this->entityManager->persist($gardenWorker);
            }
        }
        $this->entityManager->flush();
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

        if ($tile == null
            || $tile->getType() != MyrmesParameters::WATER_TILE_TYPE)
        {
            return false;
        }

        $prey = $this->getPreyOnTile($tile);
        $pheromone = $this->getPheromoneOnTile($tile);

        $canMove = ($prey == null && $pheromone == null)
            || ($prey != null && $this->canWorkerAttackPrey($player, $prey))
            || ($pheromone != null
                && $this->canWorkerWalkAroundPheromone($player, $pheromone))
        ;

        return $gardenWorker->getShiftsCount() > 0
            && $canMove;
    }

    /**
     * getPreyOnTile : return prey on the tile or null
     * @param TileMYR $tile
     * @return PreyMYR|null
     */
    private function getPreyOnTile(TileMYR $tile) : ?PreyMYR
    {
        return $this->preyMYRRepository->findOneBy(["tile" => $tile->getId()]);
    }

    /**
     * getPheromoneOnTile : return pheromone on the tile or null
     * @param TileMYR $tile
     * @return PheromonTileMYR|null
     */
    private function getPheromoneOnTile(TileMYR $tile) : ?PheromonTileMYR
    {
        return $this->pheromonMYRRepository->findOneBy(
            ["tile" => $tile->getId()]
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
     * @param PheromonTileMYR $pheromonTile
     * @return bool
     */
    private function canWorkerWalkAroundPheromone(PlayerMYR $player, PheromonTileMYR $pheromonTile) : bool
    {
        if ($pheromonTile->getPheromonMYR()->getPlayer() === $player)
        {
            return true;
        }

        $personalBoard = $player->getPersonalBoardMYR();

        return $personalBoard->getWarriorsCount() >= 1;
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
     * @param ?TileMYR $tileMYR
     * @return bool
     */
    private function isPositionAvailable(GameMYR $gameMYR, ?TileMYR $tileMYR) : bool
    {
        if ($tileMYR == null) {
            return false;
        }
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
     * placePheromoneTypeZero : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeZero(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : void
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 1:
                $coords = [[1, -1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 2:
                $coords = [[0, -2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 3:
                $coords = [[-1, -1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 4:
                $coords = [[-1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 5:
                $coords = [[0, 2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            default:
                throw new Exception("impossible case");
        }
    }

    /**
     * placePheromoneTypeOne : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeOne(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : void
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[-1, -1], [1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 1:
                $coords = [[-1, 1], [1, -1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 2:
                $coords = [[0, -2], [0, 2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * placePheromoneTypeTwo : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeTwo(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : void
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[1, -1], [1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 1:
                $coords = [[-1, -1], [0, -2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 2:
                $coords = [[0, -2], [-1, -1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 3:
                $coords = [[-1, -1], [-1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 4:
                $coords = [[-1, -1], [0, 2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 5:
                $coords = [[0, 2], [1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * placePheromoneTypeThree : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeThree(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : void
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[0, 2], [1, 1], [1, 3]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 1:
                $coords = [[1, 1], [1, -1], [2, 0]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 2:
                $coords = [[1, -1], [-1, -3], [0, -2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 3:
                $coords = [[0, -2], [-1, -3], [-1, -1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 4:
                $coords = [[-2, 0], [-1, -1], [-1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 5:
                $coords = [[0, 2], [-1, 3], [-1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            default:
                throw new Exception("can't place this tile");
        }
    }


    /**
     * placePheromoneTypeFour : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeFour(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : void
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[1, 1], [1, -1], [2, 2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 1:
                $coords = [[0, -2], [1, -1], [2, -2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 2:
                $coords = [[0, -2], [0, -4], [-1, -1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 3:
                $coords = [[-1, 1], [-1, -1], [-2, -2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 4:
                $coords = [[0, 2], [-1, 1], [-2, 2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 5:
                $coords = [[0, 2], [0, 4], [1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 6:
                $coords = [[0, 2], [1, 1], [2, 2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 7:
                $coords = [[1, 1], [1, -1], [2, -2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 8:
                $coords = [[1, -1], [0, -2], [0, -4]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 9:
                $coords = [[0, -2], [-1, -1], [-2, -2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 10:
                $coords = [[-1, -1], [-1, 1], [-2, 2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 11:
                $coords = [[-1, 1], [0, 2], [0, 4]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * placePheromoneTypeFive : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeFive(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : void
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[0, 2], [1, 3], [1, 1], [1, -1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 1:
                $coords = [[1, 1], [2, 0], [-1, -1], [0, -2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 2:
                $coords = [[1, -1], [1, -3], [0, -2], [-1, -1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 3:
                $coords = [[0, -2], [-1, -3], [-1, -1], [-1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 4:
                $coords = [[-1, -1], [0, -2], [-1, 1], [0, 2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 5:
                $coords = [[-1, 1], [-1, 3], [0, 2], [1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * placePheromoneTypeSix : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param TileTypeMYR $tileType
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeSix(PlayerMYR $player, TileMYR $tile, TileTypeMYR $tileType) : void
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $coords = [[1, 1], [2, 2], [2, 0], [2, -2], [1, -1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 1:
                $coords = [[1, -1], [2, -2], [1, -3], [0, -4], [0, -2]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 2:
                $coords = [[0, -2], [0, -4], [-1, -3], [-2, -2], [-1, -1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 3:
                $coords = [[-1, -1], [-2, -2], [-2, 0], [-2, 2], [-1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 4:
                $coords = [[-1, 1], [-2, 2], [0, 2], [0, 4], [-1, 3]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            case 5:
                $coords = [[0, 2], [0, 4], [1, 3], [2, 2], [1, 1]];
                $this->tryToPlacePheromone($coords, $game, $player, $tileType, $coordX, $coordY);
                break;
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * containsPrey : checks if the tile contains a prey
     * @param GameMYR|null $game
     * @param TileMYR      $tile
     * @return bool
     */
    private function containsPrey(?GameMYR $game, TileMYR $tile) : bool
    {
        $mainBoard = $game->getMainBoardMYR();
        return $this->preyMYRRepository->findOneBy(
            ["mainBoardMYR" => $mainBoard->getId(), "tile" => $tile]
        ) != null;
    }

    /**
     * placePheromoneFarm : checks if the player can place a farm
     * @param PlayerMYR   $player
     * @return bool
     */
    private function placePheromoneFarm(PlayerMYR $player) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();
        $grass = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        return $this->playerResourceMYRRepository->findOneBy(
            ["personalBoard" => $personalBoard, "resource" => $grass]
        ) != null;
    }

    /**
     * placePheromoneQuarry : checks if the player can place a quarry
     * @param PlayerMYR   $player
     * @return bool
     */
    private function placePheromoneQuarry(PlayerMYR $player) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();
        $grass = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $stone = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_STONE]);
        return
            $this->playerResourceMYRRepository->findOneBy(
                ["personalBoard" => $personalBoard, "resource" => $grass]
            ) != null
            &&
            $this->playerResourceMYRRepository->findOneBy(
                ["personalBoard" => $personalBoard, "resource" => $stone]
            )!= null;
    }

    /**
     * placePheromoneSubanthill : checks if the player can place a subanthill
     * @param PlayerMYR   $player
     * @return bool
     */
    private function placePheromoneSubanthill(PlayerMYR $player) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();
        $grass = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        $stone = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_STONE]);
        $dirt = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_DIRT]);
        return
            $this->playerResourceMYRRepository->findOneBy(
                ["personalBoard" => $personalBoard, "resource" => $grass]
            ) != null
            &&
            $this->playerResourceMYRRepository->findOneBy(
                ["personalBoard" => $personalBoard, "resource" => $stone]
            )!= null
            &&
            $this->playerResourceMYRRepository->findOneBy(
                ["personalBoard" => $personalBoard, "resource" => $dirt]
            )!= null;
    }

    /**
     * isWorkerOnTile : checks if the player owns a garden worker on the selected tile
     * @param PlayerMYR $player
     * @param TileMYR   $tile
     * @return bool
     */
    private function isWorkerOnTile(PlayerMYR $player, TileMYR $tile) : bool
    {
        $mainBoard = $player->getGameMyr()->getMainBoardMYR();
        $gardenWorkers = $mainBoard->getGardenWorkers();
        foreach ($gardenWorkers as $gardenWorker) {
            if ($gardenWorker->getTile() === $tile && $gardenWorker->getPlayer() === $player) {
                return true;
            }
        }
        return false;
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
        if($tileType->getType() == MyrmesParameters::PHEROMONE_TYPE_ZERO) {
            $pheromoneSize = 2;
        } else {
            if($tileType->getType() == MyrmesParameters::PHEROMONE_TYPE_ONE ||
                $tileType->getType() == MyrmesParameters::PHEROMONE_TYPE_TWO ||
                $tileType->getType() == MyrmesParameters::SPECIAL_TILE_TYPE_FARM ||
                $tileType->getType() == MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY) {
                $pheromoneSize = 3;
            } else {
                if($tileType->getType() == MyrmesParameters::PHEROMONE_TYPE_THREE ||
                    $tileType->getType() == MyrmesParameters::PHEROMONE_TYPE_FOUR ||
                    $tileType->getType() == MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL) {
                    $pheromoneSize = 4;
                } else {
                    if($tileType->getType() == MyrmesParameters::PHEROMONE_TYPE_FIVE) {
                        $pheromoneSize = 5;
                    } else {
                        if($tileType->getType() == MyrmesParameters::PHEROMONE_TYPE_SIX) {
                            $pheromoneSize = 6;
                        } else {
                            throw new Exception("pheromone type unknown");
                        }
                    }
                }
            }
        }
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
        return MyrmesParameters::PHEROMONE_TYPE_AMOUNT[$tileType->getType()] >= $pheromoneCount;
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
     * @param ArrayCollection<Int, TileMYR>  $tiles
     * @param PlayerMYR                      $playerMYR
     * @param TileTypeMYR                    $tileTypeMYR
     * @return void
     */
    private function createAndPlacePheromone(ArrayCollection $tiles,
            PlayerMYR $playerMYR,
            TileTypeMYR $tileTypeMYR) : void
    {
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
        if ($playerMYR->getPersonalBoardMYR()->getBonus() === MyrmesParameters::BONUS_POINT) {
            ++$points;
        }
        $playerMYR->setScore($playerMYR->getScore() + $points);
        $this->entityManager->persist($playerMYR);
        $this->entityManager->flush();
    }

    /**
     * tryToPlacePheromone : tries to place a pheromone
     * @param array       $coords all the coords of the neighbors tiles of the asked tile
     * @param GameMYR     $game
     * @param PlayerMYR   $player
     * @param TileTypeMYR $tileType
     * @param int         $x the abscissa of asked tile
     * @param int         $y the ordinate of asked tile
     * @return void
     * @throws Exception
     */
    private function tryToPlacePheromone(Array $coords, GameMYR $game, PlayerMYR $player,
        TileTypeMYR $tileType, int $x, int $y) : void
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
        $this->createAndPlacePheromone($tiles, $player, $tileType);
    }
}