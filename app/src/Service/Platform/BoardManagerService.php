<?php

namespace App\Service\Platform;

use App\Entity\Platform\Board;
use App\Entity\Platform\Game;
use App\Entity\Platform\Notification;
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
    ) {
        $this->gameManagerService = $gameManagerService;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->notificationService = $notificationService;
    }


    //Set up all informations which didn't come from the form
    public function setUpBoard(Board $board, Game $game): int
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
        $this->entityManagerInterface->persist($board);
        $this->entityManagerInterface->flush();

        //Sending notifications of invitation to all invited contacts
        $invitedUsers = $board->getInvitedContacts();
        $this->notificationService->notifyManyUser($invitedUsers, "Vous êtes invité à rejoindre la table (".$board->getId().") pour jouer à : ".$board->getGame()->getName(), new \DateTime(), Notification::$TYPE_INVITATION);
        $board->setNbInvitations($invitedUsers->count());
        return BoardManagerService::$SUCCESS;
    }

    // Add $user to the board, and to the game
    // Precondition : $board->isFull() == false
    public function addUserToBoard(Board $board, User $user): int
    {
        $this->gameManagerService->joinGame($board->getPartyId(), $user);
        $board->addListUser($user);

        //this part manage the case where an invited user joint the table but not by using his invitation
        if($board->getInvitedContacts()->contains($user)) {
            $board->removeInvitedContact($user);
        }

        //If it was the last player to complete the board, launch the game
        if($board->isFull()) {
            $board->cleanInvitationList();
            $this->gameManagerService->launchGame($board->getPartyId());
            $board->setInGame();
            $users = $board->getListUsers();
            $this->notificationService->notifyManyUser($users, "La table ".$board->getId()." pour le jeu ".$board->getGame()->getName()." a démarré, vous pouvez maintenant jouer", new \DateTime(), "Début de partie");

        }
        $this->entityManagerInterface->persist($board);
        $this->entityManagerInterface->persist($user);
        $user->addBoard($board);
        $this->entityManagerInterface->flush();

        return BoardManagerService::$SUCCESS;
    }

    // Remove $user from the board
    public function removePlayerFromBoard(Board $board, User $user): int
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
