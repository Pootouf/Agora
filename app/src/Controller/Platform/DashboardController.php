<?php

namespace App\Controller\Platform;

use App\Data\SearchData;
use App\Entity\Platform\Board;
use App\Entity\Platform\Game;
use App\Entity\Platform\Notification;
use App\Entity\Platform\User;
use App\Form\Platform\SearchBoardType;
use App\Repository\Platform\BoardRepository;
use App\Service\Platform\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\Platform\EditProfileType;
use Symfony\Component\HttpFoundation\Request;


class DashboardController extends AbstractController
{
    private $notifications;
    private $security;

    private $entityManager;
    public function __construct(Security $security, EntityManagerInterface $entityManager, NotificationService $notificationService)
    {
        $this->notifications = $notificationService->getNotifications($security);
        $this->security = $security;
        $this->entityManager = $entityManager;
    }
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('platform/dashboard/index.html.twig', [
            'notifications' => $this->notifications,
        ]);
    }
    #[Route('/dashboard/profile', name: 'app_dashboard_profile')]
    public function profile(): Response
    {
        if($this->security->getUser()) {
            $userId = $this->security->getUser()->getId();
            $userRepository = $this->entityManager->getRepository(Board::class);
            $favGames = $this->security->getUser()->getFavoriteGames();
            $currentBoards = $userRepository->findBoardsByUserAndStatus($userId, "IN_GAME");
            $pastBoards = $userRepository->findBoardsByUserAndStatus($userId, "WAITING");
            $userBan = $this->security->getUser()->isIsBanned();
        }
        else {
            $favGames = null;
            $currentBoards = null;
            $pastBoards = null;
            $userBan = null;
        }
        return $this->render('platform/dashboard/profile.html.twig', [
            'fav_games' => $favGames,
            'current_boards'=> $currentBoards,
            'past_boards' => $pastBoards,
            'notifications' => $this->notifications,
            'userban' => $userBan
        ]);
    }

    #[Route('/dashboard/profile/{user_id}', name: 'app_other_user_profile', requirements: ['user_id' => '\d+'])]
    public function getUserProfile(int $user_id) : Response
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $boardRepository = $this->entityManager->getRepository(Board::class);

        $user = $userRepository->find($user_id);

        $favGames = $user->getFavoriteGames();
        $currentBoards = $boardRepository->findBoardsByUserAndStatus($user_id, "IN_GAME");
        $pastBoards = $boardRepository->findBoardsByUserAndStatus($user_id, "WAITING");
        $userBan = $user->isIsBanned();

        return $this->render('platform/dashboard/profile.html.twig', [
            'fav_games' => $favGames,
            'current_boards'=> $currentBoards,
            'past_boards' => $pastBoards,
            'userProfile' => $user,
            'notifications' => $this->notifications,
            'userban' => $userBan
        ]);
    }

    #[Route('/dashboard/settings', name: 'app_dashboard_settings')]
    #[Route('/admin/settings', name: 'app_admin_settings')]
    public function settings(Request $request): Response
    {
        // Récupérer la route
        $routeName = $request->attributes->get('_route');

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Créer le formulaire de modification de profil
        $form = $this->createForm(EditProfileType::class, $user);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        // Rendre la vue en passant le formulaire
        if ($routeName === 'app_admin_settings') {
            return $this->render('platform/users/editAdminProfile.html.twig', [
                'form' => $form->createView(),
                'notifications' => $this->notifications,
            ]);
        } else {
            return $this->render('platform/users/editUserProfile.html.twig', [
                'form' => $form->createView(),
                'notifications' => $this->notifications,
            ]);
        }
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
            'notifications' => $this->notifications,
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
            'notifications' => $this->notifications,
        ]);
    }

    //    Get all boards of a unique game
    #[Route('/dashboard/game/{id}/tables', name: 'app_boards_game', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function tablesByGame(int $id, BoardRepository $boardRepository, Request $request): Response
    {
//        retrieve game by id
        $game = $this->entityManager->getRepository(Game::class)->find($id);
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
            'notifications' => $this->notifications,
        ]);
    }


    #[Route('/dashboard/history', name: 'app_board_history')]
    public function history(): Response
    {
        return $this->render('platform/dashboard/history.html.twig', [
            'notifications' => $this->notifications,
        ]);
    }

    #[Route('/dashboard/tableadmin', name: 'app_board_history')]
    public function tableadmin(Request $request, BoardRepository $boardRepository): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchBoardType::class, $data);
        $form->handleRequest($request);

        $boards = $boardRepository->searchBoards($data);
        return $this->render('platform/dashboard/tableadmin.html.twig', [
            'boards' => $boards,
            'searchboard' => $form->createView(),
            'notifications' => $this->notifications,
        ]);
    }

    /**
     * Displays a list of games.
     *
     * This method retrieves all games from the database using the entity manager (when the user is connected)
     *
     * @param EntityManagerInterface $entityManager The entity manager to interact with the database
     *
     * @return Response  HTTP response: list of games page
     */
    #[Route('/dashboard/games', name: 'app_dashboard_games')]
    public function dashboard_game_index(): Response
    {
        $gameRepository = $this->entityManager->getRepository(Game::class);

        $games = $gameRepository->findAll();

        return $this->render('platform/dashboard/games/games.html.twig', [
            'games' => $games,
            'notifications' => $this->notifications,
        ]);
    }

    /**
     * Fetches the game information from the database based on the provided game ID (when the user is connected)
     *
     * @param EntityManagerInterface $entityManager The entity manager to interact with the database
     * @param int @game_id ID of the game to displayed on description page
     *
     * @return Response HTTP response: game description by ID page
     */
    #[Route('/dashboard/games/{game_id}', name: 'app_dashboard_game_description', requirements: ['game_id' => '\d+'], methods: ['GET', 'HEAD'])]
    public function dashboard_game_description(int $game_id): Response
    {
        $gameRepository = $this->entityManager->getRepository(Game::class);
        $game = $gameRepository->find($game_id);

        if(!$game) {
            $this->addFlash('warning', 'Le jeu n\'existe pas');
            return $this->redirectToRoute('app_games');
        }

        return $this->render('platform/dashboard/games/description.html.twig', [
            'game' => $game,
            'notifications' => $this->notifications,
        ]);
    }

    /**
     * @param EntityManagerInterface $entityManager The entity manager to interact with the database
     *
     * @return Response  HTTP response: list of games page
     */
    #[Route('/admin/allusers', name: 'app_dashboard_allusers')]
    public function dashboard_allusers(): Response
    {
        // Récupérer tous les utilisateurs à partir de votre source de données (par exemple, une entité User)
        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        // Passer la liste des utilisateurs à votre modèle de vue
        return $this->render('platform/admin/allusers.html.twig', [
            'users' => $users,
            'notifications' => $this->notifications, // Assurez-vous que vos notifications sont également disponibles dans ce contrôleur
        ]);
    }

     /**
     * @param EntityManagerInterface $entityManager The entity manager to interact with the database
     *
     * @return Response  HTTP response: list of games page
     */
    #[Route('/admin/banmanager', name: 'app_dashboard_banmanager')]
    public function dashboard_banmanager(): Response
    {
        // Récupérer tous les utilisateurs à partir de votre source de données (par exemple, une entité User)
        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        // Passer la liste des utilisateurs à votre modèle de vue
        return $this->render('platform/admin/banmanager.html.twig', [
            'users' => $users,
            'notifications' => $this->notifications, // Assurez-vous que vos notifications sont également disponibles dans ce contrôleur
        ]);
    }
    #[Route('/readNotification/{notification}', name: 'app_read_notif')]
    public function readNotification(Notification $notification): JsonResponse
    {
        $notification->setIsRead(true);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Notification lue avec succès']);
    }
}
