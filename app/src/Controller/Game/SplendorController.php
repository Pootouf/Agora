<?php

namespace App\Controller\Game;

use App\Entity\Game\DTO\GameParameters;
use App\Entity\Game\DTO\GameTranslation;
use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Entity\Game\Splendor\SplendorTranslation;
use App\Service\Game\LogService;
use App\Service\Game\MessageService;
use App\Service\Game\PublishService;
use App\Service\Game\Splendor\SPLService;
use App\Service\Game\Splendor\TokenSPLService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class SplendorController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TokenSPLService        $tokenSPLService,
        private SPLService             $splService,
        private LogService             $logService,
        private PublishService         $publishService,
        private MessageService         $messageService
    ) {
    }

    #[Route('/game/splendor/{id}', name: 'app_game_show_spl')]
    public function showGame(GameSPL $game): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->splService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        $isSpectator = false;
        $needToPlay = false;
        if ($player == null) {
            $player = $game->getPlayers()->get(0);
            $isSpectator = true;
        } else {
            $needToPlay = $player->isTurnOfPlayer();
        }
        $messages = $this->messageService->receiveMessage($game->getId());

        $mainBoardTokens = $game->getMainBoard()->getTokens();
        return $this->render('/Game/Splendor/index.html.twig', [
            'game' => $game,
            'playerBoughtCards' => $this->splService->getPurchasedCards($player),
            'playerReservedCards' => $this->splService->getReservedCards($player),
            'playerTokens' => $player->getPersonalBoard()->getTokens(),
            'drawCardsLevelOneCount' => $this->splService
                    ->getDrawCardsByLevel(SplendorParameters::DRAW_CARD_LEVEL_ONE, $game)
                    ->count(),
            'drawCardsLevelTwoCount' => $this->splService
                    ->getDrawCardsByLevel(SplendorParameters::DRAW_CARD_LEVEL_TWO, $game)
                    ->count(),
            'drawCardsLevelThreeCount' => $this->splService
                    ->getDrawCardsByLevel(SplendorParameters::DRAW_CARD_LEVEL_THREE, $game)
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
            'takenCard' => null,
            'isGameFinished' => $this->splService->isGameEnded($game),
            'nobleTiles' => $game->getMainBoard()->getNobleTiles(),
            'isSpectator' => $isSpectator,
            'needToPlay' => $needToPlay,
            'selectedCard' => null,
            'levelCard' => null,
            'selectedReservedCard' => null,
            'purchasableCards' => $this->splService
                    ->getPurchasableCardsOnBoard($game, $player),
            'purchasableCardsOnPersonalBoard' => $this->splService
                ->getPurchasableCardsOnPersonalBoard($player),
            'canReserveCard' => $this->splService->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player),
            'messages' => $messages
        ]);
    }

    #[Route('/game/{idGame}/splendor/select/board/{idCard}', name: 'app_game_splendor_select_from_board')]
    public function selectCardFromBoard(
        #[MapEntity(id: 'idGame')] GameSPL $game,
        #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card
    ): Response {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->splService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        return $this->render(
            'Game/Splendor/MainBoard/cardActions.html.twig',
            [
            'selectedCard' => $card,
            'levelCard' => null,
            'game' => $game,
            'selectedReservedCard' => null,
            'purchasableCards' => $this->splService->getPurchasableCardsOnBoard($game, $player),
            'purchasableCardsOnPersonalBoard' => $this->splService
                ->getPurchasableCardsOnPersonalBoard($player),
            'canReserveCard' => $this->splService->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player),
         ]
        );
    }

    #[Route('/game/{idGame}/splendor/select/draw/{level}', name: 'app_game_splendor_select_from_draw')]
    public function selectCardFromDraw(
        #[MapEntity(id: 'idGame')] GameSPL $game,
        int $level
    ): Response {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->splService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        return $this->render(
            'Game/Splendor/MainBoard/cardActions.html.twig',
            [
                'levelCard' => $level,
                'selectedCard' => null,
                'game' => $game,
                'selectedReservedCard' => null,
                'canReserveCard' => $this->splService->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player),
            ]
        );
    }

    #[Route('/game/{idGame}/splendor/select/reserved/{idCard}', name: 'app_game_splendor_select_from_personal_board')]
    public function selectCardFromPersonalBoard(
        #[MapEntity(id: 'idGame')] GameSPL $game,
        #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card
    ): Response {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->splService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        return $this->render(
            'Game/Splendor/MainBoard/cardActions.html.twig',
            [
                'selectedCard' => null,
                'levelCard' => null,
                'game' => $game,
                'selectedReservedCard' => $card,
                'purchasableCards' => $this->splService->getPurchasableCardsOnBoard($game, $player),
                'purchasableCardsOnPersonalBoard' => $this->splService
                    ->getPurchasableCardsOnPersonalBoard($player),
                'canReserveCard' => false,
            ]
        );
    }

    #[Route('/game/{idGame}/splendor/buy/card/{idCard}', name: 'app_game_splendor_buy_card')]
    public function buyCard(
        #[MapEntity(id: 'idGame')] GameSPL $game,
        #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card
    ): Response {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->splService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if ($this->splService->getActivePlayer($game) !== $player) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }

        $playerCard = $this->splService->getPlayerCardFromDevelopmentCard($game, $card);
        $reserved = false;
        if ($playerCard != null) {
            if ($player->getId() != $playerCard->getPersonalBoardSPL()->getPlayerSPL()->getId()) {
                return new Response(
                    SplendorTranslation::RESPONSE_NOT_PLAYER_CARD,
                    Response::HTTP_FORBIDDEN
                );
            }
            if (!$playerCard->isIsReserved()) {
                return new Response(
                    SplendorTranslation::RESPONSE_CARD_NOT_RESERVED,
                    Response::HTTP_FORBIDDEN
                );
            }
            $reserved = true;
        }

        try {
            $returnedData = $this->splService->buyCard($player, $card);
        } catch (Exception $e) {
            $this->logService->sendPlayerLog(
                $game,
                $player,
                $player->getUsername() . SplendorTranslation::CANNOT_BUY_CARD . $card->getId()
            );
            return new Response(
                SplendorTranslation::RESPONSE_CANNOT_BUY_CARD . $e->getMessage(),
                Response::HTTP_FORBIDDEN
            );
        }

        $this->notifyActionValidated($game, $player);
        if (!$reserved) {
            $this->publishAnimTakenCard($game, $player->getUsername(), $card, $returnedData["newDevCard"]);
        }
        $this->publishAnimReturnedTokens($game, $player->getUsername(), $returnedData["retrievePlayerMoney"]);
        $this->manageEndOfRound($game);

        $nobleTileId = $this->splService->addBuyableNobleTilesToPlayer($game, $player);
        if ($nobleTileId != -1) {
            $this->notifyPlayersNobleTileAcquired($player);
            $this->publishAnimNoble($game, $player->getUsername(), $nobleTileId);
            $this->logService->sendPlayerLog(
                $game,
                $player,
                $player->getUsername() . SplendorTranslation::RECEIVED_NOBLE_VISIT
                . $nobleTileId
            );
        }
        $this->publishNobleTiles($game);
        $this->publishReservedCards($game);

        $this->logService->sendPlayerLog(
            $game,
            $player,
            $player->getUsername() . SplendorTranslation::BOUGHT_CARD . $card->getId()
        );
        return new Response(SplendorTranslation::RESPONSE_BOUGHT_CARD, Response::HTTP_OK);
    }

    #[Route('/game/{idGame}/splendor/reserve/card/{idCard}', name: 'app_game_splendor_reserve_card_row')]
    public function reserveCardOnRows(
        #[MapEntity(id: 'idGame')] GameSPL $game,
        #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card
    ): Response {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->splService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if ($this->splService->getActivePlayer($game) !== $player) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        if (!$this->splService->canPlayerReserveCard($game, $card)) {
            return new Response(
                SplendorTranslation::RESPONSE_CANNOT_RESERVE_CARD,
                Response::HTTP_FORBIDDEN
            );
        }

         return $this->reserveCard($player, $game, $card, $card->getLevel());
     }

    #[Route('/game/{idGame}/splendor/reserve/draw/{level}', name: 'app_game_splendor_reserve_card_draw')]
    public function reserveCardOnDraw(
        #[MapEntity(id: 'idGame')] GameSPL $game,
        int $level
    ): Response {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->splService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if ($this->splService->getActivePlayer($game) !== $player) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        $draw = $this->splService->getDrawFromGameAndLevel($game, $level);
        $card = $this->splService->getCardFromDraw($draw);
        if ($card == null || !$this->splService->canPlayerReserveCard($game, $card)) {
            return new Response(
                SplendorTranslation::RESPONSE_CANNOT_RESERVE_CARD,
                Response::HTTP_FORBIDDEN
            );
        }

         return $this->reserveCard($player, $game, $card, $level);
     }

    #[Route('/game/{idGame}/splendor/cancelTokensSelection', name: 'app_game_splendor_cancel_tokens_selection')]
    public function cancelTokensSelection(
        #[MapEntity(id: 'idGame')] GameSPL $game
    ): Response {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->splService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        $this->tokenSPLService->clearSelectedTokens($player);
        $this->publishToken($game, $player);
        $this->logService->sendPlayerLog(
            $game,
            $player,
            $player->getUsername() . SplendorTranslation::TOKEN_SELECTION_CANCELED
        );
        return new Response(
            SplendorTranslation::RESPONSE_TOKEN_SELECTION_CANCELED,
            Response::HTTP_OK
        );
    }

    #[Route('/game/{idGame}/splendor/takeToken/{color}', name: 'app_game_splendor_selectToken')]
    public function takeToken(
        #[MapEntity(id: 'idGame')] GameSPL $game,
        string                             $color
    ): Response {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->splService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(GameTranslation::INVALID_PLAYER_MESSAGE, Response::HTTP_FORBIDDEN);
        }
        if ($this->splService->getActivePlayer($game) !== $player) {
            return new Response(GameTranslation::NOT_PLAYER_TURN, Response::HTTP_FORBIDDEN);
        }
        $tokenSPL = $this->tokenSPLService->getTokenOnMainBoardFromColor($game->getMainBoard(), $color);
        if ($tokenSPL == null) {
            return new Response(
                SplendorTranslation::NO_MORE_TOKEN_SELECTED_COLOR,
                Response::HTTP_FORBIDDEN
            );
        }

        try {
            $this->tokenSPLService->takeToken($player, $tokenSPL);
        } catch(Exception) {
            $this->logService->sendPlayerLog(
                $game,
                $player,
                $player->getUsername() . SplendorTranslation::TRY_TAKE_TOKEN . $color
                . SplendorTranslation::NOT_ABLE
            );
            return new Response(
                SplendorTranslation::RESPONSE_NOT_ABLE_TO_TAKE_TOKEN,
                Response::HTTP_FORBIDDEN
            );
        }

        $message = $player->getUsername() . SplendorTranslation::TAKE_TOKEN . $tokenSPL->getColor();
        $this->logService->sendPlayerLog($game, $player, $message);

        if ($this->tokenSPLService->mustEndPlayerRoundBecauseOfTokens($player)) {
            $this->notifyActionValidated($game, $player);
            $this->publishAnimTakenTokens(
                $game,
                $player->getUsername(),
                $player->getPersonalBoard()->getSelectedTokens()
            );
            $this->tokenSPLService->validateTakingOfTokens($player);
            $this->manageEndOfRound($game);
        } else {
            foreach ($game->getPlayers() as $playerNotif) {
                $this->publishToken($game, $playerNotif);
            }
        }
        return new Response(SplendorTranslation::RESPONSE_TAKE_TOKEN, Response::HTTP_OK);
    }

    /**
     * manageEndOfRound: if the game must end then end the game
     *     else end active player's round
     * @param GameSPL $gameSPL
     * @return void
     */
    private function manageEndOfRound(GameSPL $gameSPL): void
    {
        if ($this->splService->isGameEnded($gameSPL)) {
            $this->publishEndOfGame($gameSPL);
        } else {
            $activePlayer = $this->splService->getActivePlayer($gameSPL);
            $this->splService->endRoundOfPlayer($gameSPL, $activePlayer);
            $this->entityManager->persist($gameSPL);
            $this->entityManager->persist($activePlayer);
            $this->entityManager->flush();

            $newActivePlayer = $this->splService->getActivePlayer($gameSPL);
            foreach ($gameSPL->getPlayers() as $playerNotif) {
                $this->publishToken($gameSPL, $playerNotif);
                if ($playerNotif->getUsername() == $newActivePlayer->getUsername()) {
                    $this->publishNotification(
                        $gameSPL,
                        SplendorParameters::NOTIFICATION_DURATION_10,
                        SplendorTranslation::MESSAGE_TITLE_ROUND_START,
                        SplendorTranslation::MESSAGE_DESCRIPTION_ROUND_START,
                        GameParameters::RINGING_NOTIFICATION_TYPE,
                        GameParameters::NOTIFICATION_COLOR_BLUE,
                        $playerNotif->getUsername()
                    );
                } else {
                    $this->publishNotification(
                        $gameSPL,
                        SplendorParameters::NOTIFICATION_DURATION_5,
                        SplendorTranslation::MESSAGE_TITLE_OTHER_PLAYER_ROUND_START,
                        SplendorTranslation::MESSAGE_DESCRIPTION_OTHER_PLAYER_ROUND_START
                        . $newActivePlayer->getUsername(),
                        GameParameters::INFO_NOTIFICATION_TYPE,
                        GameParameters::NOTIFICATION_COLOR_GREEN,
                        $playerNotif->getUsername()
                    );
                }
            }
            $this->publishNotification(
                $gameSPL,
                SplendorParameters::NOTIFICATION_DURATION_5,
                SplendorTranslation::MESSAGE_TITLE_OTHER_PLAYER_ROUND_START,
                SplendorTranslation::MESSAGE_DESCRIPTION_OTHER_PLAYER_ROUND_START
                . $newActivePlayer->getUsername(),
                GameParameters::INFO_NOTIFICATION_TYPE,
                GameParameters::NOTIFICATION_COLOR_GREEN,
                ""
            );
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
            $response
        );
    }

    /**
     * publishDevelopmentCards : send a mercure notification to update the development cards for players and spectators
     * @param GameSPL $game
     * @param int|null $selectedCard
     * @return void
     */
    private function publishDevelopmentCards(GameSPL $game, int $selectedCard = null): void
    {
        foreach ($game->getPlayers() as $player) {
            $this->publishDevelopmentCardsWithSelectedOptions(
                $game,
                $player,
                false,
                $player->isTurnOfPlayer(),
                $selectedCard
            );
        }
        $this->publishDevelopmentCardsWithSelectedOptions(
            $game,
            null,
            true,
            false,
            $selectedCard
        );
    }

    /**
     * publishDevelopmentCardsWithSelectedOptions : send a mercure notification regarding the development cards on
     *                                              main board depending on if the user is spectator or a player who
     *                                              must play
     * @param GameSPL $game
     * @param PlayerSPL|null $player
     * @param bool $isSpectator
     * @param bool $needToPlay
     * @param int|null $takenCard
     * @return void
     */
    private function publishDevelopmentCardsWithSelectedOptions(
        GameSPL $game,
        ?PlayerSPL $player,
        bool $isSpectator,
        bool $needToPlay,
        int $takenCard = null
    ): void {
        $response = $this->render('Game/Splendor/MainBoard/developmentCardsBoard.html.twig', [
            'rows' => $this->splService->getRowsFromGame($game),
            'drawCardsLevelOneCount' => $this->splService
                ->getDrawCardsByLevel(SplendorParameters::DRAW_CARD_LEVEL_ONE, $game)
                ->count(),
            'drawCardsLevelTwoCount' => $this->splService
                ->getDrawCardsByLevel(SplendorParameters::DRAW_CARD_LEVEL_TWO, $game)
                ->count(),
            'drawCardsLevelThreeCount' => $this->splService
                ->getDrawCardsByLevel(SplendorParameters::DRAW_CARD_LEVEL_THREE, $game)
                ->count(),
            'isSpectator' => $isSpectator,
            'needToPlay' => $needToPlay,
            'game' => $game,
            'takenCard' => $takenCard,
            'purchasableCards' => $player == null ? [] : $this->splService
                ->getPurchasableCardsOnBoard($game, $player),
            'purchasableCardsOnPersonalBoard' => $player == null ? [] : $this->splService
                ->getPurchasableCardsOnPersonalBoard($player),
            'canReserveCard' => $player == null
                || $this->splService->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player),
        ]);

        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()])
            .'developmentCards'
            .($player == null ? 'spectator' : $player->getId()),
            $response
        );
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
                $response
            );
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
                'playerReservedCards' => $this->splService->getReservedCards($player),
                'purchasableCardsOnPersonalBoard' => $this->splService
                    ->getPurchasableCardsOnPersonalBoard($player),
                'game' => $game,
            ]);

            $this->publishService->publish(
                $this->generateUrl(
                    'app_game_show_spl',
                    ['id' => $game->getId()]
                ).'reservedCards'.$player->getId(),
                $response
            );
        }
    }


    /**
     * publishRanking : send a mercure notification with new information about players to display in ranking
     * @param GameSPL $game
     * @return void
     */
    private function publishRanking(GameSPL $game): void
    {
        foreach ($game->getPlayers() as $player) {
            $response = $this->render('Game/Splendor/Ranking/ranking.html.twig', [
                'ranking' => $this->splService->getRanking($game),
                'game' => $game,
                'player' => $player,
                'isSpectator' => false,
            ]);

            $this->publishService->publish(
                $this->generateUrl('app_game_show_spl', ['id' => $game->getId()])
                .'ranking'.$player->getUsername(),
                $response
            );
        }

        $response = $this->render('Game/Splendor/Ranking/ranking.html.twig', [
            'ranking' => $this->splService->getRanking($game),
            'game' => $game,
            'player' => $game->getPlayers()->get(0),
            'isSpectator' => true,
        ]);

        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'ranking',
            $response
        );
    }

    /**
     * publishEndOfGame : send a mercure notification for the end of a game of Splendor
     * @param GameSPL $game
     * @return void
     */
    private function publishEndOfGame(GameSPL $game): void
    {
        $winner = $this->splService->getRanking($game)[0];
        $this->logService->sendPlayerLog(
            $game,
            $winner,
            $winner->getUsername() . SplendorTranslation::WIN_GAME . $game->getId()
        );
        $this->logService->sendSystemLog($game, SplendorTranslation::GAME_DESC
            . $game->getId() . SplendorTranslation::HAS_ENDED);
        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'endOfGame',
            new Response($winner?->getUsername())
        );
    }


    /**
     * publishAnimTakenTokens: publish the animation of taking the tokens
     * @param GameSPL $game
     * @param string $player
     * @param Collection<SelectedTokenSPL> $selectedTokens
     * @return void
     */
    private function publishAnimTakenTokens(GameSPL $game, string $player, Collection $selectedTokens): void
    {
        $selectedTokenIds = [];

        foreach ($selectedTokens as $token) {
            $selectedTokenIds[] = $token->getToken()->getType();
        }
        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'animTakenTokens',
            new Response($player . '__' . implode('_', $selectedTokenIds))
        );
    }


    /**
     * publishAnimNoble: publish the animation to animate noble tiles
     * @param GameSPL $game
     * @param string $player
     * @param int $nobleTileId
     * @return void
     */
    private function publishAnimNoble(GameSPL $game, string $player, int $nobleTileId): void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'animNoble',
            new Response($player . '__' . $nobleTileId)
        );
    }

    /**
     * publishAnimReturnedTokens: publish the animation to return the tokens into the draw
     * @param GameSPL $game
     * @param string $player
     * @param array<string> $returnedTokens the type of the tokens to return
     * @return void
     */
    private function publishAnimReturnedTokens(GameSPL $game, string $player, array $returnedTokens): void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'animReturnedTokens',
            new Response($player . '__' . implode('_', $returnedTokens))
        );
    }

    /**
     * publishAnimTakenCard: publish animations of taking a development card in mainboard
     * @param GameSPL $game
     * @param string $player
     * @param DevelopmentCardsSPL $selectedCard
     * @param DevelopmentCardsSPL $newDevCard
     * @return void
     */
    private function publishAnimTakenCard(
        GameSPL $game,
        string $player,
        DevelopmentCardsSPL $selectedCard,
        DevelopmentCardsSPL $newDevCard
    ): void {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'animTakenCard',
            new Response($player . '__' . $selectedCard->getId())
        );
        $discardsOfLevel =  $game->getMainBoard()->getDrawCards()->get($selectedCard->getLevel() - 1);
        $cardsInDiscard = $discardsOfLevel->getDevelopmentCards();
        $numberOfCard = $cardsInDiscard->count();

        if ($numberOfCard > 0) {
            $this->publishDevelopmentCards($game, $newDevCard->getId());
            $this->publishService->publish(
                $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'animTakenCardFromDraw',
                new Response($selectedCard->getLevel() . '__' . $newDevCard->getId())
            );
        }
    }

    /**
     * publishAnimCardOnDraw: publish the animation of moving card from draw
     * @param GameSPL $game
     * @param string $player
     * @param int $cardId
     * @return void
     */
    private function publishAnimCardOnDraw(GameSPL $game, string $player, int $cardId): void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'animDrawCard',
            new Response($player . '__' . $cardId)
        );
    }


    /**
     * publishAnimTakenJoker: publish the animation of taking a joker
     * @param GameSPL $game
     * @param string $player
     * @return void
     */
    private function publishAnimTakenJoker(GameSPL $game, string $player): void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'animTakenTokens',
            new Response($player . '__gold')
        );
    }

    private function publishNotification(
        GameSPL $game,
        int $duration,
        string $message,
        string $description,
        string $iconId,
        string $loadingBarColor,
        string $targetedPlayer
    ): void {
        $dataSent =  [$duration, $message, $description, $iconId, $loadingBarColor];

        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()])
            .'notification'.$targetedPlayer,
            new Response(implode('_', $dataSent))
        );
    }

    /**
     * notifyPlayersNobleTileAcquired: make a notification for the players of the game to warn
     *                                 that playerWithNobleTile has recovered a noble tile
     * @param PlayerSPL $playerWithNobleTile
     * @return void
     */
    private function notifyPlayersNobleTileAcquired(PlayerSPL $playerWithNobleTile)
    {
        $game = $playerWithNobleTile->getGameSPL();
        foreach ($game->getPlayers() as $playerInGame) {
            if ($playerInGame->getUsername() == $playerWithNobleTile->getUsername()) {
                $this->publishNotification(
                    $game,
                    SplendorParameters::NOTIFICATION_DURATION_10,
                    SplendorTranslation::MESSAGE_TITLE_NOBLE_VISIT,
                    "",
                    GameParameters::RINGING_NOTIFICATION_TYPE,
                    GameParameters::NOTIFICATION_COLOR_YELLOW,
                    $playerInGame->getUsername()
                );
            } else {
                $this->publishNotification(
                    $game,
                    SplendorParameters::NOTIFICATION_DURATION_5,
                    $playerWithNobleTile->getUsername()
                    . SplendorTranslation::MESSAGE_TITLE_OTHER_PLAYER_NOBLE_VISIT,
                    "",
                    GameParameters::INFO_NOTIFICATION_TYPE,
                    GameParameters::NOTIFICATION_COLOR_GREEN,
                    $playerInGame->getUsername()
                );
            }
        }
    }

    /**
     * notifyActionValidated: make a validate notification for the selected player
     * @param GameSPL $game
     * @param PlayerSPL $player
     * @return void
     */
    private function notifyActionValidated(GameSPL $game, PlayerSPL $player): void
    {
        $this->publishNotification(
            $game,
            SplendorParameters::NOTIFICATION_DURATION_5,
            SplendorTranslation::MESSAGE_TITLE_ACTION_VALIDATED,
            "",
            GameParameters::VALIDATION_NOTIFICATION_TYPE,
            GameParameters::NOTIFICATION_COLOR_GREEN,
            $player->getUsername()
        );
    }

    /**
     * reserveCard: make the player reserve the selected development card
     * @param PlayerSPL $player
     * @param GameSPL $game
     * @param DevelopmentCardsSPL $card
     * @return Response
     */
    private function reserveCard(PlayerSPL $player, GameSPL $game, DevelopmentCardsSPL $card, int $level) : Response
    {
        try {
            $returnedData = $this->splService->reserveCard($player, $card);
        } catch (Exception $e) {
            $this->logService->sendPlayerLog(
                $game,
                $player,
                $player->getUsername() . SplendorTranslation::TRY_RESERVE_CARD . $card->getId()
                . SplendorTranslation::NOT_ABLE
            );
            return new Response(SplendorTranslation::RESPONSE_CANNOT_RESERVE_CARD
                . " : " . $e->getMessage(), Response::HTTP_FORBIDDEN);
        }

        $this->notifyActionValidated($game, $player);
        if ($returnedData["cardFromDraw"] != null) {
            $this->publishAnimTakenCard($game, $player->getUsername(), $card, $returnedData["cardFromDraw"]);
        } else {
            $this->publishAnimCardOnDraw($game, $player->getUsername(), $level);
        }
        if ($returnedData["isJokerTaken"]) {
            $this->publishAnimTakenJoker($game, $player->getUsername());
        }
        $this->manageEndOfRound($game);
        $this->logService->sendPlayerLog(
            $game,
            $player,
            $player->getUsername() . SplendorTranslation::RESERVED_CARD . $card->getId()
        );
        return new Response(SplendorTranslation::RESPONSE_RESERVED_CARD, Response::HTTP_OK);
    }
}
