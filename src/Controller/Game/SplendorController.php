<?php

namespace App\Controller\Game;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\LogService;
use App\Service\Game\SixQP\SixQPService;
use App\Service\Game\PublishService;
use App\Service\Game\Splendor\SPLService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class SplendorController extends AbstractController
{
    private SPLService $service;
    private EntityManagerInterface $entityManager;
    private PublishService $publishService;
    private LogService $logService;

    public function __construct(EntityManagerInterface $entityManager,
                                ChosenCardSixQPRepository $chosenCardSixQPRepository,
                                SPLService $service,
                                LogService $logService,
                                PublishService $publishService)
    {
        $this->publishService = $publishService;
        $this->entityManager = $entityManager;
        $this->logService = $logService;
        $this->service = $service;
    }

    #[Route('/game/splendor/{id}', name: 'app_game_show_spl')]
    public function showGame(GameSPL $game): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        $isSpectator = false;
        if ($player == null) {
            $player = $game->getPlayers()->get(0);
            $isSpectator = true;
        } else {
            //TODO : display the game
        }
        return $this->render('/Game/Six_qp/index.html.twig', [
            'game' => $game,
            'playerBoughtCards' => $player->getCards(),
            //'playerReservedCards' => $this->service->getReservedCards($player),
            'playerTokens' => $player->getPersonalBoard()->getTokens(),
            'drawCardsLevelOneCount' => $game->getMainBoard()->getDrawCards()->get(DrawCardsSPL::$LEVEL_ONE),
            'drawCardsLevelTwoCount' => $game->getMainBoard()->getDrawCards()->get(DrawCardsSPL::$LEVEL_TWO),
            'drawCardsLevelThreeCount' => $game->getMainBoard()->getDrawCards()->get(DrawCardsSPL::$LEVEL_THREE),
            'rows' => $game->getMainBoard()->getRowsSPL(),
            'playersNumber' => count($game->getPlayers()),
            'ranking' => $this->service->getRanking($game),
            'player' => $player,
            'isGameFinished' => $this->service->isGameEnded($game),
            'nobleTiles' => $game->getMainBoard()->getNobleTiles(),
            'isSpectator' => $isSpectator,
            //find a way to have development cards' jewels count, tokens count per color and per player for ranking
        ]);

    }
}
