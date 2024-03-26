<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class MYRService
{

    public function __construct(private PlayerMYRRepository $playerMYRRepository,
                private EntityManagerInterface $entityManager,
                private readonly NurseMYRRepository $nurseMYRRepositoryr)
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
        $game->getMainBoardMYR()->setYearNum(MyrmesParameters::$FIRST_YEAR_NUM);

        $this->initializeNewSeason($game, MyrmesParameters::$SPRING_SEASON_NAME);
        $this->initializeNewSeason($game, MyrmesParameters::$SUMMER_SEASON_NAME);
        $this->initializeNewSeason($game, MyrmesParameters::$FALL_SEASON_NAME);

        $this->entityManager->persist($game);
        $this->entityManager->flush();
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
                    case MyrmesParameters::$LARVAE_AREA:
                    case MyrmesParameters::$SOLDIERS_AREA:
                    case MyrmesParameters::$WORKER_AREA:
                    case MyrmesParameters::$WORKSHOP_ANTHILL_HOLE_AREA:
                    case MyrmesParameters::$WORKSHOP_LEVEL_AREA:
                    case MyrmesParameters::$WORKSHOP_NURSE_AREA:
                        $n->setPosition(MyrmesParameters::$BASE_AREA);
                        $this->entityManager->persist($n);
                        break;
                    case MyrmesParameters::$WORKSHOP_GOAL_AREA:
                        break;
                    default:
                        throw new Exception("Don't manage bonus");
                }
                $nurseCount--;
            }
        }
    }
}