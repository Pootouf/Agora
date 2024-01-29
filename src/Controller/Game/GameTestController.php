<?php

namespace App\Controller\Game;

use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Service\Game\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameTestController extends AbstractController
{

    private GameService $gameService;

    public function __construct(GameService $gameService) {
        $this->gameService = $gameService;
    }

    #[Route('/game/sixqp/list', name: 'app_game_sixqp_list')]
    public function index(GameSixQPRepository $gameSixQPRepository): Response
    {
        $games = $gameSixQPRepository->findAll();

        return $this->render('Game/GameTest/list_games.twig', [
            'games' => $games
        ]);
    }
}
