<?php

namespace App\Controller\Platform;

use App\Data\SearchData;
use App\Data\SearchUser;
use App\Entity\Platform\User;
use App\Entity\Game\Message;
use App\Repository\Platform\BoardRepository;
use App\Form\Platform\GenerateAccountsType;
use App\Service\Platform\AdminService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Platform\NotificationService;
use Symfony\Bundle\SecurityBundle\Security;
use App\Form\Platform\SearchBoardType;
use App\Form\Platform\SearchUserType;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
{
    private ?array $notifications;
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager, NotificationService $notificationService)
    {
        $this->notifications = $notificationService->getNotifications($security);
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'app_dashboard_admin')]
    public function index(): Response
    {
        return $this->render('platform/admin/index.html.twig');
    }

    #[Route('/admin/ban/{id}', name: 'app_dashboard_ban_user')]
    public function banUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        return $this->render('platform/admin/banUser.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Controller method for generating user accounts in the admin dashboard.
     *
     * This method handles the form submission for generating user accounts.
     *
     * @param Request $request The incoming request object.
     * @param AdminService $adminService The service responsible for admin-related functionality.
     * @return Response The response object containing the generated user accounts form.
     */
    #[Route('/admin/generate', name: 'app_dashboard_generate')]
    public function createaccount(Request $request, AdminService $adminService): Response
    {
        $form = $this->createForm(GenerateAccountsType::class);
        $form->handleRequest($request);

        $users = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $numberOfAccounts = $formData['nbAccounts'];
            // Generate and save accounts
            $users = $adminService->generateAccounts($numberOfAccounts);

            $this->addFlash('success', "Vos comptes sont créés avec les noms d'utilisateurs listés avec le mot de passe: agora");
            return $this->render('platform/admin/generateAccount.html.twig', [
                'form' => $form->createView(),
                'users' => $users,
            ]);
        }
        return $this->render('platform/admin/generateAccount.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    /**
     * Controller method for displaying the admin table.
     *
     * This method handles the request to display the admin table, which lists all boards and relevant data.
     *
     * @param Request $request The incoming request object.
     * @param BoardRepository $boardRepository The repository for accessing board data.
     * @param NotificationService $notificationService The service responsible for managing notifications.
     * @return Response The response object containing the admin table view.
     */
    #[Route('/admin/tableadmin', name: 'app_dashboard_tableadmin')]
    public function tableadmin(Request $request, BoardRepository $boardRepository, NotificationService $notificationService): Response
    {

        $user = $this->getUser();
        $data = new SearchData();
        $form = $this->createForm(SearchBoardType::class, $data);
        $form->handleRequest($request);

        $message = $this->entityManager->getRepository(Message::class)->findAll();

        $boards = $boardRepository->searchBoards($data);

        if ($user) {
            $notifications = $notificationService->getNotifications($this->security);
        }
        return $this->render('platform/admin/tableadmin.html.twig', [
            'boards' => $boards,
            'searchboard' => $form->createView(),
            'messages' => $message,
            'notifications' => $this->notifications,
        ]);
    }

    /**
     * @param EntityManagerInterface $entityManager The entity manager to interact with the database
     *
     * @return Response  HTTP response: list of games page
     */
    #[Route('/admin/allusers', name: 'app_dashboard_allusers')]
    public function dashboard_allusers(Request $request): Response
    {
        // Récupérer tous les utilisateurs à partir de votre source de données (par exemple, une entité User)
        $userRepository = $this->entityManager->getRepository(User::class);

        $data = new SearchUser();
        $form = $this->createForm(SearchUserType::class, $data);
        $form->handleRequest($request);
        $users = $userRepository->searchUsers($data);


        // Passer la liste des utilisateurs à votre modèle de vue
        return $this->render('platform/admin/allusers.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
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

    /**
     * Controller method for attributing a role to a user.
     *
     * This method handles the request to attribute a specific role to a given user.
     *
     * @param User $user The user entity to which the role will be attributed.
     * @param string $role The role to be attributed to the user.
     * @return JsonResponse The JSON response indicating the success of role attribution.
     */
    #[Route('/admin/attributerole/{user}/{role}', name: 'app_attribute_role')]
    public function attributerolemoderate(User $user, String $role): JsonResponse
    {
        $user->setRoles([$role]);
        $this->entityManager->flush();
        $this->addFlash('success', 'L\'utilisateur '. $user->getUsername(). ' a reçu un nouveau rôle');
        return new JsonResponse(['message' => 'Role attribuée avec succès']);
    }



}
