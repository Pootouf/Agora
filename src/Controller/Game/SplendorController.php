<?php

namespace App\Controller\Game;

use AllowDynamicProperties;
use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
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
            'playerReservedCards' => $this->SPLService->getReserveCards($player),
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
            'needToPlay' => $needToPlay,
            'selectedCard' => null,
            'levelCard' => null,
            'selectedReservedCard' => null,
        ]);
    }

     #[Route('/game/{idGame}/splendor/select/board/{idCard}', name: 'app_game_splendor_select_from_board')]
     public function selectCardFromBoard(
         #[MapEntity(id: 'idGame')] GameSPL $game,
         #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card): Response
     {
         return $this->render('Game/Splendor/MainBoard/cardActions.html.twig',
         [
             'selectedCard' => $card,
             'levelCard' => null,
             'game' => $game,
             'selectedReservedCard' => null,
         ]);
     }

     #[Route('/game/{idGame}/splendor/select/draw/{level}', name: 'app_game_splendor_select_from_draw')]
     public function selectCardFromDraw(
         #[MapEntity(id: 'idGame')] GameSPL $game, int $level): Response
     {
         return $this->render('Game/Splendor/MainBoard/cardActions.html.twig',
             [
                 'levelCard' => $level,
                 'selectedCard' => null,
                 'game' => $game,
                 'selectedReservedCard' => null,
             ]);
     }

     #[Route('/game/{idGame}/splendor/select/reserved/{idCard}', name: 'app_game_splendor_select_from_personal_board')]
     public function selectCardFromPersonalBoard(
         #[MapEntity(id: 'idGame')] GameSPL $game,
         #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card): Response
     {
         return $this->render('Game/Splendor/MainBoard/cardActions.html.twig',
             [
                 'selectedCard' => null,
                 'levelCard' => null,
                 'game' => $game,
                 'selectedReservedCard' => $card,
             ]);
     }
}
