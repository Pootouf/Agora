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
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Repository\Game\Myrmes\TileTypeMYRRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;


/**
 * @codeCoverageIgnore
 */
class MYRService
{

    public function __construct(private PlayerMYRRepository $playerMYRRepository,
                private readonly EntityManagerInterface $entityManager,
                private readonly NurseMYRRepository $nurseMYRRepository,
                private readonly TileMYRRepository $tileMYRRepository,
                private readonly TileTypeMYRRepository $tileTypeMYRRepository)
    {

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
        $game->getMainBoardMYR()->setYearNum(MyrmesParameters::FIRST_YEAR_NUM);

        $this->initializeNewSeason($game, MyrmesParameters::SPRING_SEASON_NAME);
        $this->initializeNewSeason($game, MyrmesParameters::SUMMER_SEASON_NAME);
        $this->initializeNewSeason($game, MyrmesParameters::FALL_SEASON_NAME);

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
     * getPhermononesFromType : returns all orientations of a pheremone or special tile from a type
     * @param int $type
     * @return ArrayCollection<Int, TileTypeMYR>
     */
    public function getPhermononesFromType(int $type) : ArrayCollection
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
        $season->setMainBoardMYR($game->getMainBoardMYR());
        $season->setName($seasonName);
        $season->setDiceResult(rand(1, 6));
        $this->entityManager->persist($season);
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

        $this->entityManager->persist($player);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
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
        $nurse->setPosition(0);
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
        $worker->setWorkFloor(0);
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
            $player->getPersonalBoardMYR()->setBonus($game->getMainBoardMYR()->getActualSeason()->getDiceResult());
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
        $nurses =  $this->nurseMYRRepository->findBy(["position" => $position,
            "player" => $player]);
        return new ArrayCollection($nurses);
    }

    /**
     * manageNursesAfterBonusGive : Replace use nurses
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
                        $n->setPosition(MyrmesParameters::BASE_AREA);
                        $this->entityManager->persist($n);
                        break;
                    case MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA:
                    case MyrmesParameters::WORKSHOP_LEVEL_AREA:
                    case MyrmesParameters::WORKSHOP_NURSE_AREA:
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
     */
    public function doGameGoal(PlayerMYR $playerMYR, GameGoalMYR $goalMYR)
    {
        if(!$this->canDoGoal($playerMYR, $goalMYR)) {
            throw new Exception("Player can't do goal");
        }
        // TODO : COMPUTE GOAL COSTS
        $this->computePlayerRewardPointsWithGoal($playerMYR, $goalMYR->getGoal());
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
     * @param GoalMYR $goalMYR
     * @return void
     */
    private function computePlayerRewardPointsWithGoal(PlayerMYR $playerMYR, GoalMYR $goalMYR) : void
    {
        $gameGoals = $playerMYR->getGameGoalMYRs();
        foreach ($gameGoals as $gameGoal) {
            if($gameGoal->getGoal() === $goalMYR) {
                foreach ($gameGoal->getPrecedentsPlayers() as $player) {
                    $player->setScore($player->getScore() +
                        MyrmesParameters::GOAL_REWARD_WHEN_GOAL_ALREADY_DONE);
                }
            }
        }
        switch ($goalMYR->getDifficulty()) {
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
        $this->addPlayerToOtherPlayersGoal($playerMYR, $goalMYR);
        $this->entityManager->persist($playerMYR);
        $this->entityManager->flush();
    }

    /**
     * addPlayerToOtherPlayersGoal : add the player to the others players goal list
     * @param PlayerMYR $playerMYR
     * @param GoalMYR $goalMYR
     * @return void
     */
    private function addPlayerToOtherPlayersGoal(PlayerMYR $playerMYR, GoalMYR $goalMYR) : void
    {
        $game = $playerMYR->getGameMyr();
        foreach ($game->getPlayers() as $player) {
            if($player !== $playerMYR) {
                $playerGameGoals = $player->getGameGoalMYRs();
                foreach ($playerGameGoals as $playerGameGoal) {
                    if($playerGameGoal->getGoal() === $goalMYR) {
                        $playerGameGoal->addPrecedentsPlayer($player);
                    }
                    $this->entityManager->persist($playerGameGoal);
                }
            }
        }
    }
}