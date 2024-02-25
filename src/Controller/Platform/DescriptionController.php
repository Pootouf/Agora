<?php

namespace App\Controller\Platform;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Platform\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DescriptionController extends AbstractController
{
    #[Route('/description/{game_id}', name: 'app_game_description', requirements: ['game_id' => '\d+'], methods: ['GET', 'HEAD'])]
    public function index(EntityManagerInterface $entityManager, int $game_id): Response
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
