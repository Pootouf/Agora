<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Game;
use App\Entity\Platform\Board;
use App\Entity\Platform\User;
use App\Form\Platform\SearchBoardType;
use App\Repository\Platform\BoardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TableController extends AbstractController
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
    public function tables(Security $security, Request $request, BoardRepository $boardRepository, EntityManagerInterface $entityManager): Response
    {
        $currentUserID = null;
        if ($security->getUser() !== null) {
            $currentUserID = $security->getUser()->getId();
        }

        $boards = $boardRepository->findAll();
        $form = $this->createForm(SearchBoardType::class);
        $form->handleRequest($request);

        return $this->render('platform/dashboard_tables/index.html.twig', [
            'boards' => $boards,
            'searchboard' => $form->createView(),
            'currentUserID' => $currentUserID,
            'controller_name' => 'TableController',
        ]);
    }

    #[Route('/boardCreation/{game_id}', name: 'app_board_create', requirements: ['game_id' => '\d+'], methods: ['GET', 'POST', 'HEAD'])]
    public function create(Request $request, $game_id, EntityManagerInterface $manager, Security $security): Response
    {
        $game = $manager->getRepository(Game::class)->find($game_id);
        $board = new Board();
        $form = $this->createForm(BoardRegistrationType::class, $board, [
            'game' => $game
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $board->setCreationDate(new \DateTime());
            $board->setInvitationTimer(new \DateTime());
            $board->setInactivityTimer(new \DateTime());
            $userId = $security->getUser()->getId();
            $user = $manager->getRepository(User::class)->find($userId);
            $user->addBoard($board);

            $manager->persist($board);
            $manager->flush();

            $this->addFlash(
                'success',
                'La table à bien été créé ! Lien d\'invitation : '
            );

            return $this->redirectToRoute('app_home');
        }
        return $this->render('platform/game/boardRegister.html.twig', [
            'game' => $game,
            'form' => $form->createView()
        ]);
    }

    #[Route('/joinBoard/{id}', name: 'app_join_board')]
    public function joinBoardController(int $id, EntityManagerInterface $entityManager, Security $security): Response
    {
        $board = $entityManager->getRepository(Board::class)->find($id);
        $userId = $security->getUser()->getId();
        $user = $entityManager->getRepository(User::class)->find($userId);
        $boardStatus = $board->getStatus();
        $boardMaxUser = $board->getNbUserMax();
        $boardUsers = $board->getListUsers();
        $boardUserNb = $boardUsers->count();

        if ($boardStatus === "IN_GAME" || $boardStatus === "FINISH" || $boardUserNb == $boardMaxUser) {
            $errorMessage = "Impossible de rejoindre la table";
            $this->addFlash('warning', $errorMessage);
            return $this->redirectToRoute('/dashboard/user');
        }

        $board->addListUser($user);
        $this->addFlash('success', 'Bienvenue sur cette table');
        $entityManager->persist($board);
        $entityManager->flush();
        return $this->redirectToRoute('dashboard/user');
    }

    #[Route('/leaveBoard/{id}', name: 'app_leave_board')]
    public function leaveBoardController(int $id, EntityManagerInterface $entityManager, Security $security): Response
    {
        $board = $entityManager->getRepository(Board::class)->find($id);
        $userId = $security->getUser()->getId();
        $user = $entityManager->getRepository(User::class)->find($userId);
    
        if ($board->getListUsers()->contains($user)) {
            $board->getListUsers()->removeElement($user); // Correction ici
            $this->addFlash('success', 'Vous avez quitté la table.');
            $entityManager->persist($board);
            $entityManager->flush();
        } else {
            $this->addFlash('warning', 'Vous n\'êtes pas dans cette table.');
        }

        return $this->redirectToRoute('dashboard/tables');
    }

}

