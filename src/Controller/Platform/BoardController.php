<?php

namespace App\Controller\Platform;



use App\Entity\Platform\Game;
use App\Entity\Platform\Board;
use App\Form\Platform\BoardRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BoardController extends AbstractController
{
    #[Route('/boardCreation/{game_id}', name: 'app_board_create', requirements: ['game_id' => '\d+'], methods: ['GET', 'HEAD'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, $game_id, EntityManagerInterface $manager): Response
    {
        //Find the game with the id passed in parameters
        $game = $manager->getRepository(Game::class)->find($game_id);
        //create a board
        $board = new Board(4);
        $form = $this->createForm(BoardRegistrationType::class, $board, [
            'game' => $game]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $board->$form->getData();
            $manager->persist($board);
            $manager->flush();

            return $this->redirectToRoute('app_home');
        }
        dump($game->getMinPlayers());
        dump($game->getMaxPlayers());
        dump((string) $form->getErrors());     // Main errors

        return $this->render('platform/game/boardRegister.html.twig', [
            'game' => $game,
            'form' => $form->createView()
        ]);
    }
}