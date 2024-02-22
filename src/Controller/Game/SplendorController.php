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
            'playerBoughtCards' => $this->SPLService->getPurchasedCards($player),
            'playerReservedCards' => $this->SPLService->getReservedCards($player),
            'playerTokens' => $player->getPersonalBoard()->getTokens(),
            'drawCardsLevelOneCount' => $this->SPLService->getDrawCardsByLevel(DrawCardsSPL::$LEVEL_ONE, $game)->count(),
            'drawCardsLevelTwoCount' => $this->SPLService->getDrawCardsByLevel(DrawCardsSPL::$LEVEL_TWO, $game)->count(),
            'drawCardsLevelThreeCount' => $this->SPLService->getDrawCardsByLevel(DrawCardsSPL::$LEVEL_THREE, $game)->count(),
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
             $message = $player->getUsername() . " tried to pick a token of " . $tokenSPL->getColor()
                 . " but could not ";
             $this->logService->sendPlayerLog($gameSPL, $player, $message);
             return new Response('Impossible to choose', Response::HTTP_INTERNAL_SERVER_ERROR);
         }
         $message = $player->getUsername() . " picked a token of " . $tokenSPL->getColor();
         $this->logService->sendPlayerLog($gameSPL, $player, $message);
         //TODO publish(s)
         $this->entityManager->persist($gameSPL);
         $this->entityManager->persist($tokenSPL);
         $this->entityManager->persist($player);
         $this->entityManager->flush();
         if ($this->tokenSPLService->mustEndPlayerRoundBecauseOfTokens($player)) {
             $this->tokenSPLService->validateTakingOfTokens($player);
             $this->tokenSPLService->clearSelectedTokens($player);
             $this->manageEndOfRound($gameSPL);
         }
         return new Response('token picked', Response::HTTP_OK);
     }

     /**
      * manageEndOfRound: if the game must end then end the game
      *     else end active player's round
      * @param GameSPL $gameSPL
      * @return void
      */
     private function manageEndOfRound(GameSPL $gameSPL): void
     {
         if ($this->SPLService->isGameEnded($gameSPL)) {
             //TODO call ranking and print end of game
         } else {
             $activePlayer = $this->SPLService->getActivePlayer($gameSPL);
             $this->SPLService->endRoundOfPlayer($gameSPL, $activePlayer);
             $this->entityManager->persist($gameSPL);
             $this->entityManager->persist($activePlayer);
             $this->entityManager->flush();
             // TODO notif next player
         }
     }
}
