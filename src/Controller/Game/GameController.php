<?php

namespace App\Controller\Game;

use App\Entity\Game\SixQP\PlayerSixQP;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\GameManagerService;
use App\Service\Game\SixQP\SixQPService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{

    private SixQPService $sixQPService;
    private ChosenCardSixQPRepository $chosenCardSixQPRepository;

    public function __construct(SixQPService $sixQPService, ChosenCardSixQPRepository $chosenCardSixQPRepository)
    {
        $this->sixQPService = $sixQPService;
        $this->chosenCardSixQPRepository = $chosenCardSixQPRepository;
    }

    #[Route('/game/{id}', name: 'app_game_show')]
    public final function showGame(GameSixQPRepository $gameSixQPRepository,
        PlayerSixQPRepository $playerSixQPRepository,
        int $id): Response
    {
        $game = $gameSixQPRepository->findOneBy(['id' => $id]);
        if ($game != null) {
            $player = $this->sixQPService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
            $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
            return $this->render('/Game/Six_qp/index.html.twig', [
                'game' => $game,
                'chosenCards' => $chosenCards,
                'playerCards' => $player->getCards(),
                'playersNumber' => count($game->getPlayerSixQPs()),
                'ranking' => $this->sixQPService->getRanking($game),
                'player' => $player,
                'createdAt' => time(),
                'rows' => $game->getRowSixQPs(),
                'isGameFinished' => $this->sixQPService->isGameEnded($game)
            ]);
        }

        return $this->redirectToRoute('/');

    }

    protected function getURLFromRoute(string $route, array $parameters): string
    {
        return $this->generateUrl($route, $parameters);
    }
}
