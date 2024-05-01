<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameGoalMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GoalMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\MyrmesTranslation;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
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


class WorkshopMYRService
{

    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly MYRService $myrService,
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
     * getAvailableAnthillHolesPositions : returns a list of possible new anthill holes positions
     * @param PlayerMYR $player
     * @return ArrayCollection<Int, TileMYR>
     */
    public function getAvailableAnthillHolesPositions(PlayerMYR $player) : ArrayCollection
    {
        $pheromones = $player->getPheromonMYRs();
        $antHillHoles = $player->getAnthillHoleMYRs();
        $result = new ArrayCollection();
        foreach ($pheromones as $pheromone) {
            $pheromoneTiles = $pheromone->getPheromonTiles();
            foreach ($pheromoneTiles as $pheromoneTile) {
                $tile = $pheromoneTile->getTile();
                $adjacentTiles = $this->getAdjacentTiles($tile);
                foreach ($adjacentTiles as $adjacentTile) {
                    if ($adjacentTile != null && $this->isValidPosition($player, $adjacentTile)
                        && !$result->contains($adjacentTile)) {
                        $result->add($adjacentTile);
                    }
                }
            }
        }
        foreach ($antHillHoles as $antHillHole) {
            $tile = $antHillHole->getTile();
            $adjacentTiles = $this->getAdjacentTiles($tile);
            foreach ($adjacentTiles as $adjacentTile) {
                if ($adjacentTile != null && $this->isValidPosition($player, $adjacentTile)
                    && !$result->contains($adjacentTile)) {
                    $result->add($adjacentTile);
                }
            }
        }
        return $result;
    }

    /**
     * getAnthillHoleFromTile : return the anthill hole if it exists on the given tile from the given game
     * @param TileMYR $tile
     * @param GameMYR $game
     * @return AnthillHoleMYR|null
     */
    public function getAnthillHoleFromTile(TileMYR $tile, GameMYR $game): ?AnthillHoleMYR
    {
        return $this->anthillHoleMYRRepository->findOneBy([
            'tile' => $tile,
            'mainBoardMYR' => $game->getMainBoardMYR()
        ]);
    }


