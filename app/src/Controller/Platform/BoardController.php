<?php

namespace App\Controller\Platform;



use App\Data\SearchData;
use App\Entity\Platform\Game;
use App\Entity\Platform\Board;
use App\Entity\Platform\User;
use App\Form\Platform\BoardRegistrationType;
use App\Form\Platform\SearchBoardType;
use App\Repository\Platform\BoardRepository;
use App\Service\Platform\BoardManagerService;
use App\Service\Platform\GameViewerService;
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
    public function create(Request $request, $game_id): Response
    {
        //Find the game with the id passed in parameters
        $game = $this->entityManagerInterface->getRepository(Game::class)->find($game_id);
        //create a board
        $board = new Board();
        $form = $this->createForm(BoardRegistrationType::class, $board, [
                'game' => $game]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->boardManagerService->setUpBoard($board, $game);

            //adding user to the board
            $userId = $this->security->getUser()->getId();
            $user = $this->entityManagerInterface->getRepository(User::class)->find($userId);
            $this->boardManagerService->addUserToBoard($board, $user);

            //adding the new board to the game's board list
            // A COMPLETER APRES LE FIX DE MICHEL

            //persist the information to the database

            $this->addFlash(
                'success',
                'La table à bien été créé !'
            );

            return $this->redirectToRoute('app_dashboard_user');
        }
        return $this->render('platform/dashboard/games/boardRegister.html.twig', [
            'game' => $game,
            'form' => $form->createView()
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
        //get the bord data
        $boardStatus = $board->getStatus();
        $boardMaxUser = $board->getNbUserMax();
        $boardUserNb = $board->getUsersNb();
        //test if the user can join a table
        if ($board->hasUser($user)||$boardStatus === "IN_GAME" || $boardStatus === "FINISH" || $boardUserNb == $boardMaxUser) {
            $errorMessage = "Impossible de rejoindre la table";
            //send the error message to user, using session or flush
            $this->addFlash('warning', $errorMessage);
            return $this->redirectToRoute('app_boards_game', ['id' => $id]);
        }
        //add user in the board users list
        $this->boardManagerService->addUserToBoard($board, $user);
        $this->addFlash('success', 'La table a bien été rejointe ');

        //dd($user->getBoards());


        return $this->redirectToRoute('app_dashboard_tables');
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

        $this->entityManagerInterface->persist($board);
        $this->entityManagerInterface->flush();
        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->flush();

        return $this->redirectToRoute('app_dashboard_tables');
    }
    //    Get all game where connected user participate
    #[Route('/dashboard/user', name: 'app_dashboard_user', methods: ['GET'])]
    public function boardsUser(Request $request, BoardRepository $boardRepository): Response
    {
//        check if we have a connected user
        if($this->security->getUser()){
//        Get all boards where this connected user participate
            $boards = $this->security->getUser()->getBoards();
        }else{
            $boards = null;
        }
//        Create a data model to retrieve information of form
        $data = new SearchData();
        $form = $this->createForm(SearchBoardType::class, $data);
        $form->handleRequest($request);

         $boards = $boardRepository->searchBoards($data);


        return $this->render('platform/dashboard/user.html.twig', [
            'boards' => $boards,
            'searchboard' => $form->createView(),
        ]);
    }
    //    Get all created boards in platform
    #[Route('/dashboard/tables', name: 'app_dashboard_tables', methods: ['GET'])]
    public function allBoards(Request $request, BoardRepository $boardRepository): Response
    {

        $data = new SearchData();
        $form = $this->createForm(SearchBoardType::class, $data);
        $form->handleRequest($request);

        $boards = $boardRepository->searchBoards($data);

        return $this->render('platform/dashboard/tables.html.twig', [
            'boards' => $boards,
            'searchboard' => $form->createView(),
        ]);
    }

    //    Get all boards of a unique game
    #[Route('/dashboard/game/{id}/tables', name: 'app_boards_game', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function tablesByGame(int $id, BoardRepository $boardRepository,  EntityManagerInterface $entityManager, Request $request): Response
    {
//        retrieve game by id
        $game = $entityManager->getRepository(Game::class)->find($id);
        if($game){
            $boards = $boardRepository->findBy(['game' => $game], ['creationDate' => 'DESC']);
        }else{
            $boards = null;
        }
//        Create a data model to retrieve information of form and search boards of a unique game
        $data = new SearchData();
        $form = $this->createForm(SearchBoardType::class, $data);
        $form->handleRequest($request);
        $boards = $boardRepository->searchBoardsByGame($data, $game);

        return $this->render('platform/dashboard/tables.html.twig', [
            'boards' => $boards,
            'searchboard' => $form->createView(),
        ]);
    }


    //Redirect to the route of the game, using the id of the board
    #[Route('/showGame/{id}', name: 'app_join_game', methods: ['GET'])]
    public function showGame(int $id):Response
    {
        $board = $this->entityManagerInterface->getRepository(Board::class)->find($id);
        $label = $board->getGame()->getLabel();
        $route = $this->gameViewerService->getGameViewRouteFromLabel($label);
        return $this->redirectToRoute($route, ['id' => $board->getPartyId()]);
    }

    #[Route('/dashboard/history', name: 'app_board_history')]
    public function index(): Response
    {
        return $this->render('platform/dashboard/history.html.twig', [
            'controller_name' => 'BoardController',
        ]);
    }

}