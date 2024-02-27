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
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\LogService;
use App\Service\Game\PublishService;
use App\Service\Game\Splendor\SPLService;
use App\Service\Game\Splendor\TokenSPLService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Symfony\Component\Translation\t;

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
            $needToPlay = $player->isTurnOfPlayer();
        }
        $mainBoardTokens = $game->getMainBoard()->getTokens();
        return $this->render('/Game/Splendor/index.html.twig', [
            'game' => $game,
            'playerBoughtCards' => $this->SPLService->getPurchasedCards($player),
            'playerReservedCards' => $this->SPLService->getReservedCards($player),
            'playerTokens' => $player->getPersonalBoard()->getTokens(),
            'drawCardsLevelOneCount' => $this->SPLService
                    ->getDrawCardsByLevel(SplendorParameters::$DRAW_CARD_LEVEL_ONE, $game)
                    ->count(),
            'drawCardsLevelTwoCount' => $this->SPLService
                    ->getDrawCardsByLevel(SplendorParameters::$DRAW_CARD_LEVEL_TWO, $game)
                    ->count(),
            'drawCardsLevelThreeCount' => $this->SPLService
                    ->getDrawCardsByLevel(SplendorParameters::$DRAW_CARD_LEVEL_THREE, $game)
                    ->count(),
            'whiteTokensPile' => $this->tokenSPLService
                    ->getWhiteTokensFromCollection($mainBoardTokens),
            'redTokensPile' => $this->tokenSPLService
                    ->getRedTokensFromCollection($mainBoardTokens),
            'blueTokensPile' => $this->tokenSPLService
                    ->getBlueTokensFromCollection($mainBoardTokens),
            'greenTokensPile' => $this->tokenSPLService
                    ->getGreenTokensFromCollection($mainBoardTokens),
            'blackTokensPile' => $this->tokenSPLService
                    ->getBlackTokensFromCollection($mainBoardTokens),
            'yellowTokensPile' => $this->tokenSPLService
                    ->getYellowTokensFromCollection($mainBoardTokens),
            'rows' => $game->getMainBoard()->getRowsSPL(),
            'playersNumber' => count($game->getPlayers()),
            'ranking' => $game->getPlayers(),
            'player' => $player,
            'isGameFinished' => $this->SPLService->isGameEnded($game),
            'nobleTiles' => $game->getMainBoard()->getNobleTiles(),
            'isSpectator' => $isSpectator,
            'needToPlay' => $needToPlay,
            'selectedCard' => null,
            'levelCard' => null,
            'selectedReservedCard' => null,
            'purchasableCards' => $this->SPLService
                    ->getPurchasableCardsOnBoard($game, $player),
            'canReserveCard' => $this->SPLService->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player),
        ]);
    }

     #[Route('/game/{idGame}/splendor/select/board/{idCard}', name: 'app_game_splendor_select_from_board')]
     public function selectCardFromBoard(
         #[MapEntity(id: 'idGame')] GameSPL $game,
         #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card): Response
     {
         $player = $this->SPLService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         return $this->render('Game/Splendor/MainBoard/cardActions.html.twig',
         [
             'selectedCard' => $card,
             'levelCard' => null,
             'game' => $game,
             'selectedReservedCard' => null,
             'purchasableCards' => $this->SPLService->getPurchasableCardsOnBoard($game, $player),
             'canReserveCard' => $this->SPLService->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player),
         ]);
     }

     #[Route('/game/{idGame}/splendor/select/draw/{level}', name: 'app_game_splendor_select_from_draw')]
     public function selectCardFromDraw(
         #[MapEntity(id: 'idGame')] GameSPL $game, int $level): Response
     {
         $player = $this->SPLService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         return $this->render('Game/Splendor/MainBoard/cardActions.html.twig',
             [
                 'levelCard' => $level,
                 'selectedCard' => null,
                 'game' => $game,
                 'selectedReservedCard' => null,
                 'canReserveCard' => $this->SPLService->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player),
             ]);
     }

     #[Route('/game/{idGame}/splendor/select/reserved/{idCard}', name: 'app_game_splendor_select_from_personal_board')]
     public function selectCardFromPersonalBoard(
         #[MapEntity(id: 'idGame')] GameSPL $game,
         #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card): Response
     {
         $player = $this->SPLService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         return $this->render('Game/Splendor/MainBoard/cardActions.html.twig',
             [
                 'selectedCard' => null,
                 'levelCard' => null,
                 'game' => $game,
                 'selectedReservedCard' => $card,
                 'purchasableCards' => $this->SPLService->getPurchasableCardsOnBoard($game, $player),
                 'canReserveCard' => false,
             ]);
     }

     #[Route('/game/{idGame}/splendor/buy/card/{idCard}', name: 'app_game_splendor_buy_card')]
     public function buyCard(
         #[MapEntity(id: 'idGame')] GameSPL $game,
         #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card): Response
     {
         $player = $this->SPLService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         if ($player == null) {
             return new Response('Invalid player', Response::HTTP_FORBIDDEN);
         }
         if ($this->SPLService->getActivePlayer($game) !== $player) {
             return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
         }
         $playerCard = $this->SPLService->getPlayerCardFromDevelopmentCard($game, $card);
         if ($playerCard != null) {
             if ($player->getId() != $playerCard->getPersonalBoardSPL()->getPlayerSPL()->getId()) {
                 return new Response("Not player's card", Response::HTTP_FORBIDDEN);
             }
             if (!$playerCard->isIsReserved()) {
                 return new Response("The card is not reserved", Response::HTTP_FORBIDDEN);
             }
         }
         try {
             $this->SPLService->buyCard($player, $card);
         } catch (Exception $e) {
             return new Response("Not player's card", Response::HTTP_FORBIDDEN);
         }
         $this->SPLService->addBuyableNobleTilesToPlayer($game, $player);
         $this->publishNobleTiles($game);
         $this->manageEndOfRound($game);
         return new Response('Card Bought', Response::HTTP_OK);
     }

     #[Route('/game/{idGame}/splendor/reserve/card/{idCard}', name: 'app_game_splendor_reserve_card_row')]
     public function reserveCardOnRows(
         #[MapEntity(id: 'idGame')] GameSPL $game,
         #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card): Response
     {
         $player = $this->SPLService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         if ($player == null) {
             return new Response('Invalid player', Response::HTTP_FORBIDDEN);
         }
         if ($this->SPLService->getActivePlayer($game) !== $player) {
             return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
         }
         if (!$this->SPLService->canPlayerReserveCard($game, $card)) {
             return new Response("Can't reserve this card", Response::HTTP_FORBIDDEN);
         }
         try {
             $this->SPLService->reserveCard($player, $card);
         } catch (Exception $e) {
             return new Response("Can't reserve this card : " . $e->getMessage(), Response::HTTP_FORBIDDEN);
         }
         $this->manageEndOfRound($game);
         return new Response('Card reserved', Response::HTTP_OK);
     }

     #[Route('/game/{idGame}/splendor/reserve/draw/{level}', name: 'app_game_splendor_reserve_card_draw')]
     public function reserveCardOnDraw(
         #[MapEntity(id: 'idGame')] GameSPL $game,
         int $level): Response
     {
         $player = $this->SPLService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         if ($player == null) {
             return new Response('Invalid player', Response::HTTP_FORBIDDEN);
         }
         if ($this->SPLService->getActivePlayer($game) !== $player) {
             return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
         }
         $draw = $this->SPLService->getDrawFromGameAndLevel($game, $level);
         $card = $this->SPLService->getCardFromDraw($draw);
         if ($card == null || !$this->SPLService->canPlayerReserveCard($game, $card)) {
             return new Response("Can't reserve this card", Response::HTTP_FORBIDDEN);
         }
         try {
             $this->SPLService->reserveCard($player, $card);
         } catch (Exception $e) {
             return new Response("Can't reserve this card : " . $e->getMessage(), Response::HTTP_FORBIDDEN);
         }
         $this->manageEndOfRound($game);
         return new Response('Card reserved', Response::HTTP_OK);
     }

     #[Route('/game/{idGame}/splendor/cancelTokensSelection', name: 'app_game_splendor_cancel_tokens_selection')]
     public function cancelTokensSelection(
         #[MapEntity(id: 'idGame')] GameSPL $gameSPL): Response
     {
         $player = $this->SPLService->getPlayerFromNameAndGame($gameSPL, $this->getUser()->getUsername());
         if ($player == null) {
             return new Response('Invalid player', Response::HTTP_FORBIDDEN);
         }
         $this->tokenSPLService->clearSelectedTokens($player);
         $this->publishToken($gameSPL, $player);
         return new Response('Selected tokens cleaned', Response::HTTP_OK);
     }

     #[Route('/game/{idGame}/splendor/takeToken/{color}', name: 'app_game_splendor_selectToken')]
     public function takeToken(
         #[MapEntity(id: 'idGame')] GameSPL $gameSPL,
         string $color): Response
     {
         $player = $this->SPLService->getPlayerFromNameAndGame($gameSPL, $this->getUser()->getUsername());
         if ($player == null) {
             return new Response('Invalid player', Response::HTTP_FORBIDDEN);
         }
         if ($this->SPLService->getActivePlayer($gameSPL) !== $player) {
             return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
         }
         $tokenSPL = $this->tokenSPLService->getTokenOnMainBoardFromColor($gameSPL->getMainBoard(), $color);
         if ($tokenSPL == null) {
             return new Response("There is no more token of this color", Response::HTTP_FORBIDDEN);
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
         if ($this->tokenSPLService->mustEndPlayerRoundBecauseOfTokens($player)) {
             $this->tokenSPLService->validateTakingOfTokens($player);
             $this->manageEndOfRound($gameSPL);
         } else {
             foreach ($gameSPL->getPlayers() as $playerNotif) {
                 $this->publishToken($gameSPL, $playerNotif);
             }
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
             $this->publishEndOfGame($gameSPL);
         } else {
             $activePlayer = $this->SPLService->getActivePlayer($gameSPL);
             $this->SPLService->endRoundOfPlayer($gameSPL, $activePlayer);
             $this->entityManager->persist($gameSPL);
             $this->entityManager->persist($activePlayer);
             $this->entityManager->flush();

             foreach ($gameSPL->getPlayers() as $playerNotif) {
                 $this->publishToken($gameSPL, $playerNotif);
             }
             $this->publishDevelopmentCards($gameSPL);
             $this->publishRanking($gameSPL);
             $this->publishReservedCards($gameSPL);
         }
     }


    /**
     * publishToken : send a mercure notification regarding the tokens
     * @param GameSPL $game
     * @param PlayerSPL|null $player
     * @return void
     */
     private function publishToken(GameSPL $game, ?PlayerSPL $player): void
     {
         $mainBoardTokens = $game->getMainBoard()->getTokens();
         $response = $this->render('Game/Splendor/MainBoard/drawTokensPile.html.twig', [
             'whiteTokensPile' => $this->tokenSPLService->getWhiteTokensFromCollection($mainBoardTokens),
             'redTokensPile' => $this->tokenSPLService->getRedTokensFromCollection($mainBoardTokens),
             'blueTokensPile' => $this->tokenSPLService->getBlueTokensFromCollection($mainBoardTokens),
             'greenTokensPile' => $this->tokenSPLService->getGreenTokensFromCollection($mainBoardTokens),
             'blackTokensPile' => $this->tokenSPLService->getBlackTokensFromCollection($mainBoardTokens),
             'yellowTokensPile' => $this->tokenSPLService->getYellowTokensFromCollection($mainBoardTokens),
             'player' => $player,
             'needToPlay' => $player->isTurnOfPlayer(),
             'game' => $game,
        ]);

         $this->publishService->publish(
             $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'token'.$player->getId(),
             $response);
     }

    /**
     * publishDevelopmentCards : send a mercure notification to update the development cards for a spectator
     * @param GameSPL $game
     * @return void
     */
    private function publishDevelopmentCards(GameSPL $game): void
    {
        foreach ($game->getPlayers() as $player) {
            $this->publishDevelopmentCardsWithSelectedOptions($game, $player, false, $player->isTurnOfPlayer());
        }
        $this->publishDevelopmentCardsWithSelectedOptions($game, null, true, false);
    }

    /**
     * publishDevelopmentCardsWithSelectedOptions : send a mercure notification regarding the development cards on
     *                                              main board depending on if the user is spectator or a player who
     *                                              must play
     * @param GameSPL $game
     * @param PlayerSPL|null $player
     * @param bool $isSpectator
     * @param bool $needToPlay
     * @return void
     */
    private function publishDevelopmentCardsWithSelectedOptions(GameSPL $game, ?PlayerSPL $player, bool $isSpectator,
                                                                bool $needToPlay) : void
    {
        $response = $this->render('Game/Splendor/MainBoard/developmentCardsBoard.html.twig', [
            'rows' => $game->getMainBoard()->getRowsSPL(),
            'drawCardsLevelOneCount' => $this->SPLService
                ->getDrawCardsByLevel(SplendorParameters::$DRAW_CARD_LEVEL_ONE, $game)
                ->count(),
            'drawCardsLevelTwoCount' => $this->SPLService
                ->getDrawCardsByLevel(SplendorParameters::$DRAW_CARD_LEVEL_TWO, $game)
                ->count(),
            'drawCardsLevelThreeCount' => $this->SPLService
                ->getDrawCardsByLevel(SplendorParameters::$DRAW_CARD_LEVEL_THREE, $game)
                ->count(),
            'isSpectator' => $isSpectator,
            'needToPlay' => $needToPlay,
            'game' => $game,
            'purchasableCards' => $player == null ? [] : $this->SPLService
                ->getPurchasableCardsOnBoard($game, $player),
            'canReserveCard' => $player == null || $this->SPLService->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player),
        ]);

        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()])
            .'developmentCards'
            .($player == null ? 'spectator' : $player->getId()),
            $response);
    }

    /**
     * publishNobleTiles : send a mercure notification regarding noble tiles displayed on main board
     * @param GameSPL $game
     * @return void
     */
    private function publishNobleTiles(GameSPL $game): void
    {
        foreach (['player', 'spectator'] as $role) {
            $isSpectator = $role == 'spectator';
            $response = $this->render('Game/Splendor/MainBoard/nobleTilesDisplay.html.twig', [
                'nobleTiles' => $game->getMainBoard()->getNobleTiles(),
                'isSpectator' => $isSpectator,
                'game' => $game,
            ]);

            $this->publishService->publish(
                $this->generateUrl('app_game_show_spl', ['id' => $game->getId()])
                .'nobleTiles'.$role,
                $response);
        }
    }

    /**
     * publishReservedCards : send a mercure notification with information regarding the player's reserved cards
     * @param GameSPL $game
     * @return void
     */
    private function publishReservedCards(GameSPL $game): void
    {
        foreach ($game->getPlayers() as $player) {
            $response = $this->render('Game/Splendor/PersonalBoard/reservedCards.html.twig', [
                'nobleTiles' => $game->getMainBoard()->getNobleTiles(),
                'needToPlay' => $player->isTurnOfPlayer(),
                'playerReservedCards' => $this->SPLService->getReservedCards($player),
                'game' => $game,
            ]);

            $this->publishService->publish(
                $this->generateUrl('app_game_show_spl',
                    ['id' => $game->getId()]).'reservedCards'.$player->getId(),
                $response);
        }
    }


    /**
     * publishRanking : send a mercure notification with new informations about players to display in ranking
     * @param GameSPL $game
     * @return void
     */
    private function publishRanking(GameSPL $game): void
    {
        $response = $this->render('Game/Splendor/Ranking/ranking.html.twig', [
            'ranking' => $this->SPLService->getRanking($game),
            'game' => $game,
        ]);

        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'ranking',
            $response);
    }

    /**
     * publishEndOfGame : send a mercure notification for the end of a game of Splendor
     * @param GameSPL $game
     * @return void
     */
    private function publishEndOfGame(GameSPL $game): void
    {
        $winner = $this->SPLService->getRanking($game)[0];
        $this->logService->sendPlayerLog($game, $winner,
            $winner->getUsername() . " won game " . $game->getId());
        $this->logService->sendSystemLog($game, "game " . $game->getId() . " ended");
        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'endOfGame',
            new Response($winner?->getUsername()));
    }
}
