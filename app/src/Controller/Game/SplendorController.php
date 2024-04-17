<?php

namespace App\Controller\Game;

use AllowDynamicProperties;
use App\Entity\Game\DTO\Game;
use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\GameService;
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
use function Symfony\Component\Translation\t;

#[IsGranted('ROLE_USER')]
class SplendorController extends AbstractController
{


    public function __construct(private EntityManagerInterface $entityManager,
                                private TokenSPLService $tokenSPLService,
                                private SPLService $SPLService,
                                private LogService $logService,
                                private PublishService $publishService,
                                private MessageService $messageService,
                                private GameService $gameService)
    {}

    #[Route('/game/splendor/{id}', name: 'app_game_show_spl')]
    public function showGame(GameSPL $game): Response
    {
        if ($game->isPaused() || !$game->isLaunched())
            return new Response("Game cannot be accessed", Response::HTTP_FORBIDDEN);
        $player = $this->gameService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
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
            'takenCard' => null,
            'isGameFinished' => $this->SPLService->isGameEnded($game),
            'nobleTiles' => $game->getMainBoard()->getNobleTiles(),
            'isSpectator' => $isSpectator,
            'needToPlay' => $needToPlay,
            'selectedCard' => null,
            'levelCard' => null,
            'selectedReservedCard' => null,
            'purchasableCards' => $this->SPLService
                    ->getPurchasableCardsOnBoard($game, $player),
            'purchasableCardsOnPersonalBoard' => $this->SPLService
                ->getPurchasableCardsOnPersonalBoard($player),
            'canReserveCard' => $this->SPLService->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player),
            'messages' => $messages
        ]);
    }

     #[Route('/game/{idGame}/splendor/select/board/{idCard}', name: 'app_game_splendor_select_from_board')]
     public function selectCardFromBoard(
         #[MapEntity(id: 'idGame')] GameSPL $game,
         #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card): Response
     {
         if ($game->isPaused() || !$game->isLaunched())
             return new Response("Game cannot be accessed", Response::HTTP_FORBIDDEN);
         /** @var ?PlayerSPL $player */
         $player = $this->gameService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         return $this->render('Game/Splendor/MainBoard/cardActions.html.twig',
         [
             'selectedCard' => $card,
             'levelCard' => null,
             'game' => $game,
             'selectedReservedCard' => null,
             'purchasableCards' => $this->SPLService->getPurchasableCardsOnBoard($game, $player),
             'purchasableCardsOnPersonalBoard' => $this->SPLService
                 ->getPurchasableCardsOnPersonalBoard($player),
             'canReserveCard' => $this->SPLService->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player),
         ]);
     }

     #[Route('/game/{idGame}/splendor/select/draw/{level}', name: 'app_game_splendor_select_from_draw')]
     public function selectCardFromDraw(
         #[MapEntity(id: 'idGame')] GameSPL $game, int $level): Response
     {
         if ($game->isPaused() || !$game->isLaunched())
             return new Response("Game cannot be accessed", Response::HTTP_FORBIDDEN);
         $player = $this->gameService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         /** @var ?PlayerSPL $player */
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
         if ($game->isPaused() || !$game->isLaunched())
             return new Response("Game cannot be accessed", Response::HTTP_FORBIDDEN);
         /** @var ?PlayerSPL $player */
         $player = $this->gameService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         return $this->render('Game/Splendor/MainBoard/cardActions.html.twig',
             [
                 'selectedCard' => null,
                 'levelCard' => null,
                 'game' => $game,
                 'selectedReservedCard' => $card,
                 'purchasableCards' => $this->SPLService->getPurchasableCardsOnBoard($game, $player),
                 'purchasableCardsOnPersonalBoard' => $this->SPLService
                     ->getPurchasableCardsOnPersonalBoard($player),
                 'canReserveCard' => false,
             ]);
     }

     #[Route('/game/{idGame}/splendor/buy/card/{idCard}', name: 'app_game_splendor_buy_card')]
     public function buyCard(
         #[MapEntity(id: 'idGame')] GameSPL $game,
         #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card): Response
     {
         if ($game->isPaused() || !$game->isLaunched())
             return new Response("Game cannot be accessed", Response::HTTP_FORBIDDEN);
         $player = $this->gameService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         if ($player == null) {
             return new Response('Invalid player', Response::HTTP_FORBIDDEN);
         }
         if ($this->SPLService->getActivePlayer($game) !== $player) {
             $this->logService->sendPlayerLog($game, $player,
                 $player->getUsername() . " a essayé d'acheter une carte alors que ce n'est pas son tour");
             return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
         }
         $playerCard = $this->SPLService->getPlayerCardFromDevelopmentCard($game, $card);
         $reserved = false;
         if ($playerCard != null) {
             if ($player->getId() != $playerCard->getPersonalBoardSPL()->getPlayerSPL()->getId()) {
                 $this->logService->sendPlayerLog($game, $player,
                     $player->getUsername() . " a essayé d'acheter la carte " . $card->getId()
                 . " alors qu'elle ne lui appartient pas");
                 return new Response("Not player's card ", Response::HTTP_FORBIDDEN);
             }
             if (!$playerCard->isIsReserved()) {
                 $this->logService->sendPlayerLog($game, $player,
                     $player->getUsername() . " a essayé d'acheter la carte " . $card->getId()
                 . "alors qu'il ne l'a pas réservée");
                 return new Response("The card is not reserved", Response::HTTP_FORBIDDEN);
             }
             $reserved = true;
         }
         try {
             $returnedData = $this->SPLService->buyCard($player, $card);
         } catch (Exception $e) {
             $this->logService->sendPlayerLog($game, $player,
                 $player->getUsername() . " n'a pas pu acheter la carte " . $card->getId());
             return new Response("Can't buy this card : ".$e->getMessage(), Response::HTTP_FORBIDDEN);
         }
         try {
             $this->publishNotification($game, SplendorParameters::$NOTIFICATION_DURATION_5, "Action validée !", "", "validation",
                 "green", $player->getUsername());
             if (!$reserved) {
                 $this->publishAnimTakenCard($game, $player->getUsername(), $card, $returnedData["newDevCard"]);
             }
             $this->publishAnimReturnedTokens($game, $player->getUsername(), $returnedData["retrievePlayerMoney"]);
         } finally {
             $this->manageEndOfRound($game);
         }
         try {
             $nobleTileId = $this->SPLService->addBuyableNobleTilesToPlayer($game, $player);
             if ($nobleTileId != -1){
                 foreach ($game->getPlayers() as $playerInGame) {
                     if ($playerInGame->getUsername() == $player->getUsername()) {
                         $this->publishNotification($game, SplendorParameters::$NOTIFICATION_DURATION_10, "Un noble vous rend visite !",
                             "", "ringing",
                             "yellow", $playerInGame->getUsername());
                     } else {
                         $this->publishNotification($game, SplendorParameters::$NOTIFICATION_DURATION_5,
                             $player->getUsername()." reçoit la visite d'un noble",
                             "", "info",
                             "green", $playerInGame->getUsername());
                     }
                 }
                 $this->publishNotification($game, SplendorParameters::$NOTIFICATION_DURATION_5,
                     $player->getUsername()." reçoit la visite d'un noble",
                     "", "info",
                     "green", "");
                 $this->publishAnimNoble($game, $player->getUsername(), $nobleTileId);
                 $this->logService->sendPlayerLog($game, $player,
                     $player->getUsername() . " a reçu la visite d'un noble venant de la tuile noble
                      " . $nobleTileId);
             }
             $this->publishNobleTiles($game);
             $this->publishReservedCards($game);
         } finally {
             $this->logService->sendPlayerLog($game, $player,
                 $player->getUsername() . " a acheté la carte " . $card->getId());
             return new Response('Card Bought', Response::HTTP_OK);
         }
     }

     #[Route('/game/{idGame}/splendor/reserve/card/{idCard}', name: 'app_game_splendor_reserve_card_row')]
     public function reserveCardOnRows(
         #[MapEntity(id: 'idGame')] GameSPL $game,
         #[MapEntity(id: 'idCard')] DevelopmentCardsSPL $card): Response
     {
         if ($game->isPaused() || !$game->isLaunched())
             return new Response("Game cannot be accessed", Response::HTTP_FORBIDDEN);
         $player = $this->gameService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         if ($player == null) {
             return new Response('Invalid player', Response::HTTP_FORBIDDEN);
         }
         if ($this->SPLService->getActivePlayer($game) !== $player) {
             $this->logService->sendPlayerLog($game, $player,
                 $player->getUsername() . " a essayé de réserver une carte alors que ce n'est pas son tour");
             return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
         }
         if (!$this->SPLService->canPlayerReserveCard($game, $card)) {
             $this->logService->sendPlayerLog($game, $player,
                 $player->getUsername() . " a essayé de réserver la carte " . $card->getId()
                 . " alors qu'il ne peut pas");
             return new Response("Can't reserve this card", Response::HTTP_FORBIDDEN);
         }
         try {
             $returnedData = $this->SPLService->reserveCard($player, $card);
         } catch (Exception $e) {
             $this->logService->sendPlayerLog($game, $player,
                 $player->getUsername() . " a essayé de réserver la carte " . $card->getId()
                 . " alors qu'il ne peut pas");
             return new Response("Can't reserve this card : " . $e->getMessage(), Response::HTTP_FORBIDDEN);
         }
         try {
             $this->publishNotification($game, SplendorParameters::$NOTIFICATION_DURATION_5, "Action validé !", "", "validation",
                 "green", $player->getUsername());
             if ($returnedData["cardFromDraw"] != null) {
                 $this->publishAnimTakenCard($game, $player->getUsername(), $card, $returnedData["cardFromDraw"]);
             }
             if ($returnedData["isJokerTaken"]) {
                 $this->publishAnimTakenJoker($game, $player->getUsername());
             }
         } finally {
             $this->manageEndOfRound($game);
             $this->logService->sendPlayerLog($game, $player,
                 $player->getUsername() . " a réservé la carte " . $card->getId());
             return new Response('Card reserved', Response::HTTP_OK);
         }
     }

     #[Route('/game/{idGame}/splendor/reserve/draw/{level}', name: 'app_game_splendor_reserve_card_draw')]
     public function reserveCardOnDraw(
         #[MapEntity(id: 'idGame')] GameSPL $game,
         int $level): Response
     {
         if ($game->isPaused() || !$game->isLaunched())
             return new Response("Game cannot be accessed", Response::HTTP_FORBIDDEN);
         $player = $this->gameService->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
         if ($player == null) {
             return new Response('Invalid player', Response::HTTP_FORBIDDEN);
         }
         if ($this->SPLService->getActivePlayer($game) !== $player) {
             $this->logService->sendPlayerLog($game, $player,
                 $player->getUsername() . " a essayé de réserver une carte alors que ce n'est pas son tour");
             return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
         }
         $draw = $this->SPLService->getDrawFromGameAndLevel($game, $level);
         $card = $this->SPLService->getCardFromDraw($draw);
         if ($card == null || !$this->SPLService->canPlayerReserveCard($game, $card)) {
             $this->logService->sendPlayerLog($game, $player,
                 $player->getUsername() . " a essayé de réserver la carte " . $card->getId()
                 . " alors qu'il ne peut pas");
             return new Response("Can't reserve this card", Response::HTTP_FORBIDDEN);
         }
         try {
             $returnedData = $this->SPLService->reserveCard($player, $card);
         } catch (Exception $e) {
             $this->logService->sendPlayerLog($game, $player,
                 $player->getUsername() . " a essayé de réserver la carte " . $card->getId()
                 . " alors qu'il ne peut pas");
             return new Response("Can't reserve this card : " . $e->getMessage(), Response::HTTP_FORBIDDEN);
         }
         try {
             $this->publishNotification($game, SplendorParameters::$NOTIFICATION_DURATION_5, "Action validé !", "", "validation",
                 "green", $player->getUsername());
             $this->publishAnimCardOnDraw($game, $player->getUsername(), $level);
             if ($returnedData["isJokerTaken"]) {
                 $this->publishAnimTakenJoker($game, $player->getUsername());
             }
         } finally {
             $this->manageEndOfRound($game);
             $this->logService->sendPlayerLog($game, $player,
                 $player->getUsername() . " a réservé la carte " . $card->getId());
             return new Response('Card reserved', Response::HTTP_OK);
         }
     }

     #[Route('/game/{idGame}/splendor/cancelTokensSelection', name: 'app_game_splendor_cancel_tokens_selection')]
     public function cancelTokensSelection(
         #[MapEntity(id: 'idGame')] GameSPL $gameSPL): Response
     {
         if ($gameSPL->isPaused() || !$gameSPL->isLaunched())
             return new Response("Game cannot be accessed", Response::HTTP_FORBIDDEN);
         $player = $this->gameService->getPlayerFromNameAndGame($gameSPL, $this->getUser()->getUsername());
         /** @var ?PlayerSPL $player */
         if ($player == null) {
             return new Response('Invalid player', Response::HTTP_FORBIDDEN);
         }
         $this->tokenSPLService->clearSelectedTokens($player);
         $this->publishToken($gameSPL, $player);
         $this->logService->sendPlayerLog($gameSPL, $player,
             $player->getUsername() . " a reposé les jetons en cours de sélection");
         return new Response('Selected tokens cleaned', Response::HTTP_OK);
     }

     #[Route('/game/{idGame}/splendor/takeToken/{color}', name: 'app_game_splendor_selectToken')]
     public function takeToken(
         #[MapEntity(id: 'idGame')] GameSPL $gameSPL,
         string $color): Response
     {
         if ($gameSPL->isPaused() || !$gameSPL->isLaunched())
             return new Response("Game cannot be accessed", Response::HTTP_FORBIDDEN);
         $player = $this->gameService->getPlayerFromNameAndGame($gameSPL, $this->getUser()->getUsername());
         if ($player == null) {
             return new Response('Invalid player', Response::HTTP_FORBIDDEN);
         }
         if ($this->SPLService->getActivePlayer($gameSPL) !== $player) {
             $this->logService->sendPlayerLog($gameSPL, $player,
                 $player->getUsername() . " a essayé de prendre un jeton alors que ce n'est pas son tour");
             return new Response("Not player's turn", Response::HTTP_FORBIDDEN);
         }
         $tokenSPL = $this->tokenSPLService->getTokenOnMainBoardFromColor($gameSPL->getMainBoard(), $color);
         if ($tokenSPL == null) {
             $this->logService->sendPlayerLog($gameSPL, $player,
                 $player->getUsername() . " a essayé de prendre un jeton " . $color
                 . " alors qu'il n'en reste plus");
             return new Response("There is no more token of this color", Response::HTTP_FORBIDDEN);
         }
         try {
             $this->tokenSPLService->takeToken($player, $tokenSPL);
         } catch(Exception) {
             $this->logService->sendPlayerLog($gameSPL, $player,
                 $player->getUsername() . " a essayé de prendre un jeton " . $color
                 . " mais n'a pas pu");
             $message = $player->getUsername() . " tried to pick a token of " . $tokenSPL->getColor()
                 . " but could not ";
             $this->logService->sendPlayerLog($gameSPL, $player, $message);
             return new Response('Impossible to choose', Response::HTTP_FORBIDDEN);
         }
         $message = $player->getUsername() . " a pris un jeton " . $tokenSPL->getColor();
         $this->logService->sendPlayerLog($gameSPL, $player, $message);
         if ($this->tokenSPLService->mustEndPlayerRoundBecauseOfTokens($player)) {
             try {
                 $this->publishNotification($gameSPL, SplendorParameters::$NOTIFICATION_DURATION_5, "Action validé !", "", "validation",
                     "green", $player->getUsername());
                 $this->publishAnimTakenTokens($gameSPL, $player->getUsername(), $player->getPersonalBoard()->getSelectedTokens());
                 $this->tokenSPLService->validateTakingOfTokens($player);
             } finally {
                 $this->manageEndOfRound($gameSPL);
             }
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

             $newActivePlayer = $this->SPLService->getActivePlayer($gameSPL);
             foreach ($gameSPL->getPlayers() as $playerNotif) {
                 $this->publishToken($gameSPL, $playerNotif);
                 if ($playerNotif->getUsername() == $newActivePlayer->getUsername()) {
                     $this->publishNotification($gameSPL, SplendorParameters::$NOTIFICATION_DURATION_10, "C'est votre tour !",
                         "Jouez votre meilleur coup !", "ringing",
                         "blue", $playerNotif->getUsername());
                 } else {
                     $this->publishNotification($gameSPL, SplendorParameters::$NOTIFICATION_DURATION_5, "Joueur suivant !",
                         "C'est au tour de ".$newActivePlayer->getUsername(), "info",
                         "green", $playerNotif->getUsername());
                 }
             }
             $this->publishNotification($gameSPL, SplendorParameters::$NOTIFICATION_DURATION_5, "Joueur suivant !",
                 "C'est au tour de ".$newActivePlayer->getUsername(), "info",
                 "green", "");
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
     * publishDevelopmentCards : send a mercure notification to update the development cards for players and spectators
     * @param GameSPL $game
     * @param int|null $selectedCard
     * @return void
     */
    private function publishDevelopmentCards(GameSPL $game, int $selectedCard = null): void
    {
        foreach ($game->getPlayers() as $player) {
            $this->publishDevelopmentCardsWithSelectedOptions($game, $player, false,
                $player->isTurnOfPlayer(), $selectedCard);
        }
        $this->publishDevelopmentCardsWithSelectedOptions($game, null, true,
            false, $selectedCard);
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
    private function publishDevelopmentCardsWithSelectedOptions(GameSPL $game, ?PlayerSPL $player, bool $isSpectator,
                                                                bool $needToPlay, int $takenCard = null) : void
    {
        $response = $this->render('Game/Splendor/MainBoard/developmentCardsBoard.html.twig', [
            'rows' => $this->SPLService->getRowsFromGame($game),
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
            'takenCard' => $takenCard,
            'purchasableCards' => $player == null ? [] : $this->SPLService
                ->getPurchasableCardsOnBoard($game, $player),
            'purchasableCardsOnPersonalBoard' => $player == null ? [] : $this->SPLService
                ->getPurchasableCardsOnPersonalBoard($player),
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
                'purchasableCardsOnPersonalBoard' => $this->SPLService
                    ->getPurchasableCardsOnPersonalBoard($player),
                'game' => $game,
            ]);

            $this->publishService->publish(
                $this->generateUrl('app_game_show_spl',
                    ['id' => $game->getId()]).'reservedCards'.$player->getId(),
                $response);
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
                'ranking' => $this->SPLService->getRanking($game),
                'game' => $game,
                'player' => $player,
                'isSpectator' => false,
            ]);

            $this->publishService->publish(
                $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'ranking'.$player->getUsername(),
                $response);
        }

        $response = $this->render('Game/Splendor/Ranking/ranking.html.twig', [
            'ranking' => $this->SPLService->getRanking($game),
            'game' => $game,
            'player' => $game->getPlayers()->get(0),
            'isSpectator' => true,
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
            $winner->getUsername() . " a gagné la partie " . $game->getId());
        $this->logService->sendSystemLog($game, "la partie " . $game->getId() . " s'est terminée");
        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'endOfGame',
            new Response($winner?->getUsername()));
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
     * publishAnimTakenCard: publish animations of taking a development card in mainboard
     * @param GameSPL $game
     * @param string $player
     * @param DevelopmentCardsSPL $selectedCard
     * @param DevelopmentCardsSPL $newDevCard
     * @return void
     */
    private function publishAnimTakenCard(GameSPL $game, string $player, DevelopmentCardsSPL $selectedCard,
                                          DevelopmentCardsSPL $newDevCard): void
    {
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

    private function publishNotification(GameSPL $game, int $duration, string $message,
                                         string $description, string $iconId,
                                         string $loadingBarColor, string $targetedPlayer): void
    {
        $dataSent =  [$duration, $message, $description, $iconId, $loadingBarColor];

        $this->publishService->publish(
            $this->generateUrl('app_game_show_spl', ['id' => $game->getId()]).'notification'.$targetedPlayer,
            new Response(implode('_', $dataSent))
        );
    }
}
