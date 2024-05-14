<?php

namespace App\Controller\Game;

use App\Entity\Game\DTO\GameTranslation;
use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Entity\Game\SixQP\SixQPTranslation;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Service\Game\LogService;
use App\Service\Game\MessageService;
use App\Service\Game\SixQP\SixQPService;
use App\Service\Game\PublishService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class SixQPController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ChosenCardSixQPRepository $chosenCardSixQPRepository;
    private SixQPService $service;
    private PublishService $publishService;
    private LogService $logService;
    private MessageService $messageService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ChosenCardSixQPRepository $chosenCardSixQPRepository,
        SixQPService $service,
        LogService $logService,
        PublishService $publishService,
        MessageService $messageService
    ) {
        $this->publishService = $publishService;
        $this->entityManager = $entityManager;
        $this->chosenCardSixQPRepository = $chosenCardSixQPRepository;
        $this->logService = $logService;
        $this->service = $service;
        $this->messageService = $messageService;
    }

    #[Route('/game/sixqp/{id}', name: 'app_game_show_sixqp')]
    public function showGame(GameSixQP $game): Response
    {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        $isSpectator = false;
        $needToChoose = false;
        if ($player == null) {
            $player = $game->getPlayers()->get(0);
            $isSpectator = true;
        } else {
            if ($this->service->doesAllPlayersHaveChosen($game)) {
                $cards = $this->service->getNotPlacedCard($game);
                usort($cards, function (ChosenCardSixQP $c1, ChosenCardSixQP $c2) {
                    return $c1->getCard()->getValue() - $c2->getCard()->getValue();
                });
                $card = $cards[0];
                if ($this->service->getValidRowForCard($game, $card) == null) {
                    $needToChoose = $card->getPlayer()->getId() == $player->getId();
                }
            }
        }

        $messages = $this->messageService->receiveMessage($game->getId());

        return $this->render('/Game/Six_qp/index.html.twig', [
            'game' => $game,
            'chosenCards' => $chosenCards,
            'playerCards' => $player->getCards(),
            'playersNumber' => count($game->getPlayers()),
            'ranking' => $this->service->getRanking($game),
            'player' => $player,
            'rows' => $game->getRowSixQPs(),
            'isGameFinished' => $this->service->isGameEnded($game),
            'isSpectator' => $isSpectator,
            'needToChoose' => $needToChoose,
            'messages' => $messages,
            'chosenCard' => null,
            'isNewRound' => false,
        ]);

    }


    #[Route('/game/{idGame}/sixqp/select/{idCard}', name: 'app_game_sixqp_select')]
    public function selectCard(
        #[MapEntity(id: 'idGame')] GameSixQP $game,
        #[MapEntity(id: 'idCard')] CardSixQP $card
    ): Response {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        /** @var PlayerSixQP $player */
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(
                GameTranslation::INVALID_PLAYER_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }

        if ($this->service->doesPlayerAlreadyHasPlayed($player)) {
            return new Response(
                SixQPTranslation::RESPONSE_ALREADY_PLAYED,
                Response::HTTP_UNAUTHORIZED
            );
        }

        try {
            $this->service->chooseCard($player, $card);
        } catch (Exception) {
            $this->logService->sendPlayerLog(
                $game,
                $player,
                $player->getUsername() . SixQPTranslation::CANNOT_CHOOSE_CARD . $card->getValue()
            );
            return new Response(
                SixQPTranslation::RESPONSE_CANNOT_CHOOSE_CARD,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $message = $player->getUsername() . SixQPTranslation::CHOOSE_CARD . $card->getValue()
            . SixQPTranslation::DURING_GAME . $game->getId();
        $this->logService->sendPlayerLog($game, $player, $message);

        $this->publishChosenCards($game);
        $this->publishPersonalBoard($game, $player);

        if ($this->service->doesAllPlayersHaveChosen($game)) {
            try {
                $this->managePlacementOfCards($game);
            } catch (Exception) {
                return new Response(
                    SixQPTranslation::RESPONSE_NEED_TO_CHOOSE,
                    Response::HTTP_OK
                );
            }
        }
        return new Response(SixQPTranslation::RESPONSE_CARD_PLACED, Response::HTTP_OK);
    }

    #[Route('/game/{idGame}/sixqp/place/row/{idRow}', name: 'app_game_sixqp_placecardonrow')]
    public function placeCardOnRow(
        #[MapEntity(id: 'idGame')] GameSixQP $game,
        #[MapEntity(id: 'idRow')] RowSixQP $row
    ): Response {
        if ($game->isPaused() || !$game->isLaunched()) {
            return new Response(
                GameTranslation::GAME_NOT_ACCESSIBLE_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        /** @var PlayerSixQP $player */
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response(
                GameTranslation::INVALID_PLAYER_MESSAGE,
                Response::HTTP_FORBIDDEN
            );
        }
        $chosenCard = $player->getChosenCardSixQP();
        if ($chosenCard == null) {
            return new Response(
                SixQPTranslation::RESPONSE_CHOOSE_CARD,
                Response::HTTP_UNAUTHORIZED
            );
        }
        if ($this->service->getValidRowForCard($game, $chosenCard) != null) {
            $this->logService->sendPlayerLog(
                $game,
                $player,
                $player->getUsername() . SixQPTranslation::CANNOT_PLACE_CARD_ON_LINE
                . $row->getPosition()
            );
            return new Response(
                SixQPTranslation::RESPONSE_CARD_INVALID_POSITION,
                Response::HTTP_METHOD_NOT_ALLOWED
            );
        }
        $message = $player->getUsername() . SixQPTranslation::PLACE_CARD
            . $chosenCard->getCard()->getValue()
            . SixQPTranslation::DURING_GAME . $game->getId()
            . SixQPTranslation::ON_LINE . $row->getPosition();
        $this->logService->sendPlayerLog($game, $player, $message);

        $this->service->addRowToDiscardOfPlayer($player, $row);
        $row = $game->getRowSixQPs()->get($game->getRowSixQPs()->indexOf($row));
        $row->addCard($chosenCard->getCard());
        $this->entityManager->persist($game);
        $this->entityManager->persist($row);
        $this->entityManager->flush();

        $this->publishAnimRowClear($game, $row);
        $this->publishNewScoreForPlayer($game, $player);
        $this->publishMainBoard($game, $chosenCard);
        $this->publishAnimChosenCard($game, $chosenCard);

        try {
            $this->managePlacementOfCards($game);
        } catch (Exception) {
            return new Response(
                SixQPTranslation::RESPONSE_NEED_TO_CHOOSE,
                Response::HTTP_OK
            );
        }
        return new Response(
            SixQPTranslation::RESPONSE_CARD_PLACED,
            Response::HTTP_OK
        );
    }

    /**
     * managePlacementOfCards: place automatically the card and manage the endOfRound
     * @param GameSixQP $game the game to manage
     * @return void
     * @throws Exception if the player need to choose
     */
    private function managePlacementOfCards(GameSixQP $game): void
    {
        $this->placeCardAutomatically($game);
        $this->manageEndOfRoundSystemActions($game);
    }

    /**
     * manageEndOfRoundSystemActions: end the game if needed, or create a new round if needed
     */
    private function manageEndOfRoundSystemActions(GameSixQP $game): void
    {

        if ($this->service->isGameEnded($game)) {
            $this->publishEndOfGame($game);
        } elseif (!$this->service->hasCardLeft($game->getPlayers())) {
            try {
                $this->service->initializeNewRound($game);
                $this->logService->sendSystemLog(
                    $game,
                    SixQPTranslation::NEW_ROUND . $game->getId()
                );
            } catch (Exception) {
                $this->logService->sendSystemLog(
                    $game,
                    SixQPTranslation::CANNOT_CREATE_NEW_ROUND . $game->getId()
                );
            }
            $this->publishAnimAllRowClear($game);
            foreach ($game->getPlayers() as $player) {
                $this->publishPersonalBoard($game, $player);
            }
            $this->publishMainBoard($game, null, true);
        }
    }

    /**
     * placeCardAutomatically : place the chosen cards on the rows if one is available, notify the player
     *                          and send exception if there is no valid row
     * @param GameSixQP $game the game to manage
     * @throws Exception if the card can't be placed
     */
    private function placeCardAutomatically(GameSixQP $game): void
    {
        $chosenCards = $this->service->getNotPlacedCard($game);
        foreach ($chosenCards as $chosenCard) {
            $row = $this->service->getValidRowForCard($game, $chosenCard);
            $player = $chosenCard->getPlayer();
            if ($row == null) {
                $this->publishNotificationForPlayer($game, $player);
                throw new Exception("Can't place automatically the card");
            } else {
                if ($this->service->placeCardIntoRow($chosenCard, $row) != 0) {
                    $this->publishAnimRowClear($game, $row);
                    $this->publishNewScoreForPlayer($game, $player);
                }
                $this->publishMainBoard($game, $chosenCard);
                $this->publishAnimChosenCard($game, $chosenCard);
                $message = SixQPTranslation::SYSTEM_PLACE_CARD . $chosenCard->getCard()->getValue()
                    . SixQPTranslation::DURING_GAME . $game->getId()
                    . SixQPTranslation::ON_LINE . $row->getPosition();
                $this->logService->sendSystemLog($game, $message);
            }
        }
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        $this->service->clearCards($chosenCards);
        $this->publishChosenCards($game);
    }

    private function publishEndOfGame(GameSixQP $game): void
    {
        $winner = $this->service->getWinner($game);
        $this->logService->sendPlayerLog(
            $game,
            $winner,
            $winner->getUsername() . SixQPTranslation::PLAYER_WIN . $game->getId()
        );
        $this->logService->sendSystemLog($game, SixQPTranslation::GAME_STRING
            . $game->getId() . SixQPTranslation::GAME_ENDED);
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'endOfGame',
            new Response($winner?->getUsername())
        );
    }

    private function publishNotificationForPlayer(GameSixQP $game, PlayerSixQP $player): void
    {
        $this->publishService->publish(
            $this->generateUrl(
                'app_game_show_sixqp',
                ['id' => $game->getId()]
            ).'notifyPlayer'.($player->getId()),
            new Response()
        );
    }

    private function publishAnimRowClear(GameSixQP $game, RowSixQP $row): void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'animRow',
            new Response($row->getId())
        );
    }

    private function publishAnimAllRowClear(GameSixQP $game): void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'animAllRow',
            new Response()
        );
    }

    private function publishNewScoreForPlayer(GameSixQP $game, PlayerSixQP $player): void
    {
        $this->publishService->publish(
            $this->generateUrl(
                'app_game_show_sixqp',
                ['id' => $game->getId()]
            ).'ranking',
            new Response(''.$player->getUsername().' '.$player->getScore())
        );
    }

    private function publishChosenCards(GameSixQP $game): void
    {
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        $response = $this->render(
            'Game/Six_qp/MainBoard/chosenCards.html.twig',
            [
                'chosenCards' => $chosenCards,
                'playerNumbers' => $game->getPlayers()->count(),
                'game' => $game,
            ]
        );
        $this->publishService->publish(
            $this->generateUrl(
                'app_game_show_sixqp',
                ['id' => $game->getId()]
            ).'chosenCards',
            $response
        );
    }

    private function publishPersonalBoard(GameSixQP $game, PlayerSixQP $player): void
    {
        foreach (['player', 'spectator'] as $role) {
            $cards = $player->getCards()->toArray();
            usort($cards, function (CardSixQP $c1, CardSixQP $c2) {
                return $c1->getValue() - $c2->getValue();
            });
            $isSpectator = $role == 'spectator';
            $response = $this->render(
                'Game/Six_qp/PersonalBoard/personalBoard.html.twig',
                [
                    'playerCards' => $cards,
                    'game' => $game,
                    'player' => $player,
                    'isSpectator' => $isSpectator,
                ]
            );
            $route = $isSpectator ? $role : ($player->getId()).$role;
            $this->publishService->publish(
                $this->generateUrl(
                    'app_game_show_sixqp',
                    ['id' => $game->getId()]
                ) . 'personalBoard' . $route,
                $response
            );
        }
    }

    private function publishMainBoard(
        GameSixQP $game,
        ?ChosenCardSixQP $chosenCardSixQP,
        bool $isNewRound = false
    ): void {
        $response =  $this->render(
            'Game/Six_qp/MainBoard/mainBoard.html.twig',
            [
                'rows' => $game->getRowSixQPs(),
                'game' => $game,
                'needToChoose' => false,
                'chosenCard' => $chosenCardSixQP,
                'isNewRound' => $isNewRound,
            ]
        );
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'mainBoard',
            $response
        );
        if ($isNewRound) {
            $this->publishService->publish(
                $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'animFirstCards',
                new Response()
            );
        }
    }

    private function publishAnimChosenCard(GameSixQP $game, ChosenCardSixQP $chosenCard): void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'animChosenCard',
            new Response($chosenCard->getCard()->getId())
        );
    }
}
