<?php

namespace App\Controller\Game;

use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\GameService;
use App\Service\Game\SixQPService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class GameController extends AbstractController
{

    private GameService $gameService;
    private SixQPService $sixQPService;

    public function __construct(GameService $gameService, SixQPService $sixQPService)
    {
        $this->gameService = $gameService;
        $this->sixQPService = $sixQPService;
    }

    #[Route('/game/{id}', name: 'app_game_show')]
    public final function showGame(GameSixQPRepository $gameSixQPRepository,
        PlayerSixQPRepository $playerSixQPRepository,
        int $id): Response
    {
        $game = $gameSixQPRepository->findOneBy(['id' => $id]);
        if ($game != null) {
            $player = $this->gameService->getPlayerFromUser($this->getUser(),
                $game->getId(),
                $playerSixQPRepository);
            $chosenCards = array_map(function (PlayerSixQP $player) {
                return $player->getChosenCardSixQP();}, $game->getPlayerSixQPs()->toArray());
            return $this->render('/Game/Six_qp/index.html.twig', [
                'chosenCards' => $chosenCards,
                'playerCards' => $player->getCards(),
                'playersNumber' => count($game->getPlayerSixQPs()),
                'ranking' => $this->sixQPService->getRanking($game),
                'player' => $player,
                'createdAt' => time(),
                'rows' => $game->getRowSixQPs(),
            ]);
        }

        return $this->redirectToRoute('/');

    }

    public function publish(HubInterface $hub, string $route, Response $data): Response
    {
        $update = new Update(
            $this->generateUrl($route),
            html_entity_decode($data->getContent())
        );
        $hub->publish($update);

        return new Response('published!');
    }
}