    /**
     * canPlayerDoGoal: return true if the player can do the selected goal.
     *                  If the pheromone goal is selected, always true
     * @param PlayerMYR $player
     * @param GameGoalMYR $gameGoal
     * @return bool
     * @throws Exception
     */
    public function canPlayerDoGoal(PlayerMYR $player, GameGoalMYR $gameGoal): bool
    {
        $goal = $gameGoal->getGoal();
        $goalName = $goal->getName();
        $goalDifficulty = $goal->getDifficulty();

        if ($this->hasPlayerAlreadyDoneSelectedGoal($player, $gameGoal)) {
            return false;
        }

        if (!$this->doesPlayerHaveDoneGoalWithLowerDifficulty($player, $goal)) {
            return false;
        }

        return match ($goalName) {
            MyrmesParameters::GOAL_RESOURCE_FOOD_NAME => $this->canPlayerDoFoodGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_RESOURCE_STONE_NAME => $this->canPlayerDoStoneGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_RESOURCE_STONE_OR_DIRT_NAME =>
                $this->canPlayerDoStoneOrDirtGoal($player, $goalDifficulty),
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

        if ($this->hasPlayerAlreadyDoneSelectedGoal($player, $gameGoalMYR)) {
            throw new Exception("Goal already done");
        }

        if (!$this->doesPlayerHaveDoneGoalWithLowerDifficulty($player, $goal)) {
            throw new Exception(MyrmesTranslation::ERROR_CANNOT_DO_GOAL_LOWER_DIFFICULTY_NEEDED);
        }

        match ($goalName) {
            MyrmesParameters::GOAL_RESOURCE_FOOD_NAME => $this->retrieveResourcesToDoFoodGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_RESOURCE_STONE_NAME => $this->retrieveResourcesToDoStoneGoal(
                $player, $goalDifficulty
            ),
            MyrmesParameters::GOAL_RESOURCE_STONE_OR_DIRT_NAME => throw new Exception("Call method
                                                                 doStoneOrDirtGoal to do this Goal"),
            MyrmesParameters::GOAL_LARVAE_NAME => $this->retrieveResourcesToDoLarvaeGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_PREY_NAME => $this->retrieveResourcesToDoPreyGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_SOLDIER_NAME => $this->retrieveResourcesToDoSoldierGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_NURSES_NAME => $this->retrieveResourcesToDoNursesGoal($player, $goalDifficulty),
            MyrmesParameters::GOAL_ANTHILL_LEVEL_NAME => $this->retrieveResourcesToDoAnthillLevelGoal(
                $player, $goalDifficulty
            ),
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
     * @param GameGoalMYR $gameGoal
     * @param NurseMYR $nurse
     * @param int $stoneQuantity
     * @param int $dirtQuantity
     * @return void
     * @throws Exception
     */
    public function doStoneOrDirtGoal(PlayerMYR $player, GameGoalMYR $gameGoal,
        NurseMYR $nurse,
        int $stoneQuantity,
        int $dirtQuantity
    ) : void
    {
        $goal = $gameGoal->getGoal();
        if (!$this->doesPlayerHaveDoneGoalWithLowerDifficulty($player, $goal)) {
            throw new Exception(MyrmesTranslation::ERROR_CANNOT_DO_GOAL_LOWER_DIFFICULTY_NEEDED);
        }
        $this->retrieveResourceToDoStoneOrDirtGoal($player, $goal, $stoneQuantity, $dirtQuantity);
        $this->setNurseUsedInGoal($nurse);
        $this->calculateScoreAfterGoalAccomplish($gameGoal, $player);

        $this->entityManager->flush();
    }

    /**
     * doSpecialTileGoal: make the player accomplish the special tile goal if possible
     * @param PlayerMYR $player
     * @param GameGoalMYR $gameGoal
     * @param NurseMYR $nurse
     * @param Collection<PheromonMYR> $specialTiles
     * @return void
     * @throws Exception
     */
    public function doSpecialTileGoal(PlayerMYR $player, GameGoalMYR $gameGoal,
        NurseMYR $nurse,
        Collection $specialTiles
    ) : void
    {
        $goal = $gameGoal->getGoal();
        if (!$this->doesPlayerHaveDoneGoalWithLowerDifficulty($player, $goal)) {
            throw new Exception("The player can't do this goal, he must do " .
                "a goal with a lower difficulty before");
        }
        $this->retrieveResourceToDoSpecialTileGoal($player, $goal, $specialTiles);
        $this->setNurseUsedInGoal($nurse);
        $this->calculateScoreAfterGoalAccomplish($gameGoal, $player);

        $this->entityManager->flush();
    }

    /**
     * doPheromoneGoal: make the player accomplish the pheromone goal if possible
     * @param PlayerMYR $player
     * @param GameGoalMYR $gameGoal
     * @param NurseMYR $nurse
     * @param Collection<PheromonMYR> $pheromones
     * @return void
     * @throws Exception
     */
    public function doPheromoneGoal(PlayerMYR $player, GameGoalMYR $gameGoal,
        NurseMYR $nurse,
        Collection $pheromones
    ) : void
    {
        $goal = $gameGoal->getGoal();
        if (!$this->doesPlayerHaveDoneGoalWithLowerDifficulty($player, $goal)) {
            throw new Exception("The player can't do this goal, he must do " .
                "a goal with a lower difficulty before");
        }
        $this->retrieveResourceToDoPheromoneGoal($player, $goal, $pheromones);
        $this->setNurseUsedInGoal($nurse);
        $this->calculateScoreAfterGoalAccomplish($gameGoal, $player);

        $this->entityManager->flush();
    }

    /**
     * Manage resources and purchase about position of nurse
     *
     * @param PlayerMYR    $player
     * @param int          $selectedCraft
     * @param TileMYR|null $tile
     * @return void
     * @throws Exception
     */
    public function manageWorkshop(PlayerMYR $player, int $selectedCraft, TileMYR $tile = null): void
    {
        if ($player->getGameMyr()->getGamePhase() != MyrmesParameters::PHASE_WORKSHOP) {
            throw new Exception("Not in phase workshop, can't do the craft");
        }
        if (!$this->canChooseThisBonus($player, $selectedCraft)) {
            throw new Exception("player can not choose this bonus");
        }
        $nurses = $this->myrService->getNursesAtPosition($player, MyrmesParameters::WORKSHOP_AREA);
        if ($nurses->count() < 1) {
            throw new Exception("No more nurse to do the craft");
        }
        switch ($selectedCraft) {
            case MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA:
                $this->createNewAnthillHole($player, $tile);
                $this->myrService->manageNursesAfterBonusGive(
                    $player, 1, MyrmesParameters::WORKSHOP_AREA
                );
                break;
            case MyrmesParameters::WORKSHOP_LEVEL_AREA:
                $this->upgradePlayerAnthillLevel($player);
                $this->myrService->manageNursesAfterBonusGive(
                    $player, 1, MyrmesParameters::WORKSHOP_AREA
                );
                break;
            case MyrmesParameters::WORKSHOP_NURSE_AREA:
                $this->craftNewNurse($player);
                $this->myrService->manageNursesAfterBonusGive(
                    $player, 1, MyrmesParameters::WORKSHOP_AREA
                );
                break;
            case MyrmesParameters::WORKSHOP_GOAL_AREA:
                break;
            default:
        }
        $player->getWorkshopActions()[$selectedCraft] = 1;
        $this->entityManager->flush();
    }

    /**
     * canChooseThisBonus : checks if the player can choose the selected bonus in the workshopArea
     * @param PlayerMYR $player
     * @param int       $selectedCraft
     * @return bool
     */
    private function canChooseThisBonus(PlayerMYR $player, int $selectedCraft) : bool
    {
        if ( $selectedCraft < MyrmesParameters::WORKSHOP_GOAL_AREA
            || $selectedCraft > MyrmesParameters::WORKSHOP_NURSE_AREA)
        {
            return false;
        }

        if ($player->getPhase() != MyrmesParameters::PHASE_WORKSHOP) {
            return false;
        }
        if($player->getWorkshopActions()[$selectedCraft] > 0) {
            return false;
        }
        return $this->myrService->getNursesAtPosition($player, MyrmesParameters::WORKSHOP_AREA)->count() > 0;
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
        $pheromoneTile = $this->pheromoneTileMYRRepository->findOneBy(["mainBoard" => $mainBoard, "tile" => $tile]);
        if ($pheromoneTile != null) {
            return false;
        }
        $anthillHole = $this->anthillHoleMYRRepository->findOneBy(["mainBoardMYR" => $mainBoard, "tile" => $tile]);
        if ($anthillHole != null) {
            return false;
        }
        $prey = $this->preyMYRRepository->findOneBy(["mainBoardMYR" => $mainBoard, "tile" => $tile]);
        if ($prey != null) {
            return false;
        }
        /** @var ArrayCollection<Int, TileMYR> $adjacentTiles */
        $adjacentTiles = $this->getAdjacentTiles($tile);
        /** @var Array<PheromonMYR> $playerPheromones */
        $playerPheromones = $this->pheromonMYRRepository->findBy(["player" => $player]);
        /** @var Array<AnthillHoleMYR> $playerAnthillHoles */
        $playerAnthillHoles = $this->anthillHoleMYRRepository->findBy(["player" => $player]);
        foreach ($adjacentTiles as $adjacentTile) {
            foreach ($playerPheromones as $playerPheromone) {
                foreach ($playerPheromone->getPheromonTiles() as $pheromoneTile) {
                    if ($pheromoneTile->getTile() === $adjacentTile) {
                        return true;
                    }
                }
            }
            foreach ($playerAnthillHoles as $playerAnthillHole) {
                if ($playerAnthillHole->getTile() === $adjacentTile) {
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
        $newTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY - 2]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 2]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY + 1]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY - 1]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 2]);
        $result->add($newTile);
        return $result;
    }

    /**
     * createNewAnthillHole : create a new anthill hole for the player at the selected tile
     *
     * @param PlayerMYR    $player
     * @param TileMYR $tile
     * @return void
     * @throws Exception
     */
    private function createNewAnthillHole(PlayerMYR $player, TileMYR $tile) : void
    {
        if ($this->playerReachedAnthillHoleLimit($player)) {
            throw new Exception("can't place more anthill holes");
        }
        if (!$this->isValidPosition($player, $tile)) {
            throw new Exception("can't place an anthill hole there");
        }
        $anthillHole = new AnthillHoleMYR();
        $anthillHole->setTile($tile);
        $anthillHole->setPlayer($player);
        $anthillHole->setMainBoardMYR($player->getGameMyr()->getMainBoardMYR());
        $this->entityManager->persist($anthillHole);
        $player->addAnthillHoleMYR($anthillHole);
        $this->giveDirtToPlayer($player);
        $this->entityManager->persist($player);
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
     * upgradePlayerAnthillLevel: upgrade the level of the anthill of the player
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    private function upgradePlayerAnthillLevel(PlayerMYR $player) : void
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
        $this->entityManager->persist($personalBoard);
    }

    /**
     * Check if player can buy nurse
     * @param PlayerMYR $player
     * @return bool
     */
    private function canBuyNurse(PlayerMYR $player) : bool
    {
        $pBoard = $player->getPersonalBoardMYR();
        $resource = $this->getFoodResource($player);

        return $pBoard->getLarvaCount() >= 2
            && $resource != null
            && $resource->getQuantity() >= 2
            && $this->myrService->getNursesInWorkshopFromPlayer($player)->count() > 0;
    }

    private function getFoodResource(PlayerMYR $playerMYR) : ?PlayerResourceMYR
    {
        $pBoard = $playerMYR->getPersonalBoardMYR();

        $grass = $this->resourceMYRRepository->findOneBy(
            ["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]
        );
        return $this->playerResourceMYRRepository->findOneBy(
            [
                "resource" => $grass,
                "personalBoard" => $pBoard
            ]
        );
    }

    /**
     * craftNewNurse: craft a new nurse
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    private function craftNewNurse(PlayerMYR $player) : void
    {
        if (!$this->canBuyNurse($player)) {
            throw new Exception("The player can't buy a nurse, not enough resources");
        }
        $pBoard = $player->getPersonalBoardMYR();
        $pBoard->setLarvaCount($pBoard->getLarvaCount() - 2);

        $playerResource = $this->getFoodResource($player);
        $playerResource->setQuantity($playerResource->getQuantity() - 2);
        $this->entityManager->persist($playerResource);

        $nurse = new NurseMYR();
        $nurse->setArea(MyrmesParameters::BASE_AREA);
        $nurse->setPersonalBoardMYR($pBoard);
        $nurse->setAvailable(true);

        $pBoard->addNurse($nurse);

        $this->entityManager->persist($nurse);
        $this->entityManager->persist($pBoard);
    }

    /**
     * giveDirtToPlayer : gives a resource of dirt to the player
     * @param PlayerMYR $player
     * @return void
     */
    private function giveDirtToPlayer(PlayerMYR $player) : void
    {
        $dirt = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_DIRT]);
        $playerDirt = $this->playerResourceMYRRepository->findOneBy(
            ["resource" => $dirt, "personalBoard" => $player->getPersonalBoardMYR()]
        );
        if ($playerDirt != null) {
            $playerDirt->setQuantity($playerDirt->getQuantity() + 1);
            $this->entityManager->persist($playerDirt);
            $this->entityManager->flush();
        }
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
                    ->getQuantity() >= MyrmesParameters::GOAL_NEEDED_RESOURCES_FOOD_LEVEL_ONE,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_GRASS)
                    ->getQuantity() >= MyrmesParameters::GOAL_NEEDED_RESOURCES_FOOD_LEVEL_TWO,
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
                    ->getQuantity() >= MyrmesParameters::GOAL_NEEDED_RESOURCES_STONE_LEVEL_TWO,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_STONE)
                    ->getQuantity() >= MyrmesParameters::GOAL_NEEDED_RESOURCES_STONE_LEVEL_THREE,
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
                >= MyrmesParameters::GOAL_NEEDED_RESOURCES_STONE_OR_DIRT_LEVEL_ONE,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_STONE)
                    ->getQuantity()
                + $this->getPlayerResourcesFromSelectedType($player, MyrmesParameters::RESOURCE_TYPE_DIRT)
                    ->getQuantity()
                >= MyrmesParameters::GOAL_NEEDED_RESOURCES_STONE_OR_DIRT_LEVEL_THREE,
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
                $player->getPersonalBoardMYR()->getLarvaCount() >=
                MyrmesParameters::GOAL_NEEDED_RESOURCES_LARVAE_LEVEL_ONE,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $player->getPersonalBoardMYR()->getLarvaCount() >=
                MyrmesParameters::GOAL_NEEDED_RESOURCES_LARVAE_LEVEL_TWO,
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
                $player->getPreyMYRs()->count() >= MyrmesParameters::GOAL_NEEDED_RESOURCES_PREY_LEVEL_ONE,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $player->getPreyMYRs()->count() >= MyrmesParameters::GOAL_NEEDED_RESOURCES_PREY_LEVEL_TWO,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $player->getPreyMYRs()->count() >= MyrmesParameters::GOAL_NEEDED_RESOURCES_PREY_LEVEL_THREE,
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
                $player->getPersonalBoardMYR()->getWarriorsCount() >=
                MyrmesParameters::GOAL_NEEDED_RESOURCES_SOLDIER_LEVEL_ONE,
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
                $this->getSpecialTilesOfPlayer($player)->count() >=
                MyrmesParameters::GOAL_NEEDED_RESOURCES_NEEDED_SPECIAL_TILE_LEVEL_ONE,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $this->getSpecialTilesOfPlayer($player)->count() >=
                MyrmesParameters::GOAL_NEEDED_RESOURCES_NEEDED_SPECIAL_TILE_LEVEL_TWO,
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
                $nursesOfPlayer >= MyrmesParameters::GOAL_NEEDED_RESOURCES_NEEDED_NURSE_LEVEL_TWO
                && $nursesOfPlayer - $this->getNursesUsedInGoalFromPlayer($player)->count() >=
                    MyrmesParameters::GOAL_NEEDED_RESOURCES_REMOVED_NURSE_LEVEL_TWO,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $nursesOfPlayer >= MyrmesParameters::GOAL_NEEDED_RESOURCES_NEEDED_NURSE_LEVEL_THREE
                && $nursesOfPlayer - $this->getNursesUsedInGoalFromPlayer($player)->count() >=
                    MyrmesParameters::GOAL_NEEDED_RESOURCES_REMOVED_NURSE_LEVEL_THREE,
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
                $player->getPersonalBoardMYR()->getAnthillLevel() >=
                MyrmesParameters::GOAL_NEEDED_RESOURCES_NEEDED_ANTHILL_LEVEL_LEVEL_TWO,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $player->getPersonalBoardMYR()->getAnthillLevel() >=
                MyrmesParameters::GOAL_NEEDED_RESOURCES_NEEDED_ANTHILL_LEVEL_LEVEL_THREE,
            default => throw new Exception("Goal difficulty invalid for anthill level goal"),
        };
    }


    /**
     * getNumberOfResourcesFromSelectedType: return the number of resources of the player from the selected type
     * @param PlayerMYR $player
     * @param string $selectedType
     * @return PlayerResourceMYR|null
     */
    private function getPlayerResourcesFromSelectedType(PlayerMYR $player, string $selectedType) : ?PlayerResourceMYR
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
                $resource->setQuantity($resource->getQuantity() -
                    MyrmesParameters::GOAL_NEEDED_RESOURCES_FOOD_LEVEL_ONE
                ),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $resource->setQuantity($resource->getQuantity() -
                    MyrmesParameters::GOAL_NEEDED_RESOURCES_FOOD_LEVEL_TWO
                ),
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
            $resource->setQuantity($resource->getQuantity() -
                MyrmesParameters::GOAL_NEEDED_RESOURCES_STONE_LEVEL_TWO
            ),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
            $resource->setQuantity($resource->getQuantity() -
                MyrmesParameters::GOAL_NEEDED_RESOURCES_STONE_LEVEL_THREE
            ),
            default => throw new Exception("Goal difficulty invalid for stone goal"),
        };
        $this->entityManager->persist($resource);
        $this->entityManager->flush();
    }

    /**
     * retrieveResourceToDoStoneOrDirtGoal: retrieve the resource of the player to accomplish the stone or dirt goal
     * @param PlayerMYR $player
     * @param GoalMYR $goal
     * @param int $stoneQuantity
     * @param int $dirtQuantity
     * @return void
     * @throws Exception
     */
    private function retrieveResourceToDoStoneOrDirtGoal(PlayerMYR $player, GoalMYR $goal,
        int $stoneQuantity, int $dirtQuantity
    ): void
    {
        if (!$this->canPlayerDoStoneOrDirtGoal($player, $goal->getDifficulty())
            || ($goal->getDifficulty() == MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE
                && $stoneQuantity + $dirtQuantity < MyrmesParameters::GOAL_NEEDED_RESOURCES_STONE_OR_DIRT_LEVEL_ONE)
            || ($goal->getDifficulty() == MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE
                && $stoneQuantity + $dirtQuantity < MyrmesParameters::GOAL_NEEDED_RESOURCES_STONE_OR_DIRT_LEVEL_THREE)
        ) {
            throw new Exception('Player cannot do stone or dirt goal');
        }
        $resourceStone = $this->getPlayerResourcesFromSelectedType($player,
            MyrmesParameters::RESOURCE_TYPE_STONE
        );
        $resourceDirt = $this->getPlayerResourcesFromSelectedType($player,
            MyrmesParameters::RESOURCE_TYPE_DIRT
        );

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
            $player->getPersonalBoardMYR()->setLarvaCount($numberOfLarva -
                MyrmesParameters::GOAL_NEEDED_RESOURCES_LARVAE_LEVEL_ONE
            ),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
            $player->getPersonalBoardMYR()->setLarvaCount($numberOfLarva -
                MyrmesParameters::GOAL_NEEDED_RESOURCES_LARVAE_LEVEL_TWO
            ),
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
            $this->removeSelectedNumberOfPreyFromPlayer($player,
                MyrmesParameters::GOAL_NEEDED_RESOURCES_PREY_LEVEL_ONE
            ),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
            $this->removeSelectedNumberOfPreyFromPlayer($player,
                MyrmesParameters::GOAL_NEEDED_RESOURCES_PREY_LEVEL_TWO
            ),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
            $this->removeSelectedNumberOfPreyFromPlayer($player,
                MyrmesParameters::GOAL_NEEDED_RESOURCES_PREY_LEVEL_THREE
            ),
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
            $player->getPersonalBoardMYR()->setWarriorsCount($warriorsCount -
                MyrmesParameters::GOAL_NEEDED_RESOURCES_SOLDIER_LEVEL_ONE
            ),
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
            $this->removeSelectedNumberOfNursesFromPlayer($player,
                MyrmesParameters::GOAL_NEEDED_RESOURCES_REMOVED_NURSE_LEVEL_TWO
            ),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
            $this->removeSelectedNumberOfNursesFromPlayer($player,
                MyrmesParameters::GOAL_NEEDED_RESOURCES_REMOVED_NURSE_LEVEL_THREE
            ),
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
            $player->getPersonalBoardMYR()->setAnthillLevel($anthillLevel -
                MyrmesParameters::GOAL_NEEDED_RESOURCES_REMOVED_ANTHILL_LEVEL_LEVEL_TWO
            ),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
            $player->getPersonalBoardMYR()->setAnthillLevel($anthillLevel -
                MyrmesParameters::GOAL_NEEDED_RESOURCES_REMOVED_ANTHILL_LEVEL_LEVEL_THREE
            ),
            default => throw new Exception("Goal difficulty invalid for anthill level goal"),
        };
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
    }

    /**
     * retrieveResourceToDoSpecialTileGoal: retrieve the resources needed from the player to accomplish
     *                                         the special tile goal
     * @param PlayerMYR $player
     * @param GoalMYR $goal
     * @param Collection<PheromonMYR> $specialTiles
     * @return void
     * @throws Exception
     */
    private function retrieveResourceToDoSpecialTileGoal(
        PlayerMYR $player, GoalMYR $goal, Collection $specialTiles
    ): void
    {
        if (!$this->canPlayerDoSpecialTileGoal($player, $goal->getDifficulty())) {
            throw new Exception('Player cannot do special tiles goal');
        }
        switch($goal->getDifficulty()) {
            case MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE :
                if ($specialTiles->count()
                    != MyrmesParameters::GOAL_NEEDED_RESOURCES_REMOVED_SPECIAL_TILE_LEVEL_ONE)  {
                    throw new Exception("Invalid number of special tiles to do goal");
                }
                break;
            case MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO :
                if ($specialTiles->count()
                    != MyrmesParameters::GOAL_NEEDED_RESOURCES_REMOVED_SPECIAL_TILE_LEVEL_TWO)  {
                    throw new Exception("Invalid number of special tiles to do goal");
                }
                break;
            default:
        }
        foreach ($specialTiles as $tile) {
            $tile->setPlayer(null);
            $this->entityManager->persist($tile);
        }
        $this->entityManager->flush();
    }

    /**
     * retrieveResourceToDoPheromoneGoal: retrieve the resources needed from the player to accomplish
     *                                          the pheromone goal
     * @param PlayerMYR $player
     * @param GoalMYR $goal
     * @param Collection<PheromonMYR> $pheromones
     * @return void
     * @throws Exception if the pheromone are not connected or if the player has not selected enough pheromones
     */
    private function retrieveResourceToDoPheromoneGoal(PlayerMYR $player, GoalMYR $goal, Collection $pheromones): void
    {
        if (!$this->doesPlayerHaveSelectedEnoughPheromones($player, $goal->getDifficulty(), $pheromones)) {
            throw new Exception('Player cannot do pheromones goal, not enough selected pheromones');
        }
        if (!$this->arePheromoneConnected($pheromones)) {
            throw new Exception('Pheromones are not connected');
        }
        foreach ($pheromones as $pheromone) {
            foreach ($pheromone->getPheromonTiles() as $tile) {
                $tile->setPlayer(null);
                $this->entityManager->persist($tile);
            }
        }
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
        $game = $player->getGameMyr();
        foreach ($previousPlayers as $previousPlayer) {
            if (!$gameGoal->getGoalAlreadyDone()->contains($previousPlayer)) {
                $previousPlayer->setScore(
                    $previousPlayer->getScore()
                    + MyrmesParameters::SCORE_INCREASE_GOAL_ALREADY_DONE[
                        $game->getPlayers()->count()
                    ]
                );
                $this->entityManager->persist($previousPlayer);
            }
        }
        match ($gameGoal->getGoal()->getDifficulty()) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE =>
                $player->setScore($player->getScore()
                    + MyrmesParameters::SCORE_INCREASE_GOAL_DIFFICULTY_ONE),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO =>
                $player->setScore($player->getScore()
                    + MyrmesParameters::SCORE_INCREASE_GOAL_DIFFICULTY_TWO),
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE =>
                $player->setScore($player->getScore()
                    + MyrmesParameters::SCORE_INCREASE_GOAL_DIFFICULTY_THREE),
        };
        $this->entityManager->persist($player);

        $gameGoal->addPrecedentsPlayer($player);
        $gameGoal->addGoalAlreadyDone($player);
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

    /**
     * doesPlayerHaveSelectedEnoughPheromones: return true if the player have selected enough pheromones to do the goal
     *                                         if one of the pheromone is not a pheromone of the player, return false
     * @param PlayerMYR $player
     * @param ?int $difficulty
     * @param Collection<PheromonMYR> $pheromones
     * @return bool
     */
    private function doesPlayerHaveSelectedEnoughPheromones(
        PlayerMYR $player,
        ?int $difficulty,
        Collection $pheromones
    ) : bool {
        foreach ($pheromones as $pheromone) {
            if ($pheromone->getPlayer() !== $player) {
                return false;
            }
        }
        return match ($difficulty) {
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE => $pheromones->count() ==
                MyrmesParameters::GOAL_NEEDED_RESOURCES_NEEDED_PHEROMONE_LEVEL_ONE,
            MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE => $pheromones->count() ==
                MyrmesParameters::GOAL_NEEDED_RESOURCES_NEEDED_PHEROMONE_LEVEL_THREE,
        };
    }

    /**
     * arePheromoneConnected: return true if all the pheromones are connected
     * @param Collection<PheromonMYR> $pheromones
     * @return bool
     */
    private function arePheromoneConnected(Collection $pheromones) : bool
    {
        $tilesToVerify = new ArrayCollection();
        $firstPheromone = $pheromones->first();
        $pheromoneConnected = new ArrayCollection();
        $pheromoneConnected->add($firstPheromone);
        foreach ($firstPheromone->getPheromonTiles() as $tile) {
            $tilesToVerify->add($tile);
        }
        while ($tilesToVerify->count() > 0) {
            $tile = $tilesToVerify->first();
            $distinctPheromones = $this->getDistinctPheromonesAroundPheromoneTile($tile);
            $distinctPheromones->filter(function (PheromonMYR $pheromone) use ($pheromones) {
                return $pheromones->contains($pheromone);
            });
            foreach ($distinctPheromones as $distinctPheromone) {
                if ($pheromones->contains($distinctPheromone)) {
                    continue;
                }
                foreach ($distinctPheromone->getPheromonTiles() as $tile) {
                    $tilesToVerify->add($tile);
                }
            }
        }
        return $pheromones->forAll(function (PheromonMYR $pheromone) use ($pheromoneConnected) {
            return $pheromoneConnected->contains($pheromone);
        });
    }

    /**
     * getDistinctPheromonesAroundPheromoneTile: return the distinct pheromone around the selected pheromone tile
     * @param PheromonTileMYR $pheromoneTile
     * @return Collection<PheromonMYR>
     */
    private function getDistinctPheromonesAroundPheromoneTile(PheromonTileMYR $pheromoneTile) : Collection
    {
        $tile = $pheromoneTile->getTile();
        $game = $pheromoneTile->getMainBoard()->getGame();
        $distinctPheromones = new ArrayCollection();
        $x = $tile->getCoordX();
        $y = $tile->getCoordY();

        $tiles = new ArrayCollection();
        $tiles->add($this->tileMYRRepository->findOneBy(['coordX' => $x, 'coordY' => $y - 2]));
        $tiles->add($this->tileMYRRepository->findOneBy(['coordX' => $x, 'coordY' => $y + 2]));
        $tiles->add($this->tileMYRRepository->findOneBy(['coordX' => $x - 1, 'coordY' => $y - 1]));
        $tiles->add($this->tileMYRRepository->findOneBy(['coordX' => $x - 1, 'coordY' => $y + 1]));
        $tiles->add($this->tileMYRRepository->findOneBy(['coordX' => $x + 1, 'coordY' => $y - 1]));
        $tiles->add($this->tileMYRRepository->findOneBy(['coordX' => $x + 1, 'coordY' => $y + 1]));

        foreach ($tiles as $tile) {
            $pheromone = $this->getPheromoneFromTile($game, $tile);
            $this->addInCollectionOnlyIfDistinct($distinctPheromones, $pheromone);
        }
        return $distinctPheromones;
    }

    /**
     * addInCollectionOnlyIfDistinct: add in collection the object only if different from null and not already in
     * @param Collection<mixed> $collection
     * @param mixed $object
     * @return void
     */
    private function addInCollectionOnlyIfDistinct(Collection $collection, mixed $object): void
    {
        if ($object !== null && !$collection->contains($object)) {
            $collection->add($object);
        }
    }

    /**
     * getPheromoneFromTile: return the pheromone associated with the tile if it exists
     * @param GameMYR $game
     * @param TileMYR|null $tile
     * @return PheromonMYR|null
     */
    private function getPheromoneFromTile(GameMyr $game, ?TileMYR $tile) : ?PheromonMYR
    {
        if ($tile === null) {
            return null;
        }
        $pheromoneTile = $this->pheromoneTileMYRRepository->findOneBy(
            [
            'tile' => $tile,
                'myr' => $game->getMainBoardMYR(),
            ]
        );
        return $pheromoneTile->getPheromonMyr();
    }

    /**
     * hasPlayerAlreadyDoneSelectedGoal: return true if the player has already done the goal
     * @param PlayerMYR $player
     * @param GameGoalMYR $gameGoal
     * @return bool
     */
    private function hasPlayerAlreadyDoneSelectedGoal(PlayerMYR $player, GameGoalMYR $gameGoal) : bool
    {
        return $gameGoal->getPrecedentsPlayers()->exists(function (int $key, PlayerMYR $previousPlayer) use ($player) {
           return $previousPlayer === $player;
        });
    }
}
