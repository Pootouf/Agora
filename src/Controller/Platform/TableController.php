<?php

namespace App\Controller\Platform;

use Symfony\Bundle\SecurityBundle\Security;
use App\Form\Platform\SearchBoardType;
use App\Repository\Platform\BoardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TableController extends AbstractController
{
    #[Route('/table', name: 'app_table', methods: ['GET'])]
    public function index(Security $security, Request $request, BoardRepository $boardRepository): Response
    {

        $currentUserID = null;
        if ($security->getUser() !== null) {
            $currentUserID = $security->getUser()->getId();
        }

        $boards = $boardRepository->findAll();
        $form = $this->createForm(SearchBoardType::class);
        $form->handleRequest($request);

        return $this->render('platform/table/table.html.twig', [
            'boards' => $boards,
            'searchboard' => $form->createView(),
            'currentUserID' => $currentUserID,
            'controller_name' => 'TableController',
        ]);
    }
}
