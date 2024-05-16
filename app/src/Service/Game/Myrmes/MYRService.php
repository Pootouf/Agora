<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\DTO\Player;
use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\AnthillWorkerMYR;
use App\Entity\Game\Myrmes\GameGoalMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GoalMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Repository\Game\Myrmes\GoalMYRRepository;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\SeasonMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Repository\Game\Myrmes\TileTypeMYRRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class MYRService
{
    public function __construct(
        private PlayerMYRRepository $playerMYRRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly NurseMYRRepository $nurseMYRRepository,
        private readonly TileMYRRepository $tileMYRRepository,
        private readonly TileTypeMYRRepository $tileTypeMYRRepository,
        private readonly SeasonMYRRepository $seasonMYRRepository,
        private readonly GoalMYRRepository $goalMYRRepository,
        private readonly ResourceMYRRepository $resourceMYRRepository,
        private readonly PlayerResourceMYRRepository $playerResourceMYRRepository,
    ) {

    }

    /**
     * getNumberOfFreeWorkerOfPlayer: return the number of free worker of the player
     * @param PlayerMYR $player
     * @return int
     */
    public function getNumberOfFreeWorkerOfPlayer(PlayerMYR $player): int
    {
        return $player->getPersonalBoardMYR()->getAnthillWorkers()->filter(
            function (AnthillWorkerMYR $anthillWorkerMYR) {
                return $anthillWorkerMYR->getWorkFloor() == MyrmesParameters::NO_WORKFLOOR;
            }
        )->count();
    }

    /**
     * getNursesInWorkshopFromPlayer: return the nurses in the workshop area of the player
     * @param PlayerMYR $player
     * @return Collection<NurseMYR>
     */
    public function getNursesInWorkshopFromPlayer(PlayerMYR $player): Collection
    {
        return $player->getPersonalBoardMYR()->getNurses()->filter(
            function (NurseMYR $nurse) {
                return $nurse->getArea() == MyrmesParameters::WORKSHOP_AREA;
            }
        );
    }

    /**
     * @param int $type
     * @param int $orientation
     * @return TileTypeMYR|null
     */
    public function getTileTypeFromTypeAndOrientation(int $type, int $orientation): ?TileTypeMYR
    {
        return $this->tileTypeMYRRepository->findOneBy([
            "type" => $type, "orientation" => $orientation
        ]);
    }

    /**
     * getPlayerResourceAmount : returns the quantity of player's resource type
     * @param PlayerMYR $playerMYR
     * @param string $resourceName
     * @return int
     */
    public function getPlayerResourceAmount(PlayerMYR $playerMYR, string $resourceName): int
    {
        $resource = $this->resourceMYRRepository->findOneBy(["description" => $resourceName]);
        $playerResource = $this->playerResourceMYRRepository->findOneBy(
            ["resource" => $resource, "personalBoard" => $playerMYR->getPersonalBoardMYR()]
        );
        return $playerResource == null ? 0 : $playerResource->getQuantity();
    }

    /**
     * getAvailableLarvae : returns available player larvae
     * @param PlayerMYR $playerMYR
     * @return int
     */
    public function getAvailableLarvae(PlayerMYR $playerMYR): int
    {
        $personalBoard = $playerMYR->getPersonalBoardMYR();
        return $personalBoard->getLarvaCount() - $personalBoard->getSelectedEventLarvaeAmount();
    }

    /**
     * canOnePlayerDoWorkshopPhase : check if at least one player has nurses in workshop area
     * @param GameMYR $game
     * @return bool
     */
    public function canOnePlayerDoWorkshopPhase(GameMYR $game): bool
    {
        foreach ($game->getPlayers() as $player) {
            $personalBoard = $player->getPersonalBoardMYR();
            foreach($personalBoard->getNurses() as $nurse) {
                if($nurse->getArea() == MyrmesParameters::WORKSHOP_AREA) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * isInPhase : checks if player phase is equal to the phase
     * @param PlayerMYR $playerMYR
     * @param int $phase
     * @return bool
     */
    public function isInPhase(PlayerMYR $playerMYR, int $phase): bool
    {
        return $playerMYR->getPhase() == $phase;
    }

    /**
     * getPlayerFromNameAndGame : return the player associated with a username and a game
     * @param GameMYR $game
     * @param string  $name
     * @return ?PlayerMYR
     */
    public function getPlayerFromNameAndGame(GameMYR $game, string $name): ?PlayerMYR
    {
        return $this->playerMYRRepository->findOneBy(['gameMYR' => $game->getId(), 'username' => $name]);
    }

    /**
     * initializeNewGame: initialize the game in parameter
     * @param GameMYR $game
     * @return void
     */
    public function initializeNewGame(GameMYR $game): void
    {
        $this->initializeNewYear($game);

        $this->initializePreys($game);

        $this->initializeGoals($game);

        $i = 0;
        $anthillPositions = MyrmesParameters::ANTHILL_HOLE_POSITION_BY_NUMBER_OF_PLAYER[$game->getPlayers()->count()];
        $playersColors = MyrmesParameters::PLAYERS_COLORS;
        foreach ($game->getPlayers() as $player) {
            $color = $playersColors[$i];
            $this->initializePlayerData($player, $color);
            $anthillPosition = $anthillPositions[$i];
            $this->initializeAnthillHoleForPlayer($player, $anthillPosition);
            $i++;
        }

        $this->initializeEventBonus($game);
        $this->initializeMainBoardTiles($game);

        $game->setFirstPlayer($game->getPlayers()->first());

        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    /**
     * getPheromonesFromType : returns all orientations of a pheromone or special tile from a type
     * @param int $type
     * @return ArrayCollection<Int, TileTypeMYR>
     */
    public function getPheromonesFromType(int $type): ArrayCollection
    {
        return new ArrayCollection($this->tileTypeMYRRepository->findBy(
            ["type" => $type]
        ));
    }

    /**
     * getDiceResults : get dice results for all season
     * @param GameMYR $game
     * @return ArrayCollection<Int, Int>
     */
    public function getDiceResults(GameMYR $game): ArrayCollection
    {
        $result = new ArrayCollection();
        $mainBoard = $game->getMainBoardMYR();
        $fall = $this->seasonMYRRepository->findOneBy(
            ["mainBoard" => $mainBoard, "name" => MyrmesParameters::FALL_SEASON_NAME]
        );
        $result[$fall->getName()] = $fall->getDiceResult();
        $spring = $this->seasonMYRRepository->findOneBy(
            ["mainBoard" => $mainBoard, "name" => MyrmesParameters::SPRING_SEASON_NAME]
        );
        $result[$spring->getName()] = $spring->getDiceResult();
        $summer = $this->seasonMYRRepository->findOneBy(
            ["mainBoard" => $mainBoard, "name" => MyrmesParameters::SUMMER_SEASON_NAME]
        );
        $result[$summer->getName()] = $summer->getDiceResult();
        return $result;
    }

    /**
     * getActualSeason : returns the actual season
     *
     * @param GameMYR $game
     * @return SeasonMYR|null
     */
    public function getActualSeason(GameMYR $game): ?SeasonMYR
    {
        foreach ($game->getMainBoardMYR()->getSeasons() as $season) {
            if ($season->isActualSeason()) {
                return $season;
            }
        }
        return null;
    }

    /**
     * getPlayerResourceOfType : return player resource associate with the type
     * @param PlayerMYR $player
     * @param string $type
     * @return PlayerResourceMYR|null
     */
    public function getPlayerResourceOfType(PlayerMYR $player, string $type): ?PlayerResourceMYR
    {
        $personalBoard = $player->getPersonalBoardMYR();

        foreach ($personalBoard->getPlayerResourceMYRs() as $playerResource) {
            if ($playerResource->getResource()->getDescription() === $type) {
                return $playerResource;
            }
        }

        return null;
    }

    /**
     * isGameEnded : returns true if the game reached its end
     * @param GameMYR $game
     * @return bool
     */
    public function isGameEnded(GameMYR $game): bool
    {
        return $game->getMainBoardMYR()->getYearNum() > MyrmesParameters::THIRD_YEAR_NUM;
    }

    /**
     * getNursesAtPosition : return nurses which is in $position
     * @param PlayerMYR $player
     * @param int $position
     * @return ArrayCollection
     */
    public function getNursesAtPosition(PlayerMYR $player, int $position): ArrayCollection
    {
        $nurses =  $this->nurseMYRRepository->findBy(["area" => $position,
            "personalBoardMYR" => $player->getPersonalBoardMYR()]);
        return new ArrayCollection($nurses);
    }

    /**
     * getActualPlayer : return the player who need to play
     * @param GameMYR $game
     * @return PlayerMYR|null
     */
    public function getActualPlayer(GameMYR $game): ?PlayerMYR
    {
        $result = $game->getPlayers()->filter(
            function (PlayerMYR $player) {
                return $player->isTurnOfPlayer();
            }
        )->first();
        return !$result ? null : $result ;
    }

    /**
     * manageNursesAfterBonusGive : Replace all nurses that have been used
     * @param PlayerMYR $player
     * @param int $nurseCount
     * @param int $positionOfNurse
     * @return void
     * @throws Exception
     */
    public function manageNursesAfterBonusGive(PlayerMYR $player, int $nurseCount, int $positionOfNurse): void
    {
        if ($nurseCount > 0) {
            $nurses = $this->getNursesAtPosition($player, $positionOfNurse);
            foreach ($nurses as $n) {
                if ($nurseCount == 0) {
                    return;
                }
                switch ($positionOfNurse) {
                    case MyrmesParameters::LARVAE_AREA:
                    case MyrmesParameters::SOLDIERS_AREA:
                    case MyrmesParameters::WORKER_AREA:
                    case MyrmesParameters::WORKSHOP_AREA:
                        $n->setArea(MyrmesParameters::BASE_AREA);
                        $this->entityManager->persist($n);
                        break;
                    default:
                        throw new Exception("Don't manage bonus");
                }
                $nurseCount--;
            }
        }
        $this->entityManager->flush();
    }

    /**
     * doGameGoal : Activate a game goal for the player, retrieve the resources associated and gives the points
     * @param PlayerMYR $playerMYR
     * @param GameGoalMYR $goalMYR
     * @return void
     * @throws Exception
     */
    public function doGameGoal(PlayerMYR $playerMYR, GameGoalMYR $goalMYR): void
    {
        if(!$this->canDoGoal($playerMYR, $goalMYR)) {
            throw new Exception("Player can't do goal");
        }
        // TODO : COMPUTE GOAL COSTS
        $this->computePlayerRewardPointsWithGoal($playerMYR, $goalMYR);
    }

    /**
     * setPhase: Set a new phase of the game for a player, and change the game phase if all player have the same
     * @param PlayerMYR $playerMYR
     * @param int $phase
     * @return void
     */
    public function setPhase(PlayerMYR $playerMYR, int $phase): void
    {
        $playerMYR->setPhase($phase);
        $areAllPlayerAtTheSamePhase = true;
        foreach($playerMYR->getGameMyr()->getPlayers() as $player) {
            if($player->getPhase() != $phase) {
                $areAllPlayerAtTheSamePhase = false;
            }
        }
        if($areAllPlayerAtTheSamePhase) {
            $playerMYR->getGameMyr()->setGamePhase($phase);
            $this->entityManager->persist($playerMYR->getGameMyr());

            if ($phase == MyrmesParameters::PHASE_EVENT) {
                $this->manageEndOfRound($playerMYR->getGameMyr());
            }

            $players = $playerMYR->getGameMyr()->getPlayers();
            $this->manageTurnOfPlayerNewPhase($players, $phase);
        }
        $this->entityManager->persist($playerMYR);
        $this->entityManager->flush();
    }

    /**
     * setNextPlayerTurn: Set the turn of play to the next player (for sequential phase)
     *                    Do nothing if it's the last player to play of the phase
     *                    Make the loop turn until no worker left in worker phase
     * @param PlayerMYR $actualPlayer
     * @return void
     */
    public function setNextPlayerTurn(PlayerMYR $actualPlayer): void
    {
        $game = $actualPlayer->getGameMyr();
        $actualPlayer->setTurnOfPlayer(false);
        $this->entityManager->persist($actualPlayer);
        $this->entityManager->flush();

        $isInWorkerPhase = $game->getGamePhase() == MyrmesParameters::PHASE_WORKER;
        if ($isInWorkerPhase && !$this->canPlayersStillDoWorkerPhase($game)) {
            return;
        }

        $isInWorkshopPhase = $game->getGamePhase() == MyrmesParameters::PHASE_WORKSHOP;
        if ($isInWorkshopPhase && !$this->canOnePlayerDoWorkshopPhase($game)) {
            return;
        }

        $players = $this->getOrderOfPlayers($actualPlayer->getGameMyr());
        $isActualPlayerFound = false;
        foreach ($players as $player) {
            if ($isActualPlayerFound) {
                if ($isInWorkerPhase && $this->getNumberOfFreeWorkerOfPlayer($player) <= 0) {
                    continue;
                }
                if ($isInWorkshopPhase && $this->getNursesInWorkshopFromPlayer($player)->count() <= 0) {
                    continue;
                }
                $player->setTurnOfPlayer(true);
                $this->entityManager->persist($player);
                $isActualPlayerFound = false;
                break;
            }
            $isActualPlayerFound = $actualPlayer === $player;
        }
        if ($isInWorkerPhase && $isActualPlayerFound) {
            // True if the actual player is the last player
            // or if no player can play after him
            // (needs to loop in worker phase)
            foreach ($players as $player) {
                if ($this->getNumberOfFreeWorkerOfPlayer($player) <= 0) {
                    continue;
                }
                $player->setTurnOfPlayer(true);
                $this->entityManager->persist($player);
                break;
            }
        }
        $this->entityManager->flush();
    }

    /**
     * endPlayerRound: end the turn of the player (for parallel phase)
     * @param PlayerMYR $player
     * @return void
     */
    public function endPlayerRound(PlayerMYR $player): void
    {
        $player->setTurnOfPlayer(false);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * canManageEndOfPhase : indicate if all players have played this phase and are waiting for the next one
     * @param GameMYR $gameMYR
     * @param int $phase
     * @return bool
     */
    public function canManageEndOfPhase(GameMYR $gameMYR, int $phase): bool
    {
        foreach ($gameMYR->getPlayers() as $player) {
            if($player->getPhase() == $phase) {
                return false;
            }
        }
        return true;
    }

    /**
     * manageEndOfRound : does all actions concerning the end of a round
     * @param GameMYR $game
     * @return void
     */
    public function manageEndOfRound(GameMYR $game): void
    {
        $players = $game->getPlayers();
        foreach ($players as $player) {
            $this->discardLarvae($player);
            $this->replaceWorkers($player);
            $this->replaceNurses($player);
            $this->resetWorkshopActions($player);
            $this->makePheromonesHarvestable($player);
            $this->entityManager->persist($player);
        }
        $this->endRoundOfFirstPlayer($game);
        $this->endSeason($game);
        $this->resetGameGoalsDoneDuringTheRound($game);
    }

    /**
     * exchangeLarvaeForFood : player can exchange 3 larvae for 1 food resource
     * @param PlayerMYR $player
     * @return void
     */
    public function exchangeLarvaeForFood(PlayerMYR $player): void
    {
        $larvaeAvailable = $this->getAvailableLarvae($player);
        $personalBoard = $player->getPersonalBoardMYR();
        if ($larvaeAvailable >= 3) {
            $larvaCount = $personalBoard->getLarvaCount();
            $personalBoard->setLarvaCount($larvaCount - 3);
            $food = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
            $playerFood = $this->playerResourceMYRRepository->findOneBy(
                ["resource" => $food, "personalBoard" => $personalBoard]
            );
            $playerFood->setQuantity($playerFood->getQuantity() + 1);
            $this->entityManager->persist($playerFood);
            $this->entityManager->persist($personalBoard);
            $this->entityManager->flush();
        }
    }

    /**
     * initializeNewSeason: initialize in the DB a new season with the selected name
     * @param GameMYR $game
     * @param string $seasonName
     * @return void
     */
    private function initializeNewSeason(GameMYR $game, string $seasonName): void
    {
        $season = new SeasonMYR();
        $mainBoard = $game->getMainBoardMYR();
        $season->setMainBoard($mainBoard);
        $season->setName($seasonName);
        $season->setDiceResult(rand(1, 6));
        $season->setActualSeason(false);
        $mainBoard->addSeason($season);
        $this->entityManager->persist($mainBoard);
        $this->entityManager->persist($season);
        $this->entityManager->flush();
    }

    /**
     * resetGameGoalsDoneDuringTheRound : at the end of the round, for each game goal,
     *  clears the list of players who've accomplished an objective during the round
     *
     * @param GameMYR $game
     * @return void
     */
    private function resetGameGoalsDoneDuringTheRound(GameMYR $game): void
    {
        $mainBoard = $game->getMainBoardMYR();
        foreach ($mainBoard->getGameGoalsLevelOne() as $gameGoal) {
            $gameGoal->getGoalAlreadyDone()->clear();
            $this->entityManager->persist($gameGoal);
        }
        foreach ($mainBoard->getGameGoalsLevelTwo() as $gameGoal) {
            $gameGoal->getGoalAlreadyDone()->clear();
            $this->entityManager->persist($gameGoal);
        }
        foreach ($mainBoard->getGameGoalsLevelThree() as $gameGoal) {
            $gameGoal->getGoalAlreadyDone()->clear();
            $this->entityManager->persist($gameGoal);
        }
        $this->entityManager->flush();
    }

    /**
     * discardLarvae : removes all selected larvae from a player
     * @param PlayerMYR $player
     * @return void
     */
    private function discardLarvae(PlayerMYR $player): void
    {
        $selectedLarvae = $player->getPersonalBoardMYR()->getSelectedEventLarvaeAmount();
        $playerLarvae = $player->getPersonalBoardMYR()->getLarvaCount();
        $player->getPersonalBoardMYR()->setLarvaCount($playerLarvae - $selectedLarvae);
        $player->getPersonalBoardMYR()->setSelectedEventLarvaeAmount(0);
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
    }

    /**
     * replaceWorkers : each worker of the player is put back into worker area
     * @param PlayerMYR $player
     * @return void
     */
    private function replaceWorkers(PlayerMYR $player): void
    {
        $anthillWorkers = $player->getPersonalBoardMYR()->getAnthillWorkers();
        foreach ($anthillWorkers as $worker) {
            $worker->setWorkFloor(MyrmesParameters::NO_WORKFLOOR);
            $this->entityManager->persist($worker);
        }
        $this->entityManager->flush();
    }

    /**
     * replaceNurses : each nurse of the player is put back into nurse area,
     *      except if it's used to accomplish an objective.
     * @param PlayerMYR $player
     * @return void
     */
    private function replaceNurses(PlayerMYR $player): void
    {
        $nurses = $player->getPersonalBoardMYR()->getNurses();
        foreach ($nurses as $nurse) {
            $nurse->setArea(MyrmesParameters::BASE_AREA);
        }
        $this->entityManager->flush();
    }

    /**
     * initializePreys: initialize random preys on the main board of the game
     * @param GameMYR $game
     * @return void
     */
    private function initializePreys(GameMYR $game): void
    {
        $positions = MyrmesParameters::PREY_POSITIONS;
        shuffle($positions);
        $count = 0;
        for ($i = 0; $i < MyrmesParameters::LADYBUG_NUMBER; $i++) {
            $this->initializePrey($game, MyrmesParameters::LADYBUG_TYPE, $positions[$i]);
        }
        $count += MyrmesParameters::LADYBUG_NUMBER;

        for ($i = $count; $i < $count + MyrmesParameters::TERMITE_NUMBER; $i++) {
            $this->initializePrey($game, MyrmesParameters::TERMITE_TYPE, $positions[$i]);
        }
        $count += MyrmesParameters::TERMITE_NUMBER;

        for ($i = $count; $i < $count + MyrmesParameters::SPIDER_NUMBER; $i++) {
            $this->initializePrey($game, MyrmesParameters::SPIDER_TYPE, $positions[$i]);
        }

        $this->entityManager->flush();
    }


    /**
     * initializePrey: initialize one prey of the game
     * @param GameMYR $game
     * @param string $type
     * @param array $position
     * @return void
     */
    private function initializePrey(GameMYR $game, string $type, array $position): void
    {
        $prey = new PreyMYR();
        $prey->setType($type);
        $tile = $this->tileMYRRepository->findOneBy(['coordX' => $position[0], 'coordY' => $position[1]]);
        $prey->setTile($tile);
        $game->getMainBoardMYR()->addPrey($prey);
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->persist($prey);
    }

    /**
     * initializePlayerData: initialize the data of the player at the start of the game
     * @param PlayerMYR $player
     * @param string $color
     * @return void
     */
    private function initializePlayerData(PlayerMYR $player, string $color): void
    {
        $personalBoard = $player->getPersonalBoardMYR();
        for ($i = 0; $i < MyrmesParameters::START_NURSES_COUNT_PER_PLAYER; $i++) {
            $this->initializeNurse($player);
        }
        for ($i = 0; $i < MyrmesParameters::NUMBER_OF_WORKER_AT_START; $i++) {
            $this->initializeWorker($player);
        }
        $personalBoard->setLarvaCount(MyrmesParameters::NUMBER_OF_LARVAE_AT_START);
        $personalBoard->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $player->setScore(MyrmesParameters::PLAYER_START_SCORE);
        $player->setRemainingHarvestingBonus(0);
        $this->initializePlayerWorkshopActions($player);
        $this->initializeColorForPlayer($player, $color);
        $this->initializePlayerResources($player);
        $player->setPhase(MyrmesParameters::PHASE_EVENT);
        $player->setTurnOfPlayer(true);
        $this->entityManager->persist($player);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }

    /**
     * initializePlayerWorkshopActions: set for all area in workshop value 0
     * @param PlayerMYR $player
     * @return void
     */
    private function initializePlayerWorkshopActions(PlayerMYR $player): void
    {
        $actions = $player->getWorkshopActions();
        for($i = MyrmesParameters::WORKSHOP_GOAL_AREA; $i <= MyrmesParameters::WORKSHOP_NURSE_AREA; $i++) {
            $actions[$i] = 0;
        }
        $player->setWorkshopActions($actions);
    }

    /**
     * initializePlayerResources: initialize with 0 quantity the player resources
     * @param PlayerMYR $player
     * @return void
     */
    private function initializePlayerResources(PlayerMYR $player): void
    {
        foreach ($this->resourceMYRRepository->findAll() as $resource) {
            $playerResource = new PlayerResourceMYR();
            $playerResource->setResource($resource);
            $playerResource->setQuantity(0);
            $player->getPersonalBoardMYR()
                ->addPlayerResourceMYR($playerResource);
            $this->entityManager->persist($playerResource);
            $this->entityManager->persist($player->getPersonalBoardMYR());
        }
    }

    /**
     * initializeNurse: initialize a nurse for the selected player
     * @param PlayerMYR $player
     * @return void
     */
    private function initializeNurse(PlayerMYR $player): void
    {
        $nurse = new NurseMYR();
        $nurse->setArea(MyrmesParameters::BASE_AREA);
        $nurse->setAvailable(true);
        $player->getPersonalBoardMYR()->addNurse($nurse);
        $this->entityManager->persist($nurse);
        $this->entityManager->persist($player->getPersonalBoardMYR());
    }

    /**
     * initializeWorker: initialize a new worker for the player
     * @param PlayerMYR $player
     * @return void
     */
    private function initializeWorker(PlayerMYR $player): void
    {
        $worker = new AnthillWorkerMYR();
        $worker->setWorkFloor(MyrmesParameters::NO_WORKFLOOR);
        $player->getPersonalBoardMYR()->addAnthillWorker($worker);
        $this->entityManager->persist($worker);
        $this->entityManager->persist($player->getPersonalBoardMYR());
    }

    /**
     * initializeAnthillHoleForPlayer: create and initialize a new anthill hole for the player at the selected position
     * @param PlayerMYR $player
     * @param array $position
     * @return void
     */
    private function initializeAnthillHoleForPlayer(PlayerMYR $player, array $position): void
    {
        $tile = $this->tileMYRRepository->findOneBy(['coordX' => $position[0], 'coordY' => $position[1]]);
        $hole = new AnthillHoleMYR();
        $hole->setPlayer($player);
        $hole->setTile($tile);
        $player->getGameMyr()->getMainBoardMYR()->addAnthillHole($hole);
        $this->entityManager->persist($hole);
        $player->addAnthillHoleMYR($hole);
        $this->entityManager->persist($player);
        $this->entityManager->persist($player->getGameMyr()->getMainBoardMYR());
    }

    /**
     * initializeColorForPlayer : set the player's color
     * @param PlayerMYR $player
     * @param string $color
     * @return void
     */
    private function initializeColorForPlayer(PlayerMYR $player, string $color): void
    {
        $player->setColor($color);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }


    /**
     * initializeEventBonus: initialize the bonus of season for each of the player of the game
     * @param GameMYR $game
     * @return void
     */
    private function initializeEventBonus(GameMYR $game): void
    {
        foreach ($game->getPlayers() as $player) {
            $player->getPersonalBoardMYR()->setBonus($this->getActualSeason($game)->getDiceResult());
            $this->entityManager->persist($player->getPersonalBoardMYR());
        }
        $this->entityManager->flush();
    }


    /**
     * initializeMainBoardTiles: initialize the tiles of the mainBoard based on the number of players of the game
     * @param GameMYR $game
     * @return void
     */
    private function initializeMainBoardTiles(GameMYR $game): void
    {
        $numberOfPlayers = $game->getPlayers()->count();
        $tiles = $this->tileMYRRepository->findAll();
        switch ($numberOfPlayers) {
            case 4:
                break;
            case 3:
                $excludedTiles = MyrmesParameters::EXCLUDED_TILES_2_PLAYERS;
                $tiles = array_filter($tiles, function (TileMYR $tile) use ($excludedTiles) {
                    return $tile->getCoordX() >= 2
                        && !in_array([$tile->getCoordX(), $tile->getCoordY()], $excludedTiles);
                });
                break;
            case 2:
                $tiles = array_filter($tiles, function (TileMYR $tile) {
                    return $tile->getCoordX() >= 7;
                });
                break;
            default:
        }
        foreach ($tiles as $tile) {
            $game->getMainBoardMYR()->addTile($tile);
        }
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->flush();
    }

    /**
     * manageTurnOfPlayerNewPhase: modify the turn of player for the players of the game depending on the phase
     * @param Collection<PlayerMYR> $players
     * @param int $phase
     * @return void
     */
    private function manageTurnOfPlayerNewPhase(Collection $players, int $phase): void
    {
        switch ($phase) {
            case MyrmesParameters::PHASE_HARVEST :
                $this->setPheromonesHarvestedIfNoResourcesOnIt($players);
                // no break because harvest is also a parallel phase
            case MyrmesParameters::PHASE_EVENT :
            case MyrmesParameters::PHASE_BIRTH :
            case MyrmesParameters::PHASE_WINTER :
                foreach ($players as $player) {
                    $player->setTurnOfPlayer(true);
                    $this->entityManager->persist($player);
                }
                break;
            case MyrmesParameters::PHASE_WORKER :
                $firstPlayer = $players->first()->getGameMyr()->getFirstPlayer();
                $firstPlayer->setTurnOfPlayer(true);
                $this->entityManager->persist($firstPlayer);
                break;
            case MyrmesParameters::PHASE_WORKSHOP :
                $player = $players->first()->getGameMyr()->getFirstPlayer();
                $player->setTurnOfPlayer(true);
                $this->entityManager->persist($player);
                while ($this->getNursesInWorkshopFromPlayer($player)->count() <= 0) {
                    $this->setNextPlayerTurn($player);
                    $player = $player->getGameMyr()->getPlayers()->filter(
                        function (PlayerMYR $player) {
                            return $player->isTurnOfPlayer();
                        }
                    )->first();
                }
                break;
            default:
        }
        $this->entityManager->flush();
    }

    /**
     * getOrderOfPlayers: return the player of the game in the order of turn of play
     * @param GameMYR $gameMYR
     * @return Collection<PlayerMYR>
     */
    private function getOrderOfPlayers(GameMYR $gameMYR): Collection
    {
        $firstPlayer = $gameMYR->getFirstPlayer();
        $isFirstPlayerManaged = false;
        $queue = [];
        $head = [];
        foreach ($gameMYR->getPlayers() as $player) {
            if ($firstPlayer === $player) {
                $isFirstPlayerManaged = true;
            }
            if (!$isFirstPlayerManaged) {
                $queue[] = $player;
            } else {
                $head[] = $player;
            }
        }
        return new ArrayCollection(array_merge($head, $queue));
    }

    /**
     * canDoGoal : returns true if player do not have done the objective yet,
     *      and have done at least an objective of inferior level
     * @param PlayerMYR $playerMYR
     * @param GameGoalMYR $goalMYR
     * @return bool
     */
    private function canDoGoal(PlayerMYR $playerMYR, GameGoalMYR $goalMYR): bool
    {
        $playerGameGoals = $playerMYR->getGameGoalMYRs();
        if($playerGameGoals->contains($goalMYR)) {
            return false;
        }
        if($goalMYR->getGoal()->getDifficulty() == MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE) {
            return true;
        }
        foreach ($playerGameGoals as $playerGameGoal) {
            if($playerGameGoal->getGoal()->getDifficulty() == $goalMYR->getGoal()->getDifficulty()
                || $playerGameGoal->getGoal()->getDifficulty() == $goalMYR->getGoal()->getDifficulty() - 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * computePlayerRewardPointsWithGoal : Computes and gives Player points related to the goal
     * @param PlayerMYR $playerMYR
     * @param GameGoalMYR $goalMYR
     * @return void
     */
    private function computePlayerRewardPointsWithGoal(PlayerMYR $playerMYR, GameGoalMYR $goalMYR): void
    {
        $precedentPlayers = $goalMYR->getPrecedentsPlayers();
        foreach ($precedentPlayers as $player) {
            $player->setScore($player->getScore() +
                MyrmesParameters::GOAL_REWARD_WHEN_GOAL_ALREADY_DONE);
            $this->entityManager->persist($player);
        }
        switch ($goalMYR->getGoal()->getDifficulty()) {
            case MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE :
                $playerMYR->setScore($playerMYR->getScore() + MyrmesParameters::GOAL_REWARD_LEVEL_ONE);
                break;
            case MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO :
                $playerMYR->setScore($playerMYR->getScore() + MyrmesParameters::GOAL_REWARD_LEVEL_TWO);
                break;
            case MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE :
                $playerMYR->setScore($playerMYR->getScore() + MyrmesParameters::GOAL_REWARD_LEVEL_THREE);
                break;
            default:
        }
        $goalMYR->addPrecedentsPlayer($playerMYR);
        $this->entityManager->persist($playerMYR);
        $this->entityManager->flush();
    }

    /**
     * endRoundOfFirstPlayer : first player role is now given to the next player
     * @param GameMYR $game
     * @return void
     */
    private function endRoundOfFirstPlayer(GameMYR $game): void
    {
        $players = $game->getPlayers();
        $firstPlayer = $game->getFirstPlayer();
        $nbOfPlayers = $players->count();
        $index = 0;
        for ($i = 0; $i < $nbOfPlayers; ++$i) {
            if ($players->get($i) === $firstPlayer) {
                $index = $i;
                break;
            }
        }
        $nextPlayer = $players->get($index % $nbOfPlayers);
        $game->setFirstPlayer($nextPlayer);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    /**
     * endSeason : ends actual season of the game, if needed ends the actual year,
     * @param GameMYR $game
     * @return void
     */
    private function endSeason(GameMYR $game): void
    {
        $mainBoard = $game->getMainBoardMYR();
        if ($game->getGamePhase() == MyrmesParameters::PHASE_WINTER) {
            $this->initializeNewYear($game);
            $this->initializeEventBonus($game);
            $this->entityManager->persist($game);
            return;
        }
        $actualSeason = $this->getActualSeason($game);
        $fall = $this->seasonMYRRepository->findOneBy(
            [
                "mainBoard" => $mainBoard,
                "name" => MyrmesParameters::FALL_SEASON_NAME
            ]
        );
        $summer = $this->seasonMYRRepository->findOneBy(
            [
                "mainBoard" => $mainBoard,
                "name" => MyrmesParameters::SUMMER_SEASON_NAME
            ]
        );
        $actualSeason->setActualSeason(false);
        $this->entityManager->persist($actualSeason);
        if ($actualSeason->getName() === MyrmesParameters::SPRING_SEASON_NAME) {
            $summer->setActualSeason(true);
            $this->entityManager->persist($summer);
        } elseif ($actualSeason->getName() === MyrmesParameters::SUMMER_SEASON_NAME) {
            $fall->setActualSeason(true);
            $this->entityManager->persist($fall);
        }
        $this->initializeEventBonus($game);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    /**
     * initializeNewYear : initializes a new year
     * @param GameMYR $game
     * @return void
     */
    private function initializeNewYear(GameMYR $game): void
    {
        $this->clearSeasons($game);
        $yearNum = $game->getMainBoardMYR()->getYearNum();
        $yearNum += 1;
        $game->getMainBoardMYR()->setYearNum($yearNum);
        $this->entityManager->persist($game->getMainBoardMYR());
        if ($yearNum > MyrmesParameters::THIRD_YEAR_NUM) {
            $this->entityManager->flush();
            return;
        }
        $this->initializeNewSeason($game, MyrmesParameters::SPRING_SEASON_NAME);
        $this->initializeNewSeason($game, MyrmesParameters::SUMMER_SEASON_NAME);
        $this->initializeNewSeason($game, MyrmesParameters::FALL_SEASON_NAME);
        $spring = $game->getMainBoardMYR()->getSeasons()->first();
        $spring->setActualSeason(true);
        $this->entityManager->persist($spring);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    /**
     * initializeGoals: initialize random goals for the game
     * @param GameMYR $game
     * @return void
     */
    private function initializeGoals(GameMYR $game): void
    {
        $goalsLevelOne = $this->goalMYRRepository->findBy([
            'difficulty' => MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE
        ]);
        $goalsLevelTwo = $this->goalMYRRepository->findBy([
            'difficulty' => MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO
        ]);
        $goalsLevelThree = $this->goalMYRRepository->findBy([
            'difficulty' => MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE
        ]);
        shuffle($goalsLevelOne);
        shuffle($goalsLevelTwo);
        shuffle($goalsLevelThree);

        $this->createGameGoal($goalsLevelOne[0], $game->getMainBoardMYR());
        $this->createGameGoal($goalsLevelOne[1], $game->getMainBoardMYR());
        $this->createGameGoal($goalsLevelTwo[0], $game->getMainBoardMYR());
        $this->createGameGoal($goalsLevelTwo[1], $game->getMainBoardMYR());
        $this->createGameGoal($goalsLevelThree[0], $game->getMainBoardMYR());
        $this->createGameGoal($goalsLevelThree[1], $game->getMainBoardMYR());

        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->flush();
    }

    /**
     * createGameGoal: create game goal entity
     * @param GoalMYR $goal
     * @param MainBoardMYR $mainBoard
     * @return void
     */
    private function createGameGoal(GoalMYR $goal, MainBoardMYR $mainBoard): void
    {
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        switch ($goal->getDifficulty()) {
            case MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE:
                $gameGoal->setMainBoardLevelOne($mainBoard);
                $mainBoard->addGameGoalsLevelOne($gameGoal);
                break;
            case MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO:
                $gameGoal->setMainBoardLevelTwo($mainBoard);
                $mainBoard->addGameGoalsLevelTwo($gameGoal);
                break;
            case MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE:
                $gameGoal->setMainBoardLevelThree($mainBoard);
                $mainBoard->addGameGoalsLevelThree($gameGoal);
                break;
            default:
        }
        $this->entityManager->persist($gameGoal);
        $this->entityManager->flush();

    }

    /**
     * clearSeasons : clear all seasons after new Year
     *
     * @param GameMYR $game
     * @return void
     */
    private function clearSeasons(GameMYR $game): void
    {
        $seasons = $game->getMainBoardMYR()->getSeasons();
        foreach ($seasons as $season) {
            $game->getMainBoardMYR()->removeSeason($season);
        }
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->flush();
    }

    private function resetWorkshopActions(PlayerMYR $player): void
    {
        $playerActions = array();
        for($j = MyrmesParameters::WORKSHOP_GOAL_AREA; $j <= MyrmesParameters::WORKSHOP_NURSE_AREA; $j += 1) {
            $playerActions[$j] = 0;
        }
        $player->setWorkshopActions($playerActions);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * canPlayersStillDoWorkerPhase: return true if at least one player can do the worker phase (still have at least
     *                               one worker ant)
     * @param GameMYR $game
     * @return bool
     */
    private function canPlayersStillDoWorkerPhase(GameMYR $game): bool
    {
        return $game->getPlayers()->exists(
            function (int $key, PlayerMYR $player) {
                return $this->getNumberOfFreeWorkerOfPlayer($player) > 0;
            }
        );
    }

    /**
     * setPheromonesHarvestedIfNoResourcesOnIt: get all the pheromones of the players and set them as harvested if
     *                                          there is no resources on them
     * @param Collection<PlayerMYR> $players
     * @return void
     */
    private function setPheromonesHarvestedIfNoResourcesOnIt(Collection $players): void
    {
        foreach ($players as $player) {
            foreach ($player->getPheromonMYRs() as $pheromone) {
                if ($pheromone->getPheromonTiles()->forAll(
                    function (int $key, PheromonTileMYR $pheromonTile) {
                        return $pheromonTile->getResource() == null;
                    }
                )) {
                    if ($pheromone->getType()->getType() != MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY) {
                        $pheromone->setHarvested(true);
                        $this->entityManager->persist($pheromone);
                    }
                }
            }
        }
    }

    /**
     * makePheromonesHarvestable: make all the pheromones of the player harvestable
     * @param PlayerMYR $player
     * @return void
     */
    private function makePheromonesHarvestable(PlayerMYR $player): void
    {
        foreach ($player->getPheromonMYRs() as $pheromone) {
            if ($pheromone->getType()->getType() !== MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL &&
                $pheromone->getType()->getType() !== MyrmesParameters::SPECIAL_TILE_TYPE_FARM) {
                $pheromone->setHarvested(false);
                $this->entityManager->persist($pheromone);
            }
        }
    }

}
