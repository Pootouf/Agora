<?php

namespace App\Service\Platform;

use App\Entity\Platform\Board;
use App\Entity\Platform\Game;
use App\Entity\Platform\User;
use App\Service\Game\GameManagerService;
use Doctrine\ORM\EntityManagerInterface;

class BoardManagerService
{

    private GameManagerService $gameManagerService;

    private static $SUCCESS = 1;
    private EntityManagerInterface $entityManagerInterface;

    public function __construct(
        GameManagerService $gameManagerService,
        EntityManagerInterface $entityManagerInterface
    )
    {
        $this->gameManagerService = $gameManagerService;
        $this->entityManagerInterface = $entityManagerInterface;
    }


    //Set up all informations which didn't come from the form
    public function setUpBoard(Board $board, Game $game):int
    {

        //setting all timers of the board
        $board->setCreationDate(new \DateTime());
        $board->setInvitationTimer(new \DateTime());
        $board->setInactivityTimer(new \DateTime());

        //create the instance of game and register its id to the board
        $gameId = $this->gameManagerService->createGame($game->getLabel());
        $board->setPartyId($gameId);

        return BoardManagerService::$SUCCESS;
    }

    // Add $user to the board, and to the game
    public function addUserToBoard(Board $board, User $user):int
    {
        $this->gameManagerService->joinGame($board->getPartyId() , $user);

        //If it was the last player to complete the board, launch the game
        if($board->isFull()){
            $this->gameManagerService->launchGame($board->getPartyId());
            $board->setStatus("IN_GAME");
        }
        $this->entityManagerInterface->persist($board);
        $this->entityManagerInterface->persist($user);
        $user->addBoard($board);
        $this->entityManagerInterface->flush();

        return BoardManagerService::$SUCCESS;
    }

    // Remove $user from the board
    public function removePlayerFromBoard(Board $board, User $user):int
    {
        $board->removeListUser($user);
        $this->gameManagerService->quitGame($board->getPartyId(), $user);

        return BoardManagerService::$SUCCESS;
    }


}