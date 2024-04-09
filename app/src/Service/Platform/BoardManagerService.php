<?php

namespace App\Service\Platform;

use App\Entity\Platform\Board;
use App\Entity\Platform\Game;
use App\Entity\Platform\User;
use App\Service\Game\GameManagerService;
use App\Service\Platform\NotificationService;
use Doctrine\ORM\EntityManagerInterface;

class BoardManagerService
{

    private GameManagerService $gameManagerService;

    private static $SUCCESS = 1;
    private EntityManagerInterface $entityManagerInterface;

    private NotificationService $notificationService;

    public function __construct(
        GameManagerService $gameManagerService,
        EntityManagerInterface $entityManagerInterface,
        notificationService $notificationService
    )
    {
        $this->gameManagerService = $gameManagerService;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->notificationService = $notificationService;
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
        $board->setGame($game);
        $board->setPartyId($gameId);

        return BoardManagerService::$SUCCESS;
    }

    // Add $user to the board, and to the game
    public function addUserToBoard(Board $board, User $user):int
    {
        $this->gameManagerService->joinGame($board->getPartyId() , $user);
        $board->addListUser($user);

        //If it was the last player to complete the board, launch the game
        if($board->isFull()){
            $this->gameManagerService->launchGame($board->getPartyId());
            $board->setStatus("IN_GAME");
            $users = $board->getListUsers();
            $this->notificationService->notifyManyUser($users, "La partie ".$board->getPartyId()." du jeu ".$board->getGame()->getLabel()." a démarré, vous pouvez maintenant jouer", new \DateTime());
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

        $this->entityManagerInterface->persist($board);
        $this->entityManagerInterface->flush();
        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->flush();

        return BoardManagerService::$SUCCESS;
    }


}