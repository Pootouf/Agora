<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Repository\Game\Myrmes\GardenWorkerMYRRepository;
use App\Repository\Game\Myrmes\AnthillWorkerMYRRepository;
use App\Repository\Game\Myrmes\PheromonTileMYRRepository;
use App\Repository\Game\Myrmes\PreyMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;

class WorkerMYRService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly MYRService $MYRService,
                                private readonly PheromoneMYRService $pheromoneMYRService,
                                private readonly AnthillWorkerMYRRepository $anthillWorkerMYRRepository,
                                private readonly PreyMYRRepository $preyMYRRepository,
                                private readonly TileMYRRepository $tileMYRRepository,
                                private readonly GardenWorkerMYRRepository $gardenWorkerMYRRepository,
                                private readonly PheromonTileMYRRepository $pheromoneTileMYRRepository
    )
    {}

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
     * @param PlayerMYR $player
     * @return int
     */
    public function getNeededSoldiers(int $coordX, int $coordY, GameMYR $gameMYR, PlayerMYR $player): int
    {
        $tile = $this->tileMYRRepository->findOneBy([
            "coord_Y" => $coordY,
            "coord_X" => $coordX
        ]);
        if ($tile == null) {
            throw new InvalidArgumentException("Not a valid tile, can't identify if there is a prey on it");
        }
        $prey = $this->pheromoneMYRService->getPrey($gameMYR, $tile);
        $pheromoneTile = $this->getPheromoneTileOnTile($tile, $gameMYR);
        $soldiersForPheromone = 0;
        if($pheromoneTile != null) {
            $soldiersForPheromone = $pheromoneTile->getPheromonMYR()->getPlayer() === $player ? 0 : 1;
        }

        return ($prey == null ? 0 : MyrmesParameters::NUMBER_SOLDIERS_FOR_ATTACK_PREY[$prey->getType()])
            + $soldiersForPheromone;
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
     * @throws InvalidArgumentException
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
            throw new InvalidArgumentException("Not a valid tile, can't identify if there is a prey on it");
        }
        $pheromoneTile1 = $this->pheromoneTileMYRRepository->findOneBy([
            "tile" => $tile1,
            "mainBoard" => $gameMYR->getMainBoardMYR()
        ]);
        $pheromoneTile2 = $this->pheromoneTileMYRRepository->findOneBy([
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
            'personalBoardMYR' => $personalBoard,
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
        if(!$this->pheromoneMYRService->isPositionAvailable($playerMYR->getGameMyr(), $tileMYR)) {
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
        return $this->pheromoneTileMYRRepository->findOneBy(
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


}