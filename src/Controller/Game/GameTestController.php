<?php

namespace App\Controller\Game;

use App\Entity\Game\SixQP\GameSixQP;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Service\Game\AbstractGameService;
use App\Service\Game\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class GameTestController extends AbstractController
{

    private GameService $gameService;

    public function __construct(GameService $gameService) {
        $this->gameService = $gameService;
    }

    #[Route('/game/sixqp/list', name: 'app_game_sixqp_list')]
    public function listSixQPGames(GameSixQPRepository $gameSixQPRepository): Response
    {
        $games = $gameSixQPRepository->findAll();

        return $this->render('Game/GameTest/list_games.twig', [
            'games' => $games
        ]);
    }

    #[Route('/game/sixqp/create', name: 'app_game_sixqp_create')]
    public function createSixQPGame(): Response
    {
        $this->gameService->createGame(AbstractGameService::$SIXQP_LABEL);
        return $this->redirectToRoute('app_game_sixqp_list');
    }

    #[Route('/game/sixqp/join/{id}', name: 'app_game_sixqp_join')]
    public function joinSixQPGame(GameSixQP $gameSixQP): Response
    {
        $user = $this->getUser();
        $this->gameService->joinGame($gameSixQP->getId(), $user);
        return $this->redirectToRoute('app_game_sixqp_list');
    }

    #[Route('/game/sixqp/leave/{id}', name: 'app_game_sixqp_quit')]
    public function quitSixQPGame(GameSixQP $gameSixQP): Response
    {
        $user = $this->getUser();
        $this->gameService->quitGame($gameSixQP->getId(), $user);
        return $this->redirectToRoute('app_game_sixqp_list');
    }

    #[Route('/game/sixqp/delete/{id}', name: 'app_game_sixqp_delete')]
    public function deleteSixQPGame(GameSixQP $gameSixQP): Response
    {
        $this->gameService->deleteGame($gameSixQP->getId());
        return $this->redirectToRoute('app_game_sixqp_list');
    }

    #[Route('/game/sixqp/launch/{id}', name: 'app_game_sixqp_launch')]
    public function launchSixQPGame(GameSixQP $gameSixQP): Response
    {
        $this->gameService->launchGame($gameSixQP->getId());
        return $this->redirectToRoute('app_game_sixqp_list');
    }
}
