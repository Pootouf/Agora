<?php

namespace App\Controller\Platform;

use App\Data\SearchData;
use App\Form\Platform\SearchBoardType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\Platform\BoardRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(Request $request, BoardRepository $boardRepository): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchBoardType::class, $data);
        $form->handleRequest($request);
        $boards = $boardRepository->searchBoards($data);

        return $this->render('platform/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'boards' => $boards,
            'searchboard' => $form->createView(),
        ]);
    }
    #[Route('/dashboard/profile', name: 'app_dashboard_profile', methods: ['GET'])]
    public function profile(Request $request, BoardRepository $boardRepository): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchBoardType::class, $data);
        $form->handleRequest($request);
        $boards = $boardRepository->searchBoards($data);

        return $this->render('platform/dashboard/profile.html.twig', [
            'controller_name' => 'DashboardController',
            'boards' => $boards,
            'searchboard' => $form->createView(),
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
