<?php

namespace App\Controller\Game;

use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\Splendor\GameSPL;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\GameManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class GameTestController extends AbstractController
{

    private GameManagerService $gameService;

    public function __construct(GameManagerService $gameService) {
        $this->gameService = $gameService;
    }

    #[Route('/game/sixqp/list', name: 'app_game_sixqp_list')]
    public function listSixQPGames(GameSixQPRepository $gameSixQPRepository): Response
    {
        $games = $gameSixQPRepository->findAll();

        return $this->render('Game/Six_qp/GameTest/list_games.twig', [
            'games' => $games
        ]);
    }

    #[Route('/game/sixqp/create', name: 'app_game_sixqp_create')]
    public function createSixQPGame(): Response
    {
        $this->gameService->createGame(AbstractGameManagerService::$SIXQP_LABEL);
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







    #[Route('/game/splendor/list', name: 'app_game_splendor_list')]
    public function listSPLGames(GameSixQPRepository $gameSixQPRepository): Response
    {
        $games = $gameSixQPRepository->findAll();

        return $this->render('Game/Splendor/GameTest/list_games.twig', [
            'games' => $games
        ]);
    }

    #[Route('/game/splendor/create', name: 'app_game_splendor_create')]
    public function createSplendorGame(): Response
    {
        $this->gameService->createGame(AbstractGameManagerService::$SPL_LABEL);
        return $this->redirectToRoute('app_game_splendor_list');
    }

    #[Route('/game/splendor/join/{id}', name: 'app_game_splendor_join')]
    public function joinSplendorGame(GameSPL $gameSPL): Response
    {
        $user = $this->getUser();
        $this->gameService->joinGame($gameSPL->getId(), $user);
        return $this->redirectToRoute('app_game_splendor_list');
    }

    #[Route('/game/splendor/leave/{id}', name: 'app_game_splendor_quit')]
    public function quitSplendorGame(GameSPL $gameSPL): Response
    {
        $user = $this->getUser();
        $this->gameService->quitGame($gameSPL->getId(), $user);
        return $this->redirectToRoute('app_game_splendor_list');
    }

    #[Route('/game/splendor/delete/{id}', name: 'app_game_splendor_delete')]
    public function deleteSplendorGame(GameSPL $gameSPL): Response
    {
        $this->gameService->deleteGame($gameSPL->getId());
        return $this->redirectToRoute('app_game_splendor_list');
    }

    #[Route('/game/splendor/launch/{id}', name: 'app_game_splendor_launch')]
    public function launchSplendorGame(GameSPL $gameSPL): Response
    {
        $this->gameService->launchGame($gameSPL->getId());
        return $this->redirectToRoute('app_game_splendor_list');
    }
}
