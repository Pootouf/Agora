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

    //Success return code
    private static $SUCCESS = 1;

    //Nb of Days before expiration of an invitation
    private static $DAYS_BEFORE_EXPIRATION = "+1 week";
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

        $actualDate = new \DateTime();

        //setting creation date
        $board->setCreationDate($actualDate);
        // Calculating expiration date of invitation
        $expirationDate = $actualDate->modify($this::$DAYS_BEFORE_EXPIRATION);
        $expirationDate->modify('tomorrow')->setTime(0, 0, 0);
        $board->setInvitationTimer($expirationDate);

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

        return BoardManagerService::$SUCCESS;
    }




}