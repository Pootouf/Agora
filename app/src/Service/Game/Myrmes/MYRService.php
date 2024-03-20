<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use Doctrine\ORM\EntityManagerInterface;

class MYRService
{

    public function __construct(private PlayerMYRRepository $playerMYRRepository,
                private EntityManagerInterface $entityManager)
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

}