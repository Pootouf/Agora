<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Game;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * Displays a list of games.
     *
     * This method retrieves all games from the database using the entity manager
     *
     * @param EntityManagerInterface $entityManager The entity manager to interact with the database
     *
     * @return Response  HTTP response: list of games page
    */
    #[Route('/games', name: 'app_games')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $gameRepository = $entityManager->getRepository(Game::class);

        $games = $gameRepository->findAll();

        return $this->render('platform/game/games.html.twig', [
            'controller_name' => 'GameController',
            'games' => $games,
        ]);
    }

    /**
     * Fetches the game information from the database based on the provided game ID
     * 
     * @param EntityManagerInterface $entityManager The entity manager to interact with the database
     * @param int @game_id ID of the game to displayed on description page
     * 
     * @return Response HTTP response: game description by ID page
     */
    #[Route('/games/{game_id}', name: 'app_game_description', requirements: ['game_id' => '\d+'], methods: ['GET', 'HEAD'])]
    public function game_description(EntityManagerInterface $entityManager, int $game_id): Response
    {
        $gameRepository = $entityManager->getRepository(Game::class);
        $game = $gameRepository->find($game_id);

        if(!$game) {
            throw $this->createNotFoundException('Le jeu n\'existe pas');
        }

        return $this->render('platform/game/description.html.twig', [
            'game' => $game,
        ]);
    }
}

