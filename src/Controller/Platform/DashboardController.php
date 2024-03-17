<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Board;
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
            //TODO: Change this to IN_GAME
            $currentBoards = $userRepository->findBoardsByUserAndStatus($userId, "IN_GAME");
            $pastBoards = $userRepository->findBoardsByUserAndStatus($userId, "WAITING");
        }
        else {
            $favGames = null;
            $boards = null;
        }
        return $this->render('platform/dashboard/profile.html.twig', [
            'controller_name' => 'DashboardController',
            'favgames' => $favGames,
            'current_boards'=> $currentBoards,
            'past_boards' => $pastBoards
        ]);
    }
    #[Route('/dashboard/settings', name: 'app_dashboard_settings')]
    public function settings(): Response
    {
        return $this->render('platform/dashboard/settings.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
