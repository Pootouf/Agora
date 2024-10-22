<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Game\Log;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GameController extends AbstractController
{



     /**
     * Displays a list of games (when the user is not connected).
     *
     * This method retrieves all games from the database using the entity manager
     *
     * @param EntityManagerInterface $entityManager The entity manager to interact with the database
     *
     * @return Response  HTTP response: list of games page
     */
    #[Route('/games', name: 'app_games')]
    public function game_index(EntityManagerInterface $entityManager): Response
    {
        $gameRepository = $entityManager->getRepository(Game::class);

        $games = $gameRepository->findAll();

        return $this->render('platform/home/games.html.twig', [
            'controller_name' => 'GameController',
            'games' => $games,
        ]);
    }



    /**
     * Fetches the game information from the database based on the provided game ID (when the user is not connected)
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
            $this->addFlash('warning', 'Le jeu n\'existe pas');
            return $this->redirectToRoute('app_games');
        }

        return $this->render('platform/home/description.html.twig', [
            'game' => $game,
        ]);
    }


    #[Route('/game/{game_id}/addfavorite', name: 'app_game_favorite', requirements: ['game_id' => '\d+'], methods: ['GET', 'POST'])]
    public function addFavoriteGame(int $game_id, EntityManagerInterface $entityManager, Security $security): Response
    {

        $gameRepository = $entityManager->getRepository(Game::class);
        $game = $gameRepository->find($game_id);

        if(!$game) {
            $this->addFlash('warning', 'Le jeu n\'existe pas');
            return $this->redirectToRoute('app_dashboard_games');
        }
        $user = $security->getUser();
        if ($user) {
            //            Add game as favorite game
            if(!$user->getFavoriteGames()->contains($game)) {
                $user->addFavoriteGame($game);
                $entityManager->flush();
                $this->addFlash('success', 'Le jeu '. $game->getName() . ' a été ajouté à vos favoris.');
            } else {
                $this->addFlash('warning', 'Le jeu '. $game->getName() . ' a déja été ajouté à vos favoris.');
            }
        } else {
            $this->addFlash('warning', 'Le joueur n\'est pas connecté.');
        }

        return $this->redirectToRoute('app_dashboard_games');
    }

    #[Route('/game/{game_id}/removefavorite', name: 'app_game_remove_favorite', requirements: ['game_id' => '\d+'], methods: ['GET', 'POST'])]
    public function removeFavoriteGame(int $game_id, EntityManagerInterface $entityManager, Security $security): Response
    {
        $gameRepository = $entityManager->getRepository(Game::class);
        $game = $gameRepository->find($game_id);

        if (!$game) {
            $this->addFlash('warning', 'Le jeu n\'existe pas');
            return $this->redirectToRoute('app_dashboard_games');
        }

        $user = $security->getUser();
        if ($user) {
            // Remove the game from the user's favorite games
            if ($user->getFavoriteGames()->contains($game)) {
                $user->removeFavoriteGame($game);
                $entityManager->flush();
                $this->addFlash('success', 'Le jeu ' . $game->getName() . ' a été retiré de vos favoris.');
            } else {
                $this->addFlash('warning', 'Le jeu ' . $game->getName() . ' n\'est pas dans vos favoris.');
            }
        } else {
            $this->addFlash('warning', 'Le joueur n\'est pas connecté.');
        }

        return $this->redirectToRoute('app_dashboard_games');
    }

    #[Route('/game/{gameId}/logs', name: 'game_logs')]
    public function showGameLogs(int $gameId,EntityManagerInterface $entityManager): Response
    {
        $logs = $entityManager->getRepository(Log::class)->findBy(['gameId' => $gameId]);

        return $this->render('platform/dashboard/games/logs.html.twig', [
            'logs' => $logs,
        ]);
    }
}

