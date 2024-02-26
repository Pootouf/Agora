<?php

namespace App\Controller\Platform;



use App\Entity\Platform\Game;
use App\Entity\Platform\Board;
use App\Entity\Platform\User;
use App\Form\Platform\BoardRegistrationType;
use App\Form\Platform\SearchBoardType;
use App\Repository\Platform\BoardRepository;
use App\Service\Game\GameManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BoardController extends AbstractController
{

    private GameManagerService $gameManagerService;
    
    public function __construct(GameManagerService $gameManagerService)
    {
        $this->gameManagerService = $gameManagerService;
    }
    #[Route('/boardCreation/{game_id}', name: 'app_board_create', requirements: ['game_id' => '\d+'], methods: ['GET', 'POST', 'HEAD'])]
    public function create(Request $request, $game_id, EntityManagerInterface $manager, Security $security): Response
    {
        //Find the game with the id passed in parameters
        $game = $manager->getRepository(Game::class)->find($game_id);
        //create a board
        $board = new Board();
        $form = $this->createForm(BoardRegistrationType::class, $board, [
                'game' => $game]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //setting all timers of the board
            $board->setCreationDate(new \DateTime());
            $board->setInvitationTimer(new \DateTime());
            $board->setInactivityTimer(new \DateTime());

            //create the instance of game and register its id to the board
            $gameId = $this->gameManagerService->createGame($game->getLabel());
            $board->setGameId($gameId);

            //adding user to the board, and board to the user's board list
            $userId = $security->getUser()->getId();
            $user = $manager->getRepository(User::class)->find($userId);
            $user->addBoard($board);
            $this->gameManagerService->joinGame($gameId, $user);

            //adding the new board to the game's board list
            // A COMPLETER APRES LE FIX DE MICHEL

            //persist the information to the database
            $manager->persist($board);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'La table à bien été créé !'
            );

            return $this->redirectToRoute('app_home');
        }
        return $this->render('platform/game/boardRegister.html.twig', [
            'game' => $game,
            'form' => $form->createView()
        ]);
    }

    #[Route('/joinBoard/{id}', name: 'app_join_board')]
    public function joinBoardController(int $id, EntityManagerInterface $entityManager, Security $security,  ): Response
    {
        /*$boards = $entityManager->getRepository(Board::class)->findAll();
        dd($boards);*/
        //get the board object
        $board = $entityManager->getRepository(Board::class)->find($id);
        //get the logged user
        $userId = $security->getUser()->getId();
        $user = $entityManager->getRepository(User::class)->find($userId);
        //get the bord data
        $boardStatus = $board->getStatus();
        $boardMaxUser = $board->getNbUserMax();
        $boardUserNb = $board->getUsersNb();
        //test if the user can join a table
        if ($board->hasUser($user)||$boardStatus === "IN_GAME" || $boardStatus === "FINISH" || $boardUserNb == $boardMaxUser) {
            $errorMessage = "Impossible de rejoindre la table";
            //send the error message to user, using session or flush
            $this->addFlash('warning', $errorMessage);
            return $this->redirectToRoute('app_dashboard_tables');
        }
        //add user the Board users list
        $user->addBoard($board);
        $this->addFlash('success', 'La table a bien été rejointe ');

        //add the user to the game data
        $this->gameManagerService->joinGame($board->getGameId(), $user);

        //If it was the last player to complete the board, launch the game
        if($board->isFull()){
            $this->gameManagerService->launchGame($board->getGameId());
            $board->setStatus("IN_GAME");
        }
        //save changes
        $entityManager->persist($board);
        $entityManager->flush();
        $entityManager->persist($user);
        $entityManager->flush();

        //dd($user->getBoards());


        return $this->redirectToRoute('app_dashboard_user');
    }

#[Route('/leaveBoard/{id}', name: 'app_leave_board')]
public function leaveBoard(int $id, EntityManagerInterface $entityManager, Security $security):Response
{
    $board = $entityManager->getRepository(Board::class)->find($id);
    //get the logged user
    $userId = $security->getUser()->getId();
    $user = $entityManager->getRepository(User::class)->find($userId);
    //remove the user from user list
    $user->removeBoard($board);
    $this->gameManagerService->quitGame($board->getGameId(), $user);

    $entityManager->persist($board);
    $entityManager->flush();
    $entityManager->persist($user);
    $entityManager->flush();

    return $this->redirectToRoute('app_dashboard_tables');
}

    #[\Symfony\Component\Routing\Attribute\Route('/dashboard/user', name: 'app_dashboard_user', methods: ['GET'])]
    public function index(Request $request, Security $security): Response
    {
        $boards = $security->getUser()->getBoards();
        $form = $this->createForm(SearchBoardType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->render('platform/dashboard_tables/index.html.twig', [
            'boards' => $boards,
            'searchboard' => $form->createView(),
        ]);
    }
    #[Route('/dashboard/tables', name: 'app_dashboard_tables', methods: ['GET'])]
    public function tables(Request $request, BoardRepository $boardRepository): Response
    {
        $boards = $boardRepository->findAll();
        $form = $this->createForm(SearchBoardType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->render('platform/dashboard_tables/index.html.twig', [
            'boards' => $boards,
            'searchboard' => $form->createView(),
        ]);
    }


}