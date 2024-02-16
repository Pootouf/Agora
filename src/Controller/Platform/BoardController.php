<?php

namespace App\Controller\Platform;



use App\Entity\Platform\Game;
use App\Entity\Platform\Board;
use App\Form\Platform\BoardRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoardController extends AbstractController
{
    #[Route('/boardCreation/{game_id}', name: 'app_board_create', requirements: ['game_id' => '\d+'], methods: ['GET', 'HEAD'])]
    public function create($game_id, EntityManagerInterface $manager): Response
    {
        //Find the game with the id passed in parameters
        $game = $manager->getRepository(Game::class)->find($game_id);
        //create a board
        $board = new Board(4);
        $form = $this->createForm(BoardRegistrationType::class, $board, [
            'game' => $game]
        );
        return $this->render('platform/game/boardRegister.html.twig', [
            'game' => $game,
            'form' => $form->createView()

        ]);
    }
}