<?php

namespace App\Controller\Platform;

use App\Data\SearchData;
use App\Data\SearchUser;
use App\Entity\Platform\User;
use App\Entity\Game\Message;
use App\Form\Platform\SearchUserType;
use App\Repository\Platform\BoardRepository;
use App\Form\Platform\GenerateAccountsType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Platform\NotificationService;
use Symfony\Bundle\SecurityBundle\Security;
use App\Form\Platform\SearchBoardType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
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

    #[Route('/admin/generate', name: 'app_dashboard_generate')]
    public function createaccount(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(GenerateAccountsType::class);
        $form->handleRequest($request);

        $users = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $numberOfAccounts = $formData['nbAccounts'];
            // Generate and save accounts
            for ($i = 0; $i < $numberOfAccounts; $i++) {
                $user = new User();
                $randomName = $this->generateRandomName();
                $user->setUsername($randomName . $i);
                $users[] = $randomName . $i;
                $user->setEmail($randomName."{$i}@univ-rouen.fr");
                $user->setIsVerified(true);
                $password = $hasher->hashPassword($user, 'agora');
                $user->setPassword($password);
                $user->setRoles(['ROLE_USER']);
                $entityManager->persist($user);
            }
            $entityManager->flush();
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

    private function generateRandomName(): string
    {
        $names = ['alice', 'bob', 'toto', 'elisa', 'eve'];
        $randomNumber = mt_rand(0000000001, 9999999999);
        $name = $names[array_rand($names)];
        $pseudo = $name. '_' . $randomNumber ;
        return $pseudo;
    }

    #[Route('/admin/tableadmin', name: 'app_dashboard_tableadmin')]
    public function tableadmin(Request $request, BoardRepository $boardRepository, NotificationService $notificationService): Response
    {
    
        $user = $this->getUser();
        $data = new SearchData();
        $form = $this->createForm(SearchBoardType::class, $data);
        $form->handleRequest($request);
    
        $message = $this->entityManager->getRepository(Message::class)->findAll();

        $boards = $boardRepository->searchBoards($data);
    
        if ($user){
            $notifications = $notificationService->getNotifications($this->security);
        }else{
            $notifications = null;
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

    #[Route('/admin', name: 'app_dashboard_admin')]
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
            'notifications' => $this->notifications,
        ]);
    }

    /**
     * @param EntityManagerInterface $entityManager The entity manager to interact with the database
     *
     * @return Response  HTTP response: list of games page
     */
    #[Route('/admin/banmanager', name: 'app_dashboard_banmanager')]
    public function dashboard_banmanager(Request $request): Response
    {
        // Récupérer tous les utilisateurs à partir de votre source de données (par exemple, une entité User)
        $userRepository = $this->entityManager->getRepository(User::class);

        $data = new SearchUser();
        $form = $this->createForm(SearchUserType::class, $data);
        $form->handleRequest($request);
        $users = $userRepository->searchUsers($data);

        // Passer la liste des utilisateurs à votre modèle de vue
        return $this->render('platform/admin/banmanager.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
            'notifications' => $this->notifications, // Assurez-vous que vos notifications sont également disponibles dans ce contrôleur
        ]);
    }

    #[Route('/admin/attributerole/{user}/{role}', name: 'app_attribute_role')]
    public function attributerolemoderate(User $user, String $role): JsonResponse
    {
        $user->setRoles([$role]);
        $this->entityManager->flush();
        $this->addFlash('success-role', 'L\'utilisateur '. $user->getUsername(). ' a reçu un nouveau rôle');
        return new JsonResponse(['message' => 'Role attribuée avec succès']);
    }

}
