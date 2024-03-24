<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Board;
use App\Entity\Platform\Notification;
use App\Entity\Platform\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('platform/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
    #[Route('/dashboard/profile', name: 'app_dashboard_profile')]
    public function profile(EntityManagerInterface $entityManager, Security $security): Response
    {
        if($security->getUser()) {
            $userId = $security->getUser()->getId();
            $userRepository = $entityManager->getRepository(Board::class);
            $favGames = $security->getUser()->getFavoriteGames();
            $currentBoards = $userRepository->findBoardsByUserAndStatus($userId, "IN_GAME");
            $pastBoards = $userRepository->findBoardsByUserAndStatus($userId, "WAITING");
            $user = $security->getUser();
            $notifications = $entityManager->getRepository(Notification::class)
                ->findBy(
                    ['receiver' => $user],
                    ['createdAt' => 'DESC']
                );
        }
        else {
            $favGames = null;
            $boards = null;
            $notifications = null;
        }
        return $this->render('platform/dashboard/profile.html.twig', [
            'controller_name' => 'DashboardController',
            'fav_games' => $favGames,
            'current_boards'=> $currentBoards,
            'past_boards' => $pastBoards,
            'notifications' => $notifications
        ]);
    }

    #[Route('/dashboard/profile/{user_id}', name: 'app_other_user_profile', requirements: ['user_id' => '\d+'])]
    public function getUserProfile(EntityManagerInterface $entityManager, Security $security, int $user_id) : Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $boardRepository = $entityManager->getRepository(Board::class);

        $user = $userRepository->find($user_id);

        $favGames = $user->getFavoriteGames();
        $currentBoards = $boardRepository->findBoardsByUserAndStatus($user_id, "IN_GAME");
        $pastBoards = $boardRepository->findBoardsByUserAndStatus($user_id, "WAITING");

        return $this->render('platform/dashboard/profile.html.twig', [
            'controller_name' => 'DashboardController',
            'fav_games' => $favGames,
            'current_boards'=> $currentBoards,
            'past_boards' => $pastBoards,
            'userProfile' => $user,
            'notifications' => null
        ]);
    }

    #[Route('/dashboard/settings', name: 'app_dashboard_settings')]
    public function settings(): Response
    {
        return $this->render('platform/users/editUserProfile.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
