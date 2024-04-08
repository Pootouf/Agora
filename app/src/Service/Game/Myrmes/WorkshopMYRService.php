<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameGoalMYR;
use App\Entity\Game\Myrmes\GoalMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use App\Repository\Game\Myrmes\PheromonMYRRepository;
use App\Repository\Game\Myrmes\PheromonTileMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\PreyMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @codeCoverageIgnore
 */
class WorkshopMYRService
{

    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly MYRService $MYRService,
                                private readonly PheromonTileMYRRepository $pheromoneTileMYRRepository,
                                private readonly PreyMYRRepository $preyMYRRepository,
                                private readonly TileMYRRepository $tileMYRRepository,
                                private readonly PheromonMYRRepository $pheromonMYRRepository,
                                private readonly ResourceMYRRepository $resourceMYRRepository,
                                private readonly PlayerResourceMYRRepository $playerResourceMYRRepository,
                                private readonly NurseMYRRepository $nurseMYRRepository,
                                private readonly AnthillHoleMYRRepository $anthillHoleMYRRepository)
    {}


    /**
     * canPlayerDoGoal: return true if the player can do the selected goal.
     *                  If the pheromone goal is selected, always true
     * @param PlayerMYR $player
     * @param GoalMYR $goal
     * @return bool
     * @throws Exception
     */
    public function canPlayerDoGoal(PlayerMYR $player, GoalMYR $goal): bool
    {
        $goalName = $goal->getName();
        $goalDifficulty = $goal->getDifficulty();

        if (!$this->doesPlayerHaveDoneGoalWithLowerDifficulty($player, $goal)) {
            return false;
        }

        return match ($goalName) {
            MyrmesParameters::GOAL_RESOURCE_FOOD_NAME => $this->canPlayerDoFoodGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_RESOURCE_STONE_NAME => $this->canPlayerDoStoneGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_RESOURCE_STONE_OR_DIRT_NAME => $this->canPlayerDoStoneOrDirtGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_LARVAE_NAME => $this->canPlayerDoLarvaeGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_PREY_NAME => $this->canPlayerDoPreyGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_SOLDIER_NAME => $this->canPlayerDoSoldierGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_SPECIAL_TILE_NAME => $this->canPlayerDoSpecialTileGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_NURSES_NAME => $this->canPlayerDoNursesGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_ANTHILL_LEVEL_NAME => $this->canPlayerDoAnthillLevelGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_PHEROMONE_NAME => true, // Always true, player can cancel later
            default => throw new Exception("Goal does not exist"),
        };
    }

    /**
     * doGoal: retrieve the needed resources from the player to accomplish the goal with the selected nurse
     * @param PlayerMYR $player
     * @param GameGoalMYR $gameGoalMYR
     * @param NurseMYR $nurse
     * @return void
     * @throws Exception if the player needs to do a goal with a lower difficulty before
     *                   or if the selected goal is a goal of type :
     *                          STONE OR DIRT
     *                          SPECIAL TILE
     *                          PHEROMONE
     *                   these goals needs player interactivity and other parameters
     *
     */
    public function doGoal(PlayerMYR $player, GameGoalMYR $gameGoalMYR, NurseMYR $nurse): void
    {
        $goal = $gameGoalMYR->getGoal();
        $goalName = $goal->getName();
        $goalDifficulty = $goal->getDifficulty();

        if (!$this->doesPlayerHaveDoneGoalWithLowerDifficulty($player, $goal)) {
            throw new Exception("The player can't do this goal, he must do " .
                "a goal with a lower difficulty before");
        }

        match ($goalName) {
            MyrmesParameters::GOAL_RESOURCE_FOOD_NAME => $this->retrieveResourcesToDoFoodGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_RESOURCE_STONE_NAME => $this->retrieveResourcesToDoStoneGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_RESOURCE_STONE_OR_DIRT_NAME => throw new Exception("Call method 
                                                                 doStoneOrDirtGoal to do this Goal"),
            MyrmesParameters::GOAL_LARVAE_NAME => $this->retrieveResourcesToDoLarvaeGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_PREY_NAME => $this->retrieveResourcesToDoPreyGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_SOLDIER_NAME => $this->retrieveResourcesToDoSoldierGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_NURSES_NAME => $this->retrieveResourcesToDoNursesGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_ANTHILL_LEVEL_NAME => $this->retrieveResourcesToDoAnthillLevelGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_SPECIAL_TILE_NAME => throw new Exception("Call method 
                                                                    doSpecialTileGoal to do this Goal"),
            MyrmesParameters::GOAL_PHEROMONE_NAME => throw new Exception("Call method 
                                                                    doPheromoneGoal to do this Goal"),
            default => throw new Exception("Goal does not exist"),
        };
        $this->setNurseUsedInGoal($nurse);
        $this->calculateScoreAfterGoalAccomplish($gameGoalMYR, $player);
    }

    /**
     * doStoneOrDirtGoal: make the player accomplish the stone or dirt goal if possible
     * @param PlayerMYR $player
     * @param GoalMYR $goal
     * @param int $stoneQuantity
     * @param int $dirtQuantity
     * @return void
     * @throws Exception
     */
    public function doStoneOrDirtGoal(PlayerMYR $player, GoalMYR $goal,
        int $stoneQuantity,
        int $dirtQuantity
    ) : void
    {
        if (!$this->canPlayerDoStoneOrDirtGoal($player, $goal->getDifficulty())
            || ($goal->getDifficulty() == MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE
                && $stoneQuantity + $dirtQuantity < 3)
            || ($goal->getDifficulty() == MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE
                && $stoneQuantity + $dirtQuantity < 6)
        ) {
            throw new Exception('Player cannot do stone or dirt goal');
        }
        $resourceStone = $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_STONE);
        $resourceDirt = $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_DIRT);

        if ($resourceStone->getQuantity() < $stoneQuantity) {
            throw new Exception('Not enough stone to pay the cost');
        }
        if ($resourceDirt->getQuantity() < $dirtQuantity) {
            throw new Exception('Not enough dirt to pay the cost');
        }

        $resourceStone->setQuantity($resourceStone->getQuantity() - $stoneQuantity);
        $resourceDirt->setQuantity($resourceDirt->getQuantity() - $dirtQuantity);

        $this->entityManager->persist($resourceStone);
        $this->entityManager->persist($resourceDirt);
        $this->entityManager->flush();
    }


    /**
     * Manage resources and purchase about position of nurse
     *
     * @param PlayerMYR    $player
     * @param int          $workshop
     * @param TileMYR|null $tile
     * @return void
     * @throws Exception
     */
    public function manageWorkshop(PlayerMYR $player, int $workshop, TileMYR $tile = null): void
    {
        if (!$this->canChooseThisBonus($player, $workshop)) {
            throw new Exception("player can not choose this bonus");
        }
        $nurses = $this->MYRService->getNursesAtPosition($player, MyrmesParameters::WORKSHOP_AREA);
        $nursesCount = $nurses->count();
        switch ($workshop) {
            case MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA:
                $this->manageAnthillHole($nursesCount, $player, $tile);
                break;
            case MyrmesParameters::WORKSHOP_LEVEL_AREA:
                $this->manageLevel($nursesCount, $player);
                break;
            case MyrmesParameters::WORKSHOP_NURSE_AREA:
                if ($this->canBuyNurse($player)) {
                    $this->manageNurse($nursesCount, $player);
                }
                break;
            case MyrmesParameters::WORKSHOP_GOAL_AREA:
                break;
            default:
                throw new Exception("Don't give bonus");
        }
        $this->entityManager->flush();
    }

    /**
     * getAvailableAnthillHolesPositions : returns a list of possible new anthill holes positions
     * @param PlayerMYR $player
     * @return ArrayCollection<Int, TileMYR>
     */
    public function getAvailableAnthillHolesPositions(PlayerMYR $player) : ArrayCollection
    {
        $pheromones = $player->getPheromonMYRs();
        $result = new ArrayCollection();
        foreach ($pheromones as $pheromone) {
            $pheromoneTiles = $pheromone->getPheromonTiles();
            foreach ($pheromoneTiles as $pheromoneTile) {
                $tile = $pheromoneTile->getTile();
                $adjacentTiles = $this->getAdjacentTiles($tile);
                foreach ($adjacentTiles as $adjacentTile) {
                    if ($this->isValidPosition($player, $adjacentTile)) {
                        $result->add($adjacentTile);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * canChooseThisBonus : checks if the player can choose the selected bonus in the workshopArea
     * @param PlayerMYR $player
     * @param int       $workshopArea
     * @return bool
     */
    private function canChooseThisBonus(PlayerMYR $player, int $workshopArea) : bool
    {
        if ($player->getPhase() != MyrmesParameters::PHASE_WORKSHOP) {
            return false;
        }
        return $this->MYRService->getNursesAtPosition($player, $workshopArea) >= 0;
    }

    /**
     * playerReachedAnthillHoleLimit : checks if the player reached his limit of anthill holes
     * @param PlayerMYR $player
     * @return bool
     */
    private function playerReachedAnthillHoleLimit(PlayerMYR $player) : bool
    {
        return $player->getAnthillHoleMYRs()->count() >= MyrmesParameters::MAX_ANTHILL_HOLE_NB;
    }

    /**
     * isValidPosition : checks if the tile chosen is valid for the player to place a new anthill hole
     * @param PlayerMYR $player
     * @param TileMYR   $tile
     * @return bool
     */
    private function isValidPosition(PlayerMYR $player, TileMYR $tile) : bool
    {
        if ($tile->getType() === MyrmesParameters::WATER_TILE_TYPE) {
            return false;
        }
        $mainBoard = $player->getGameMyr()->getMainBoardMYR();
        $pheromoneTile = $this->pheromoneTileMYRRepository->findBy(["mainBoard" => $mainBoard, "tile" => $tile]);
        if ($pheromoneTile != null) {
            return false;
        }
        $anthillHole = $this->anthillHoleMYRRepository->findBy(["mainBoard" => $mainBoard, "tile" => $tile]);
        if ($anthillHole != null) {
            return false;
        }
        $prey = $this->preyMYRRepository->findBy(["mainBoardMYR" => $mainBoard, "tile" => $tile]);
        if ($prey != null) {
            return false;
        }
        $adjacentTiles = $this->getAdjacentTiles($tile);
        $playerPheromones = $this->pheromonMYRRepository->findBy(["player" => $player]);
        foreach ($adjacentTiles as $adjacentTile) {
            foreach ($playerPheromones as $playerPheromone) {
                if ($playerPheromone->contains($adjacentTile)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * getAdjacentTiles : return all adjacent tiles of a tile
     * @param TileMYR $tile
     * @return ArrayCollection<Int, TileMYR>
     */
    private function getAdjacentTiles(TileMYR $tile) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $result = new ArrayCollection();
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX, "coord_Y" => $coordY - 2]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX, "coord_Y" => $coordY + 2]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX + 1, "coord_Y" => $coordY + 2]);
        $result->add($newTile);
        return $result;
    }

    /**
     * Manage all change driven by add anthill hole
     *
     * @param int       $nursesCount
     * @param PlayerMYR $player
     * @param TileMYR   $tile
     * @return void
     * @throws Exception
     */
    private function manageAnthillHole(int $nursesCount, PlayerMYR $player, TileMYR $tile) : void
    {
        if ($this->playerReachedAnthillHoleLimit($player)) {
            throw new Exception("can't place more anthill holes");
        }
        if (!$this->isValidPosition($player, $tile)) {
            throw new Exception("can't place an anthill hole there");
        }
        if ($nursesCount == 1) {
            $anthillHole = new AnthillHoleMYR();
            $anthillHole->setTile($tile);
            $anthillHole->setPlayer($player);
            $anthillHole->setMainBoardMYR($player->getGameMyr()->getMainBoardMYR());
            $this->entityManager->persist($anthillHole);
            $player->addAnthillHoleMYR($anthillHole);
            $this->giveDirtToPlayer($player);
            $this->entityManager->persist($player);
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA
            );
        }
    }

    /**
     * Check if player can increase anthill level
     * @param PlayerMYR $player
     * @param array $requestResources
     * @return bool
     */
    private function canIncreaseLevel(PlayerMYR $player, array $requestResources) : bool
    {
        $pBoard = $player->getPersonalBoardMYR();

        $haveResource = array_fill_keys(array_keys($requestResources), 0);

        foreach ($pBoard->getPlayerResourceMYRs() as $playerResource)
        {
            $resource = $playerResource->getResource();
            if (array_key_exists($resource->getDescription(), $requestResources))
            {
                $haveResource[$resource->getDescription()]
                    = $playerResource->getQuantity();
            }
        }

        foreach (array_keys($haveResource) as $resource)
        {
            if ($haveResource[$resource] < $requestResources[$resource]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get buy about start anthill level
     * @param int $level
     * @return array
     * @throws Exception
     */
    private function getBuyForLevel(int $level) : array
    {
        return match ($level) {
            0 => MyrmesParameters::BUY_RESOURCE_FOR_LEVEL_ONE,
            1 => MyrmesParameters::BUY_RESOURCE_FOR_LEVEL_TWO,
            2 => MyrmesParameters::BUY_RESOURCE_FOR_LEVEL_THREE,
            default => throw new Exception("Don't buy"),
        };
    }

    /**
     * Player spend resource necessary for add anthill level
     * @param PlayerMYR $player
     * @param string $resourceStr
     * @param int $count
     * @return void
     */
    private function spendResource(PlayerMYR $player, string $resourceStr, int $count) : void
    {
        $personalBoard = $player->getPersonalBoardMYR();

        foreach ($personalBoard->getPlayerResourceMYRs() as $playerResource)
        {
            $resource = $playerResource->getResource();
            if ($resource->getDescription() === $resourceStr)
            {
                $oldQuantity = $playerResource->getQuantity();
                $playerResource->setQuantity($oldQuantity - $count);
                return;
            }
        }
    }

    /**
     * Manage all changes driven by level increase
     * @param int $nursesCount
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    private function manageLevel(int $nursesCount, PlayerMYR $player) : void
    {
        if ($nursesCount == 1)
        {
            $personalBoard = $player->getPersonalBoardMYR();
            $buys = $this->getBuyForLevel($personalBoard->getAnthillLevel());

            if (!$this->canIncreaseLevel($player, $buys)) {
                throw new Exception("Can't increase anthill level");
            }

            foreach (array_keys($buys) as $resource)
            {
                $this->spendResource($player, $resource, $buys[$resource]);
            }

            $level = $personalBoard->getAnthillLevel();
            $personalBoard->setAnthillLevel($level + 1);
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::WORKSHOP_LEVEL_AREA
            );
        }
    }

    /**
     * Check if player can buy nurse
     * @param PlayerMYR $player
     * @return bool
     */
    private function canBuyNurse(PlayerMYR $player) : bool
    {
        $pBoard = $player->getPersonalBoardMYR();

        return $pBoard->getLarvaCount() >= 2
            && $pBoard->getPlayerResourceMYRs()->count() >= 2;
    }

    /**
     * Manage all change driven by add nurse
     * @param int $nursesCount
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    private function manageNurse(int $nursesCount, PlayerMYR $player) : void
    {
        $pBoard = $player->getPersonalBoardMYR();

        if ($nursesCount == 1)
        {
            $pBoard->setLarvaCount($pBoard->getLarvaCount() - 2);

            for ($i = $pBoard->getPlayerResourceMYRs()->count() - 3;
                 $i < $pBoard->getPlayerResourceMYRs()->count();
                 $i++) {
                $playerResource = $pBoard->getPlayerResourceMYRs()->get($i);
                $pBoard->removePlayerResourceMYR($playerResource);
            }

            $nurse = $this->nurseMYRRepository->findOneBy(['available' => false]);
            if($nurse != null) {
                $nurse->setAvailable(true);
                $nurse->setArea(MyrmesParameters::BASE_AREA);
                $this->entityManager->persist($nurse);
                $this->entityManager->persist($pBoard);
            } else {
                throw new Exception("Can't have a new nurse, already reach the limit");
            }
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::WORKSHOP_NURSE_AREA
            );
        }
    }

    /**
     * giveDirtToPlayer : gives a resource of dirt to the player
     * @param PlayerMYR $player
     * @return void
     */
    private function giveDirtToPlayer(PlayerMYR $player) : void
    {
        $dirt = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_DIRT]);
        $playerDirt = $this->playerResourceMYRRepository->findOneBy(["resource" => $dirt]);
        if ($playerDirt != null) {
            $playerDirt->setQuantity($playerDirt->getQuantity() + 1);
            $this->entityManager->persist($playerDirt);
        } else {
            $playerDirt = new PlayerResourceMYR();
            $playerDirt->setResource($dirt);
            $playerDirt->setQuantity(1);
            $playerDirt->setPersonalBoard($player->getPersonalBoardMYR());
            $this->entityManager->persist($playerDirt);
            $player->getPersonalBoardMYR()->addPlayerResourceMYR($playerDirt);
            $this->entityManager->persist($player->getPersonalBoardMYR());
        }
        $this->entityManager->flush();
    }

    /**
     * canPlayerDoFoodGoal: return true if the player can do a food goal with the selected difficulty
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return bool
     * @throws Exception
     */
    private function canPlayerDoFoodGoal(PlayerMYR $player, int $goalDifficulty) : bool
    {
        return match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
                $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_GRASS)
                    ->getQuantity() >= 3,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_GRASS)
                    ->getQuantity() >= 6,
            default => throw new Exception("Goal difficulty invalid for food goal"),
        };
    }

    /**
     * canPlayerDoStoneGoal: return true if the player can do a stone goal with the selected difficulty
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return bool
     * @throws Exception
     */
    private function canPlayerDoStoneGoal(PlayerMYR $player, int $goalDifficulty) : bool
    {
        return match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_STONE)
                    ->getQuantity() >= 3,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_STONE)
                    ->getQuantity() >= 6,
            default => throw new Exception("Goal difficulty invalid for stone goal"),
        };
    }

    /**
     * canPlayerDoStoneOrDirtGoal: return true if the player can do a stone or dirt goal with the selected difficulty
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return bool
     * @throws Exception
     */
    private function canPlayerDoStoneOrDirtGoal(PlayerMYR $player, int $goalDifficulty) : bool
    {
        return match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
                $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_STONE)
                    ->getQuantity()
                + $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_DIRT)
                    ->getQuantity()
                >= 3,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_STONE)
                    ->getQuantity()
                + $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_DIRT)
                    ->getQuantity()
                >= 6,
            default => throw new Exception("Goal difficulty invalid for stone or dirt goal"),
        };
    }

    /**
     * canPlayerDoLarvaeGoal: return true if the player have enough larvae to do the larvae goal with the selected
     *                        difficulty
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return bool
     * @throws Exception
     */
    private function canPlayerDoLarvaeGoal(PlayerMYR $player, int $goalDifficulty) : bool
    {
        return match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
                $player->getPersonalBoardMYR()->getLarvaCount() >= 5,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $player->getPersonalBoardMYR()->getLarvaCount() >= 9,
            default => throw new Exception("Goal difficulty invalid for larvae goal"),
        };
    }

    /**
     * canPlayerDoPreyGoal: return true if the player have enough prey to do the prey goal with the selected difficulty
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return bool
     * @throws Exception
     */
    private function canPlayerDoPreyGoal(PlayerMYR $player, int $goalDifficulty) : bool
    {
        return match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
                $player->getPreyMYRs()->count() >= 2,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $player->getPreyMYRs()->count() >= 3,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $player->getPreyMYRs()->count() >= 4,
            default => throw new Exception("Goal difficulty invalid for prey goal"),
        };
    }

    /**
     * canPlayerDoSoldierGoal: return true if the player have enough soldiers to do the soldier goal with the selected
     *                         difficulty
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return bool
     * @throws Exception
     */
    private function canPlayerDoSoldierGoal(PlayerMYR $player, int $goalDifficulty) : bool
    {
        return match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
                $player->getPersonalBoardMYR()->getWarriorsCount() >= 2,
            default => throw new Exception("Goal difficulty invalid for soldier goal"),
        };
    }


    /**
     * canPlayerDoSpecialTileGoal: return true if the player have enough specialTile to do the specialTile goal with the
     *                              selected difficulty
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return bool
     * @throws Exception
     */
    private function canPlayerDoSpecialTileGoal(PlayerMYR $player, int $goalDifficulty) : bool
    {
        return match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
                $this->getSpecialTilesOfPlayer($player)->count() >= 2,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $this->getSpecialTilesOfPlayer($player)->count() >= 3,
            default => throw new Exception("Goal difficulty invalid for specialTile goal"),
        };
    }

    /**
     * canPlayerDoNursesGoal: return true if the player have enough nurses to do the nurses goal with the selected
     *                        difficulty
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return bool
     * @throws Exception
     */
    private function canPlayerDoNursesGoal(PlayerMYR $player, int $goalDifficulty) : bool
    {
        $nursesOfPlayer = $player->getPersonalBoardMYR()->getNurses()->count();
        return match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $nursesOfPlayer >= 6
                && $nursesOfPlayer - $this->getNursesUsedInGoalFromPlayer($player)->count() >= 1,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $nursesOfPlayer >= 8
                && $nursesOfPlayer - $this->getNursesUsedInGoalFromPlayer($player)->count() >= 2,
            default => throw new Exception("Goal difficulty invalid for nurse goal"),
        };
    }


    /**
     * canPlayerDoAnthillLevelGoal: return true if the player can do the anthill level goal with the selected difficulty
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return bool
     * @throws Exception
     */
    private function canPlayerDoAnthillLevelGoal(PlayerMYR $player, int $goalDifficulty) : bool
    {
        return match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $player->getPersonalBoardMYR()->getAnthillLevel() >= MyrmesParameters::ANTHILL_LEVEL_TWO,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $player->getPersonalBoardMYR()->getAnthillLevel() >= MyrmesParameters::ANTHILL_LEVEL_THREE,
            default => throw new Exception("Goal difficulty invalid for anthill level goal"),
        };
    }


    /**
     * getNumberOfResourcesFromSelectedType: return the number of resources of the player from the selected type
     * @param PlayerMYR $player
     * @param string $selectedType
     * @return PlayerResourceMYR
     */
    private function getPlayerResourcesFromSelectedType(PlayerMYR $player, string $selectedType) : PlayerResourceMYR
    {
        return $player->getPersonalBoardMYR()->getPlayerResourceMYRs()
            ->filter(
                function (PlayerResourceMYR $resourceMYR) use ($selectedType) {
                    return $resourceMYR->getResource()->getDescription() == $selectedType;
                }
            )->first();
    }

    /**
     * getSpecialTilesOfPlayer: return the special tiles of the player
     * @param PlayerMYR $player
     * @return Collection<PheromonMYR>
     */
    private function getSpecialTilesOfPlayer(PlayerMYR $player) : Collection
    {
        return $player->getPheromonMYRs()->filter(
            function (PheromonMYR $pheromone) {
                return $pheromone->getType()->getType() == MyrmesParameters::SPECIAL_TILE_TYPE_FARM
                    || $pheromone->getType()->getType() == MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY
                    || $pheromone->getType()->getType() == MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL;
            }
        );
    }

    /**
     * getNursesUsedInGoalFromPlayer: return the nurses used to complete goal by the player
     * @param PlayerMYR $player
     * @return Collection
     */
    private function getNursesUsedInGoalFromPlayer(PlayerMYR $player): Collection
    {
        return $player->getPersonalBoardMYR()->getNurses()->filter(
            function (NurseMYR $nurseMYR) {
                return $nurseMYR->getArea() == MyrmesParameters::GOAL_AREA;
            }
        );
    }


    /**
     * retrieveResourcesToDoFoodGoal: retrieve the resources needed from the player to accomplish the food goal
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return void
     * @throws Exception
     */
    private function retrieveResourcesToDoFoodGoal(PlayerMYR $player, int $goalDifficulty): void
    {
        if (!$this->canPlayerDoFoodGoal($player, $goalDifficulty)) {
            throw new Exception('Player cannot do food goal');
        }
        $resource = $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_GRASS);
        match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
                $resource->setQuantity($resource->getQuantity() - 3),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $resource->setQuantity($resource->getQuantity() - 6),
            default => throw new Exception("Goal difficulty invalid for food goal"),
        };
        $this->entityManager->persist($resource);
        $this->entityManager->flush();
    }

    /**
     * retrieveResourcesToDoStoneGoal: retrieve the resources needed from the player to accomplish the stone goal
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return void
     * @throws Exception
     */
    private function retrieveResourcesToDoStoneGoal(PlayerMYR $player, int $goalDifficulty): void
    {
        if (!$this->canPlayerDoStoneGoal($player, $goalDifficulty)) {
            throw new Exception('Player cannot do stone goal');
        }
        $resource = $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_STONE);
        match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
            $resource->setQuantity($resource->getQuantity() - 3),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
            $resource->setQuantity($resource->getQuantity() - 6),
            default => throw new Exception("Goal difficulty invalid for stone goal"),
        };
        $this->entityManager->persist($resource);
        $this->entityManager->flush();
    }

    /**
     * retrieveResourcesToDoLarvaeGoal: retrieve the resources needed from the player to accomplish the larvae goal
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return void
     * @throws Exception
     */
    private function retrieveResourcesToDoLarvaeGoal(PlayerMYR $player, int $goalDifficulty): void
    {
        if (!$this->canPlayerDoLarvaeGoal($player, $goalDifficulty)) {
            throw new Exception('Player cannot do larvae goal');
        }
        $numberOfLarva = $player->getPersonalBoardMYR()->getLarvaCount();
        match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
            $player->getPersonalBoardMYR()->setLarvaCount($numberOfLarva - 5),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
            $player->getPersonalBoardMYR()->setLarvaCount($numberOfLarva - 9),
            default => throw new Exception("Goal difficulty invalid for larvae goal"),
        };
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
    }

    /**
     * retrieveResourcesToDoPreyGoal: retrieve the resources needed from the player to accomplish the prey goal
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return void
     * @throws Exception
     */
    private function retrieveResourcesToDoPreyGoal(PlayerMYR $player, int $goalDifficulty): void
    {
        if (!$this->canPlayerDoPreyGoal($player, $goalDifficulty)) {
            throw new Exception('Player cannot do prey goal');
        }
        match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
            $this->removeSelectedNumberOfPreyFromPlayer($player, 2),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
            $this->removeSelectedNumberOfPreyFromPlayer($player, 3),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
            $this->removeSelectedNumberOfPreyFromPlayer($player, 4),
            default => throw new Exception("Goal difficulty invalid for prey goal"),
        };
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * retrieveResourcesToDoSoldierGoal: retrieve the resources needed from the player to accomplish the soldier goal
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return void
     * @throws Exception
     */
    private function retrieveResourcesToDoSoldierGoal(PlayerMYR $player, int $goalDifficulty): void
    {
        if (!$this->canPlayerDoSoldierGoal($player, $goalDifficulty)) {
            throw new Exception('Player cannot do soldier goal');
        }
        $warriorsCount = $player->getPersonalBoardMYR()->getWarriorsCount();
        match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
            $player->getPersonalBoardMYR()->setWarriorsCount($warriorsCount - 2),
            default => throw new Exception("Goal difficulty invalid for soldier goal"),
        };
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
    }

    /**
     * retrieveResourcesToDoNursesGoal: retrieve the resources needed from the player to accomplish the nurses goal
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return void
     * @throws Exception
     */
    private function retrieveResourcesToDoNursesGoal(PlayerMYR $player, int $goalDifficulty): void
    {
        if (!$this->canPlayerDoNursesGoal($player, $goalDifficulty)) {
            throw new Exception('Player cannot do nurses goal');
        }
        match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
            $this->removeSelectedNumberOfNursesFromPlayer($player, 1),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
            $this->removeSelectedNumberOfNursesFromPlayer($player, 2),
            default => throw new Exception("Goal difficulty invalid for nurses goal"),
        };
    }


    /**
     * retrieveResourcesToDoAnthillLevelGoal: retrieve the resources needed from the player to accomplish
     *                                        the anthill level goal
     * @param PlayerMYR $player
     * @param int $goalDifficulty
     * @return void
     * @throws Exception
     */
    private function retrieveResourcesToDoAnthillLevelGoal(PlayerMYR $player, int $goalDifficulty): void
    {
        if (!$this->canPlayerDoAnthillLevelGoal($player, $goalDifficulty)) {
            throw new Exception('Player cannot do anthill level goal');
        }
        $anthillLevel = $player->getPersonalBoardMYR()->getAnthillLevel();
        match ($goalDifficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
            $player->getPersonalBoardMYR()->setAnthillLevel($anthillLevel - 1),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
            $player->getPersonalBoardMYR()->setAnthillLevel($anthillLevel - 2),
            default => throw new Exception("Goal difficulty invalid for anthill level goal"),
        };
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
    }


    /**
     * removeSelectedNumberOfPreyFromPlayer: remove the selected number of prey from the player inventory
     * @param PlayerMYR $player
     * @param int $preyToRemove
     * @return void
     */
    private function removeSelectedNumberOfPreyFromPlayer(PlayerMYR $player, int $preyToRemove) : void
    {
        $prey = $player->getPreyMYRs();
        for ($i = 0; $i < $preyToRemove; $i++) {
            $player->removePreyMYR($prey[$i]);
            $this->entityManager->remove($prey[$i]);
        }
    }

    /**
     * removeSelectedNumberOfNursesFromPlayer: remove the selected number of nurses from the player inventory
     * @param PlayerMYR $player
     * @param int $nurseToRemove
     * @return void
     */
    private function removeSelectedNumberOfNursesFromPlayer(PlayerMYR $player, int $nurseToRemove) : void
    {
        $nurses = $player->getPersonalBoardMYR()->getNurses();
        for ($i = 0; $i < $nurseToRemove; $i++) {
            if ($nurses[$i]->getArea == MyrmesParameters::GOAL_AREA ) {
                $nurseToRemove++;
                continue;
            }
            $player->getPersonalBoardMYR()->removeNurse($nurses[$i]);
            $this->entityManager->remove($nurses[$i]);
        }
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
    }

    /**
     * setNurseUsedInGoal: make the nurse unusable and place it in the goal area
     * @param NurseMYR $nurse
     * @return void
     */
    private function setNurseUsedInGoal(NurseMYR $nurse): void
    {
        $nurse->setArea(MyrmesParameters::GOAL_AREA);
        $nurse->setAvailable(false);
        $this->entityManager->persist($nurse);
        $this->entityManager->flush();
    }

    /**
     * calculateScoreAfterGoalAccomplish: recalculate the score of the player of the game after the selected player
     *                                    accomplish the goal
     * @param GameGoalMYR $gameGoal
     * @param PlayerMYR $player
     * @return void
     */
    private function calculateScoreAfterGoalAccomplish(GameGoalMYR $gameGoal, PlayerMYR $player): void
    {
        $previousPlayers = $gameGoal->getPrecedentsPlayers();
        foreach ($previousPlayers as $previousPlayer) {
            $previousPlayer->setScore(
                $previousPlayer->getScore() + MyrmesParameters::SCORE_INCREASE_GOAL_ALREADY_DONE
            );
            $this->entityManager->persist($previousPlayer);
        }
        match ($gameGoal->getGoal()->getDifficulty()) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
                $player->setScore($player->getScore() + MyrmesParameters::SCORE_INCREASE_GOAL_DIFFICULTY_ONE),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $player->setScore($player->getScore() + MyrmesParameters::SCORE_INCREASE_GOAL_DIFFICULTY_TWO),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $player->setScore($player->getScore() + MyrmesParameters::SCORE_INCREASE_GOAL_DIFFICULTY_THREE),
        };
        $this->entityManager->persist($player);

        $gameGoal->addPrecedentsPlayer($player);
        $this->entityManager->persist($gameGoal);

        $this->entityManager->flush();
    }

    /**
     * doesPlayerHaveDoneGoalWithLowerDifficulty: return true if the player have done a goal with a lower difficulty
     * @param PlayerMYR $player
     * @param GoalMYR $goal
     * @return bool
     */
    private function doesPlayerHaveDoneGoalWithLowerDifficulty(PlayerMYR $player, GoalMYR $goal): bool
    {
        return match ($goal->getDifficulty()) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE => true,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO => $this->doesPlayerHaveDoneDifficultyOneGoal($player),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE => $this->doesPlayerHaveDoneDifficultyTwoGoal($player),
        };
    }

    /**
     * doesPlayerHaveDoneDifficultyOneGoal: return true if the player has done a difficulty one goal before
     * @param PlayerMYR $player
     * @return bool
     */
    private function doesPlayerHaveDoneDifficultyOneGoal(PlayerMYR $player) : bool
    {
        $game = $player->getGameMyr();
        $goals = $game->getMainBoardMYR()->getGameGoalsLevelOne();
        return  $this->doesPlayerHaveDoneOneOfTheSelectedGoal($goals, $player);
    }

    /**
     * doesPlayerHaveDoneDifficultyTwoGoal: return true if the player has done a difficulty two goal before
     * @param PlayerMYR $player
     * @return bool
     */
    private function doesPlayerHaveDoneDifficultyTwoGoal(PlayerMYR $player) : bool
    {
        $game = $player->getGameMyr();
        $goals = $game->getMainBoardMYR()->getGameGoalsLevelTwo();
        return  $this->doesPlayerHaveDoneOneOfTheSelectedGoal($goals, $player);
    }

    /**
     * doesPlayerHaveDoneOneOfTheSelectedGoal: return true if the player has done one of the selected goal
     * @param Collection<GameGoalMYR> $goals
     * @param PlayerMYR $player
     * @return bool
     */
    private function doesPlayerHaveDoneOneOfTheSelectedGoal(Collection $goals, PlayerMYR $player): bool
    {
        foreach ($goals as $goal) {
            foreach ($goal->getPrecedentsPlayers() as $previousPlayer) {
                if ($previousPlayer === $player) {
                    return true;
                }
            }
        }
        return false;
    }
}