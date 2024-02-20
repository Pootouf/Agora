<?php

namespace App\Controller\Game;

use AllowDynamicProperties;
use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\LogService;
use App\Service\Game\SixQP\SixQPService;
use App\Service\Game\PublishService;
use App\Service\Game\Splendor\SPLService;
use App\Service\Game\Splendor\TokenSPLService;
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


    public function __construct(private EntityManagerInterface $entityManager,
                                private TokenSPLService $tokenSPLService,
                                private SPLService $SPLService,
                                private LogService $logService,
                                private PublishService $publishService)
    {}

    #[Route('/game/splendor/{id}', name: 'app_game_show_spl')]
    public function showGame(GameSPL $game): Response
    {
        $player = $this->SPLService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        $isSpectator = false;
        $needToPlay = false;
        if ($player == null) {
            $player = $game->getPlayers()->get(0);
            $isSpectator = true;
        } else {
            //TODO : display the game
        }
        $mainBoardTokens = $game->getMainBoard()->getTokens();
        return $this->render('/Game/Splendor/index.html.twig', [
            'game' => $game,
            'playerBoughtCards' => $player->getPersonalBoard()->getPlayerCards(), //TODO: separate reserved and bought cards
            //'playerReservedCards' => $this->service->getReservedCards($player),
            'playerTokens' => $player->getPersonalBoard()->getTokens(),
            'drawCardsLevelOneCount' => $game->getMainBoard()->getDrawCards()->get(DrawCardsSPL::$LEVEL_ONE)->getDevelopmentCards()->count(),
            'drawCardsLevelTwoCount' => $game->getMainBoard()->getDrawCards()->get(DrawCardsSPL::$LEVEL_TWO)->getDevelopmentCards()->count(),
            'drawCardsLevelThreeCount' => $game->getMainBoard()->getDrawCards()->get(DrawCardsSPL::$LEVEL_THREE)->getDevelopmentCards()->count(),
            'whiteTokensPile' => $this->tokenSPLService->getWhiteTokensFromCollection($mainBoardTokens),
            'redTokensPile' => $this->tokenSPLService->getRedTokensFromCollection($mainBoardTokens),
            'blueTokensPile' => $this->tokenSPLService->getBlueTokensFromCollection($mainBoardTokens),
            'greenTokensPile' => $this->tokenSPLService->getGreenTokensFromCollection($mainBoardTokens),
            'blackTokensPile' => $this->tokenSPLService->getBlackTokensFromCollection($mainBoardTokens),
            'yellowTokensPile' => $this->tokenSPLService->getYellowTokensFromCollection($mainBoardTokens),
            'rows' => $game->getMainBoard()->getRowsSPL(),
            'playersNumber' => count($game->getPlayers()),
            'ranking' => $this->SPLService->getRanking($game),
            'player' => $player,
            'isGameFinished' => $this->SPLService->isGameEnded($game),
            'nobleTiles' => $game->getMainBoard()->getNobleTiles(),
            'isSpectator' => $isSpectator,
            'needToPlay' => $needToPlay
        ]);

    }
    #[Route('/game/{idGame}/splendor/takeToken/{idToken}', name: 'app_game_splendor_selectToken')]
    public function takeToken(
        #[MapEntity(id: 'idGame')] GameSPL $gameSPL,
        #[MapEntity(id: 'idToken')] TokenSPL $tokenSPL): Response
    {
        $player = $this->SPLService->getPlayerFromNameAndGame($gameSPL, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        if ($this->SPLService->getActivePlayer($gameSPL) !== $player) {
            return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
        }
        try {
            $this->tokenSPLService->takeToken($player, $tokenSPL);
        } catch(Exception) {
            //TODO log
            return new Response('Impossible to choose', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        //TODO log
        //TODO publish(s)

        if ($this->tokenSPLService->mustEndPlayerRoundBecauseOfTokens($player)) {
            $this->tokenSPLService->validateTakingOfTokens($player);
            $this->tokenSPLService->clearSelectedTokens($player);
            $this->manageEndOfRound($gameSPL);
        }
        return new Response('token picked', Response::HTTP_OK);
    }

    private function manageEndOfRound(GameSPL $gameSPL): void
    {
        if ($this->SPLService->isGameEnded($gameSPL)) {
            //TODO call ranking and print end of game
        } else {
            $activePlayer = $this->SPLService->getActivePlayer($gameSPL);
            $this->SPLService->endRoundOfPlayer($gameSPL, $activePlayer);
        }
    }
}
