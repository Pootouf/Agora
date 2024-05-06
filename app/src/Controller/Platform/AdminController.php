<?php

namespace App\Controller\Platform;

use App\Data\SearchData;
use App\Entity\Platform\User;
use App\Entity\Game\Message;
use App\Repository\Platform\BoardRepository;
use App\Form\Platform\GenerateAccountsType;
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

    #[Route('/admin', name: 'app_dashboard_admin')]
    public function index(): Response
    {
        return $this->render('platform/admin/index.html.twig');
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
        }
        return $this->render('platform/admin/tableadmin.html.twig', [
            'boards' => $boards,
            'searchboard' => $form->createView(),
            'messages' => $message,
            'notifications' => $this->notifications,
        ]);
    }

}
