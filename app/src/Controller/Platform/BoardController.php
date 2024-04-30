<?php

namespace App\Controller\Platform;



use App\Data\SearchData;
use App\Entity\Platform\Game;
use App\Entity\Platform\Board;
use App\Entity\Platform\Notification;
use App\Entity\Platform\User;
use App\Form\Platform\BoardRegistrationType;
use App\Form\Platform\SearchBoardType;
use App\Repository\Platform\BoardRepository;
use App\Service\Platform\BoardManagerService;
use App\Service\Platform\GameViewerService;
use App\Service\Platform\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoardController extends AbstractController
{
    private EntityManagerInterface $entityManagerInterface;

    private BoardManagerService $boardManagerService;
    private GameViewerService $gameViewerService;

    private Security $security;


    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        BoardManagerService $boardManagerService,
        GameViewerService $gameViewerService,
        Security $security)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->boardManagerService = $boardManagerService;
        $this->gameViewerService = $gameViewerService;
        $this->security= $security;
    }
    #[Route('/dashboard/boardCreation/{game_id}', name: 'app_board_create', requirements: ['game_id' => '\d+'], methods: ['GET', 'POST', 'HEAD'])]
    public function create(Request $request, $game_id, NotificationService $notificationService): Response
    {
        //Find the game with the id passed in parameters
        $game = $this->entityManagerInterface->getRepository(Game::class)->find($game_id);
        //get the logged user
        $userId = $this->security->getUser()->getId();
        $allUsers = array_filter($this->entityManagerInterface->getRepository(User::class)->findAll(), function($user) use ($userId) {
            return $user->getId() !== $userId && !$user->isAdmin();
        });
        //create a board
        $board = new Board();
        //generate the BaordRegistrationType form
        $form = $this->createForm(BoardRegistrationType::class, $board, [
                'game' => $game]
        );
        $form->handleRequest($request);
        $user = $this->security->getUser();
        $notifications = null;
        if ($user){
            $notifications = $notificationService->getNotifications($this->security);
        }

        //When the form is valid
        if ($form->isSubmitted() && $form->isValid()) {

            $this->boardManagerService->setUpBoard($board, $game);

            //adding user to the board
            $userId = $this->security->getUser()->getId();
            $user = $this->entityManagerInterface->getRepository(User::class)->find($userId);
            $this->boardManagerService->addUserToBoard($board, $user);

            //adding the new board to the game's board list
            // A COMPLETER APRES LE FIX DE MICHEL


            $this->addFlash(
                'success',
                'La table à bien été créé !'
            );

            return $this->redirectToRoute('app_dashboard_user');
        }
        return $this->render('platform/dashboard/games/boardRegister.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
            'notifications' => $notifications,
            'allUsers' => $allUsers
        ]);
    }

    #[Route('/joinBoard/{id}', name: 'app_join_board')]
    public function joinBoardController(int $id): Response
    {
        /*$boards = $entityManager->getRepository(Board::class)->findAll();
        dd($boards);*/
        //get the board object
        $board = $this->entityManagerInterface->getRepository(Board::class)->find($id);
        //get the logged user
        $userId = $this->security->getUser()->getId();
        $user = $this->entityManagerInterface->getRepository(User::class)->find($userId);
        //test if the user can join a table
        if ($board->hasUser($user)|| !$board->isAvailble()) {
            $errorMessage = "Impossible de rejoindre la table";
            //send the error message to user, using session or flush
            $this->addFlash('warning', $errorMessage);
            $gameId = $board->getGame()->getId();
            return $this->redirectToRoute('app_boards_game', ['id' => $gameId]);
        }
        //add user in the board users list
        $this->boardManagerService->addUserToBoard($board, $user);
        $this->addFlash('success', 'La table a bien été rejointe ');

        return $this->redirectToRoute('app_dashboard_user');
    }

    #[Route('/leaveBoard/{id}', name: 'app_leave_board')]
    public function leaveBoard(int $id):Response
    {
        $board = $this->entityManagerInterface->getRepository(Board::class)->find($id);
        //get the logged user
        $userId = $this->security->getUser()->getId();
        $user = $this->entityManagerInterface->getRepository(User::class)->find($userId);
        //remove the user from user list
        $this->boardManagerService->removePlayerFromBoard($board, $user);


        return $this->redirectToRoute('app_dashboard_tables');
    }


    //Redirect to the route of the game, using the id of the board
    #[Route('/showGame/{id}', name: 'app_join_game', methods: ['GET'])]
    public function showGame(int $id):Response
    {
        $board = $this->entityManagerInterface->getRepository(Board::class)->find($id);
        //get the label of the Game, which the GameViewerService need for the redirection
        $label = $board->getGame()->getLabel();
        $route = $this->gameViewerService->getGameViewRouteFromLabel($label);
        //redirect the player to the view of the party, using the party id
        return $this->redirectToRoute($route, ['id' => $board->getPartyId()]);
    }

    #[Route('/checkInvitation/{id}', name: 'app_join_invitation', methods: ['GET'])]
    public function checkInvitation(int $id):Response
    {
        $board = $this->entityManagerInterface->getRepository(Board::class)->find($id);
        $userId = $this->security->getUser()->getId();
        $user = $this->entityManagerInterface->getRepository(User::class)->find($userId);
        $actualDate = new \DateTime();
        if ($board->getInvitedContacts()->contains($user) && $board->getInvitationTimer() < $actualDate) {
            $this->boardManagerService->removePlayerFromInvitationList($board, $user);
            return $this->redirectToRoute('app_join_board', ['id' => $id]);
        }
        //send the error message to user, using session or flush
        $this->addFlash('warning', "L'invitation a expiré ou n'est pas valide");
        return $this->redirectToRoute('app_dashboard_user');

    }



}