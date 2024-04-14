<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\AnthillWorkerMYR;
use App\Entity\Game\Myrmes\GameGoalMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GoalMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
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
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class MYRService
{

    public function __construct(private PlayerMYRRepository $playerMYRRepository,
                private readonly EntityManagerInterface $entityManager,
                private readonly NurseMYRRepository $nurseMYRRepository,
                private readonly TileMYRRepository $tileMYRRepository,
                private readonly TileTypeMYRRepository $tileTypeMYRRepository,
                private readonly SeasonMYRRepository $seasonMYRRepository,
                private readonly GoalMYRRepository $goalMYRRepository,
                private readonly ResourceMYRRepository $resourceMYRRepository,
                private readonly PlayerResourceMYRRepository $playerResourceMYRRepository)
    {

    }

    /**
     * getPlayerResourceAmount : returns the quantity of player's resource type
     * @param PlayerMYR $playerMYR
     * @param string $resourceName
     * @return int
     */
    public function getPlayerResourceAmount(PlayerMYR $playerMYR, string $resourceName): int
    {
        $resource = $this->resourceMYRRepository->findOneBy(["description"=>$resourceName]);
        $playerResource = $this->playerResourceMYRRepository->findOneBy(["resource"=>$resource]);
        return $playerResource== null ? 0 : $playerResource->getQuantity();
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
    public function initializeNewGame(GameMYR $game) : void
    {
        $this->initializeNewYear($game);

        $this->initializePreys($game);

        //TODO: initialize game objective

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

        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    /**
     * getPheromonesFromType : returns all orientations of a pheromone or special tile from a type
     * @param int $type
     * @return ArrayCollection<Int, TileTypeMYR>
     */
    public function getPheromonesFromType(int $type) : ArrayCollection
    {
        return new ArrayCollection($this->tileTypeMYRRepository->findBy(
            ["type" => $type]
        ));
    }

    /**
     * initializeNewSeason: initialize in the DB a new season with the selected name
     * @param GameMYR $game
     * @param string $seasonName
     * @return void
     */
    public function initializeNewSeason(GameMYR $game, string $seasonName) : void
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
     * getDiceResults : get dice results for all season
     * @param GameMYR $game
     * @return ArrayCollection<
     */
    public function getDiceResults(GameMYR $game) : ArrayCollection
    {
        $result = new ArrayCollection();
        $mainBoard = $game->getMainBoardMYR();
        $fall = $this->seasonMYRRepository->findOneBy(["mainBoard" => $mainBoard, "name" => MyrmesParameters::FALL_SEASON_NAME]);
        $result[$fall->getName()] = $fall->getDiceResult();
        $spring = $this->seasonMYRRepository->findOneBy(["mainBoard" => $mainBoard, "name" => MyrmesParameters::SPRING_SEASON_NAME]);
        $result[$spring->getName()] = $spring->getDiceResult();
        $summer = $this->seasonMYRRepository->findOneBy(["mainBoard" => $mainBoard, "name" => MyrmesParameters::SUMMER_SEASON_NAME]);
        $result[$summer->getName()] = $summer->getDiceResult();
        return $result;
    }

    /**
     * getActualSeason : returns the actual season
     *
     * @param GameMYR $game
     * @return SeasonMYR|null
     */
    public function getActualSeason(GameMYR $game) : ?SeasonMYR
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
    public function getPlayerResourceOfType(PlayerMYR $player, string $type) : ?PlayerResourceMYR
    {
        $personalBoard = $player->getPersonalBoardMYR();

        foreach ($personalBoard->getPlayerResourceMYRs() as $playerResource)
        {
            if ($playerResource->getResource()->getDescription() === $type)
            {
                return $playerResource;
            }
        }

        return null;
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
     * isGameEnded : returns true if the game reached its end
     * @param GameMYR $game
     * @return bool
     */
    public function isGameEnded(GameMYR $game) : bool
    {
        return $game->getMainBoardMYR()->getYearNum() > MyrmesParameters::THIRD_YEAR_NUM;
    }

    /**
     * manageEndOfRound : does all actions concerning the end of a round
     * @param GameMYR $game
     * @return void
     */
    public function manageEndOfRound(GameMYR $game) : void
    {
        $players = $game->getPlayers();
        foreach ($players as $player) {
            $this->discardLarvae($player);
            $this->replaceWorkers($player);
            $this->replaceNurses($player);
        }
        $this->endRoundOfFirstPlayer($game);
        $this->endSeason($game);
        $this->resetGameGoalsDoneDuringTheRound($game);
    }

    /**
     * resetGameGoalsDoneDuringTheRound : at the end of the round, for each game goal,
     *  clears the list of players who've accomplished an objective during the round
     *
     * @param GameMYR $game
     * @return void
     */
    private function resetGameGoalsDoneDuringTheRound(GameMYR $game) : void
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
    private function discardLarvae(PlayerMYR $player) : void
    {
        $selectedLarvae = $player->getPersonalBoardMYR()->getSelectedEventLarvaeAmount();
        $playerLarvae = $player->getPersonalBoardMYR()->getLarvaCount();
        $player->getPersonalBoardMYR()->setLarvaCount($playerLarvae - $selectedLarvae);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * replaceWorkers : each worker of the player is put back into worker area
     * @param PlayerMYR $player
     * @return void
     */
    private function replaceWorkers(PlayerMYR $player) : void
    {
        $anthillWorkers = $player->getPersonalBoardMYR()->getAnthillWorkers();
        foreach ($anthillWorkers as $worker) {
            $worker->setWorkFloor(MyrmesParameters::WORKER_AREA);
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
    private function replaceNurses(PlayerMYR $player) : void
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
    private function initializePreys(GameMYR $game) : void
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
    private function initializePrey(GameMYR $game, string $type, array $position) : void
    {
        $prey = new PreyMYR();
        $prey->setType($type);
        $tile = $this->tileMYRRepository->findOneBy(['coord_X' => $position[0], 'coord_Y' => $position[1]]);
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
    private function initializePlayerData(PlayerMYR $player, string $color) : void
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

        $this->initializeColorForPlayer($player, $color);
        $this->initializePlayerResources($player);

        $this->entityManager->persist($player);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
        $this->setPhase($player, MyrmesParameters::PHASE_EVENT);
    }

    /**
     * initializePlayerResources: initialize with 0 quantity the player resources
     * @param PlayerMYR $player
     * @return void
     */
    private function initializePlayerResources(PlayerMYR $player) : void
    {
        foreach ($this->resourceMYRRepository->findAll() as $resource) {
            $playerResource = new PlayerResourceMYR();
            $playerResource->setResource($resource);
            $playerResource->setPersonalBoard($player->getPersonalBoardMYR());
            $playerResource->setQuantity(0);
            $this->entityManager->persist($playerResource);
        }
    }

    /**
     * initializeNurse: initialize a nurse for the selected player
     * @param PlayerMYR $player
     * @return void
     */
    private function initializeNurse(PlayerMYR $player) : void
    {
        $nurse = new NurseMYR();
        $nurse->setPlayer($player);
        $nurse->setArea(MyrmesParameters::BASE_AREA);
        $nurse->setAvailable(true);
        $nurse->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $this->entityManager->persist($nurse);
    }

    /**
     * initializeWorker: initialize a new worker for the player
     * @param PlayerMYR $player
     * @return void
     */
    private function initializeWorker(PlayerMYR $player) : void
    {
        $worker = new AnthillWorkerMYR();
        $worker->setPlayer($player);
        $worker->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $worker->setWorkFloor(MyrmesParameters::NO_WORKFLOOR);
        $this->entityManager->persist($worker);
    }

    /**
     * initializeAnthillHoleForPlayer: create and initialize a new anthill hole for the player at the selected position
     * @param PlayerMYR $player
     * @param array $position
     * @return void
     */
    private function initializeAnthillHoleForPlayer(PlayerMYR $player, array $position) : void
    {
        $tile = $this->tileMYRRepository->findOneBy(['coord_X' => $position[0], 'coord_Y' => $position[1]]);
        $hole = new AnthillHoleMYR();
        $hole->setPlayer($player);
        $hole->setTile($tile);
        $player->getGameMyr()->getMainBoardMYR()->addAnthillHole($hole);
        $this->entityManager->persist($hole);
        $this->entityManager->persist($player->getGameMyr()->getMainBoardMYR());
    }

    /**
     * initializeColorForPlayer : set the player's color
     * @param PlayerMYR $player
     * @param string $color
     * @return void
     */
    private function initializeColorForPlayer(PlayerMYR $player, string $color) : void
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
    private function initializeEventBonus(GameMYR $game) : void
    {
        foreach ($game->getPlayers() as $player) {
            $player->getPersonalBoardMYR()->setBonus($this->getActualSeason($game)->getDiceResult());
            $this->entityManager->persist($player);
        }
        $this->entityManager->flush();
    }


    /**
     * initializeMainBoardTiles: initialize the tiles of the mainBoard based on the number of players of the game
     * @param GameMYR $game
     * @return void
     */
    private function initializeMainBoardTiles(GameMYR $game) : void
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
        }
        foreach ($tiles as $tile) {
            $game->getMainBoardMYR()->addTile($tile);
        }
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->flush();
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
            "player" => $player]);
        return new ArrayCollection($nurses);
    }

    /**
     * manageNursesAfterBonusGive : Replace all nurses that have been used
     * @param PlayerMYR $player
     * @param int $nurseCount
     * @param int $positionOfNurse
     * @return void
     * @throws Exception
     */
    public function manageNursesAfterBonusGive(PlayerMYR $player, int $nurseCount, int $positionOfNurse) : void
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
                    case MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA:
                    case MyrmesParameters::WORKSHOP_LEVEL_AREA:
                    case MyrmesParameters::WORKSHOP_NURSE_AREA:
                        $n->setArea(MyrmesParameters::BASE_AREA);
                        $this->entityManager->persist($n);
                        break;
                    case MyrmesParameters::WORKSHOP_GOAL_AREA:
                        break;
                    default:
                        throw new Exception("Don't manage bonus");
                }
                $nurseCount--;
            }
        }
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
        }
        $this->entityManager->persist($playerMYR);
        $this->entityManager->flush();
    }

    /**
     * canDoGoal : returns true if player do not have done the objective yet,
     *      and have done at least an objective of inferior level
     * @param PlayerMYR $playerMYR
     * @param GameGoalMYR $goalMYR
     * @return bool
     */
    private function canDoGoal(PlayerMYR $playerMYR, GameGoalMYR $goalMYR) : bool
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
    private function computePlayerRewardPointsWithGoal(PlayerMYR $playerMYR, GameGoalMYR $goalMYR) : void
    {
        $precedentPlayers = $goalMYR->getPrecedentsPlayers();
        foreach ($precedentPlayers as $player) {
            $player->setScore($player->getScore() +
                MyrmesParameters::GOAL_REWARD_WHEN_GOAL_ALREADY_DONE);
            $this->entityManager->persist($player);
        }
        $gameGoals = $playerMYR->getGameGoalMYRs();
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
    private function endRoundOfFirstPlayer(GameMYR $game) : void
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
    private function endSeason(GameMYR $game) : void
    {
        $actualSeason = $this->getActualSeason($game);
        $mainBoard = $game->getMainBoardMYR();
        if ($actualSeason->getName() === MyrmesParameters::WINTER_SEASON_NAME) {
            $this->initializeNewYear($game);
            return;
        }
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
        $winter = $this->seasonMYRRepository->findOneBy(
            [
                "mainBoard" => $mainBoard,
                "name" => MyrmesParameters::WINTER_SEASON_NAME
            ]
        );
        if ($actualSeason->getName() === MyrmesParameters::SPRING_SEASON_NAME) {
            $summer->setActualSeason(true);
            $this->entityManager->persist($summer);
            $this->initializeEventBonus($game);
            $this->entityManager->persist($game);
        } else if ($actualSeason->getName() === MyrmesParameters::SUMMER_SEASON_NAME) {
            $fall->setActualSeason(true);
            $this->entityManager->persist($fall);
            $this->initializeEventBonus($game);
            $this->entityManager->persist($game);
        } else if ($actualSeason->getName() === MyrmesParameters::FALL_SEASON_NAME) {
            $winter->setActualSeason(true);
            $this->entityManager->persist($winter);
            $this->entityManager->persist($game);
        }
        $actualSeason->setActualSeason(false);
        $this->entityManager->persist($actualSeason);
        $this->entityManager->flush();
    }

    /**
     * initializeNewYear : initializes a new year
     * @param GameMYR $game
     * @return void
     */
    private function initializeNewYear(GameMYR $game) : void
    {
        $this->clearSeasons($game);
        $yearNum = $game->getMainBoardMYR()->getYearNum();
        $game->getMainBoardMYR()->setYearNum($yearNum + 1);
        if ($yearNum > MyrmesParameters::THIRD_YEAR_NUM) {
            return;
        }
        $this->initializeNewSeason($game, MyrmesParameters::SPRING_SEASON_NAME);
        $this->initializeNewSeason($game, MyrmesParameters::SUMMER_SEASON_NAME);
        $this->initializeNewSeason($game, MyrmesParameters::FALL_SEASON_NAME);
        $spring = $this->seasonMYRRepository->findOneBy(
            [
                "mainBoard" => $game->getMainBoardMYR(),
                "name" => MyrmesParameters::SPRING_SEASON_NAME
            ]
        );
        $spring->setActualSeason(true);
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->persist($spring);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }


    private function initializeGoals(GameMYR $game) : void
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
        $game->getMainBoardMYR()->addGameGoalsLevelOne($goalsLevelOne[0]);
        $game->getMainBoardMYR()->addGameGoalsLevelOne($goalsLevelOne[1]);
        $game->getMainBoardMYR()->addGameGoalsLevelTwo($goalsLevelTwo[0]);
        $game->getMainBoardMYR()->addGameGoalsLevelTwo($goalsLevelTwo[1]);
        $game->getMainBoardMYR()->addGameGoalsLevelThree($goalsLevelThree[0]);
        $game->getMainBoardMYR()->addGameGoalsLevelThree($goalsLevelThree[1]);

        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->flush();
    }

    /**
     * clearSeasons : clear all seasons after new Year
     *
     * @param GameMYR $game
     * @return void
     */
    private function clearSeasons(GameMYR $game) : void
    {
        $seasons = $game->getMainBoardMYR()->getSeasons();
        foreach ($seasons as $season) {
            $game->getMainBoardMYR()->removeSeason($season);
        }
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->flush();
    }

}