<?php

namespace App\Controller\Platform;

use App\Form\Platform\SearchBoardType;
use App\Repository\Platform\BoardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardUserController extends AbstractController
{
    #[Route('/dashboard/user', name: 'app_dashboard_user', methods: ['GET'])]
    public function index(Request $request, Security $security): Response
    {
        $boards = $security->getUser()->getBoards();
        $form = $this->createForm(SearchBoardType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->render('platform/dashboard_user/index.html.twig', [
            'boards' => $boards,
            'searchboard' => $form->createView(),
        ]);
    }
    #[Route('/dashboard/tables', name: 'app_dashboard_tables', methods: ['GET'])]
    public function tables(BoardRepository $boardRepository): Response
    {
        $boards = $boardRepository->findAll();

        return $this->render('platform/dashboard_tables/index.html.twig', [
            'boards' => $boards,
        ]);
    }
}
