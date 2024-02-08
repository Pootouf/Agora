<?php

namespace App\Controller\Game;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\LogService;
use App\Service\Game\SixQP\SixQPService;
use App\Service\Game\PublishService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class SixQPController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ChosenCardSixQPRepository $chosenCardSixQPRepository;
    private SixQPService $service;
    private PublishService $publishService;
    private LogService $logService;

    public function __construct(EntityManagerInterface $entityManager,
                                ChosenCardSixQPRepository $chosenCardSixQPRepository,
                                SixQPService $service,
                                LogService $logService,
                                PublishService $publishService)
    {
        $this->publishService = $publishService;
        $this->entityManager = $entityManager;
        $this->chosenCardSixQPRepository = $chosenCardSixQPRepository;
        $this->logService = $logService;
        $this->service = $service;
    }

    #[Route('/game/sixqp/{id}', name: 'app_game_show_sixqp')]
    public function showGame(GameSixQP $game): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        $isSpectator = false;
        if ($player == null) {
            $player = $game->getPlayerSixQPs()->get(0);
            $isSpectator = true;
        }

        $needToChoose = false;
        if ($this->service->doesAllPlayersHaveChosen($game)) {
            $cards = $this->service->getNotPlacedCard($game);
            usort($cards, function (ChosenCardSixQP $c1, ChosenCardSixQP $c2){
                return $c1->getCard()->getValue() - $c2->getCard()->getValue();
            });
            $card = $cards[0];
            if ($this->service->getValidRowForCard($game, $card) == null) {
                $needToChoose = $card->getPlayer()->getId() == $player->getId();
            }
        }

        return $this->render('/Game/Six_qp/index.html.twig', [
            'game' => $game,
            'chosenCards' => $chosenCards,
            'playerCards' => $player->getCards(),
            'playersNumber' => count($game->getPlayerSixQPs()),
            'ranking' => $this->service->getRanking($game),
            'player' => $player,
            'createdAt' => time(),
            'rows' => $game->getRowSixQPs(),
            'isGameFinished' => $this->service->isGameEnded($game),
            'isSpectator' => $isSpectator,
            'needToChoose' => $needToChoose,
        ]);

    }

    #[Route('/game/{idGame}/sixqp/spectator/get/{idPlayer}', name: 'app_game_sixqp_spectator_board')]
    public function getPersonalBoardOfPlayer(#[MapEntity(id: 'idGame')] GameSixQP $game,
                                             #[MapEntity(id: 'idPlayer')] PlayerSixQP $playerToDisplay): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player != null) {
            return new Response('Not a spectator', Response::HTTP_FORBIDDEN);
        }
        return $this->render('Game/Six_qp/Spectator/spectatorBoard.html.twig',
            ['playerCards' => $playerToDisplay->getCards(),
            'game' => $game,
            'player' => $playerToDisplay,
        ]);
    }


    #[Route('/game/{idGame}/sixqp/select/{idCard}', name: 'app_game_sixqp_select')]
    public function selectCard(
        #[MapEntity(id: 'idGame')] GameSixQP $game,
        #[MapEntity(id: 'idCard')] CardSixQP $card): Response
    {
        /** @var PlayerSixQP $player */
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
           return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }

        if ($this->service->doesPlayerAlreadyHasPlayed($player)) {
            return new Response('Player already have played', Response::HTTP_UNAUTHORIZED);
        }

        try {
            $this->service->chooseCard($player, $card);
        } catch (Exception) {
            return new Response('Impossible to choose', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $message = $player->getUsername() . " a choisi la carte " . $card->getValue()
            . "durant la partie " . $game->getId();
        $this->logService->sendLog($game, $player, $message);

        $this->publishChosenCards($game);
        $this->publishMainBoard($game);
        $this->publishPersonalBoard($game, $player);

        if ($this->service->doesAllPlayersHaveChosen($game)) {
            try {
                $this->managePlacementOfCards($game);
            } catch (Exception $e) {
                return new Response('Need to choose', Response::HTTP_OK);
            }
        }
        return new Response('Card placed', Response::HTTP_OK);
    }

    #[Route('/game/{idGame}/sixqp/place/row/{idRow}', name: 'app_game_sixqp_placecardonrow')]
    public function placeCardOnRow(#[MapEntity(id: 'idGame')] GameSixQP $game,
        #[MapEntity(id: 'idRow')] RowSixQP $row) : Response{
        /** @var PlayerSixQP $player */
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        if ($player == null) {
            return new Response('Invalid player', Response::HTTP_FORBIDDEN);
        }
        $chosenCard = $player->getChosenCardSixQP();
        if ($chosenCard == null) {
            return new Response('Choose a card', Response::HTTP_UNAUTHORIZED);
        }
        if ($this->service->getValidRowForCard($game, $chosenCard) != null) {
            return new Response("Can't place the card here", Response::HTTP_METHOD_NOT_ALLOWED);
        }
        $message = $player->getUsername() . " a placé la carte " . $chosenCard->getCard()->getValue()
            . "durant la partie " . $game->getId() . "sur la ligne " . $row->getPosition();
        $this->logService->sendLog($game, $player, $message);

        $this->service->addRowToDiscardOfPlayer($player, $row);
        $row = $game->getRowSixQPs()->get($game->getRowSixQPs()->indexOf($row));
        $row->addCard($chosenCard->getCard());
        $this->entityManager->persist($game);
        $this->entityManager->persist($row);
        $this->entityManager->flush();

        $this->publishNewScoreForPlayer($game, $player);

        try {
            $this->managePlacementOfCards($game);
        } catch (Exception $e) {
            return new Response('Need to choose', Response::HTTP_OK);
        }
        return new Response('Card placed', Response::HTTP_OK);
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
        } else if (!$this->service->hasCardLeft($game->getPlayerSixQPs())){
            try {
                $this->service->initializeNewRound($game);
            } catch (Exception $e) {
                //TODO: log error
            }
            foreach ($game->getPlayerSixQPs() as $player) {
                $this->publishPersonalBoard($game, $player);
                $this->publishMainBoard($game);
            }
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
        usort($chosenCards, function (ChosenCardSixQP $a, ChosenCardSixQP $b) {
            return $a->getCard()->getValue() - $b->getCard()->getValue();});
        foreach ($chosenCards as $chosenCard) {
            $row = $this->service->getValidRowForCard($game, $chosenCard);
            $player = $chosenCard->getPlayer();
            if ($row == null) {
                $this->publishNotificationForPlayer($game, $player);
                throw new Exception("Can't place automatically the card");
            } else {
                $returnValue = $this->service->placeCardIntoRow($chosenCard, $row);
                $this->publishMainBoard($game);
                $this->publishNewScoreForPlayer($game, $player);
                if ($returnValue != 0) {
                    $this->publishAnimRowClear($game, $row->getPosition());
                }
                $message = "Le système a placé la carte " . $chosenCard->getCard()->getValue()
                    . "durant la partie " . $game->getId();
                $this->logService->sendLog($game, $player, $message);
            }
        }
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        $this->service->clearCards($chosenCards);
        $this->publishChosenCards($game);
    }

    private function publishEndOfGame(GameSixQP $game): void
    {
        $winner = $this->service->getWinner($game);
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'endOfGame',
            new Response($winner?->getUsername()));
    }

    private function publishNotificationForPlayer(GameSixQP $game, PlayerSixQP $player): void
    {
        $this->publishService->publish($this->generateUrl('app_game_show_sixqp',
                ['id' => $game->getId()]).'notifyPlayer'.($player->getId()),
            new Response());
    }

    private function publishAnimRowClear(GameSixQP $game, int $rowPosition): void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'animRow',
            new Response($rowPosition));
    }

    private function publishNewScoreForPlayer(GameSixQP $game, PlayerSixQP $player): void
    {
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp',
                ['id' => $game->getId()]).'ranking',
            new Response(''.$player->getUsername().' '.$player->getDiscardSixQP()->getTotalPoints()));
    }

    private function publishChosenCards(GameSixQP $game): void
    {
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        $response = $this->render('Game/Six_qp/chosenCards.html.twig',
            [
                'chosenCards' => $chosenCards,
                'playerNumbers' => $game->getPlayerSixQPs()->count(),
                'game'=>$game,
            ]
        );
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp',
                ['id' => $game->getId()]).'chosenCards',
            $response);
    }

    private function publishPersonalBoard(GameSixQP $game, PlayerSixQP $player): void
    {
        $cards = $player->getCards()->toArray();
        usort($cards, function (CardSixQP $c1, CardSixQP $c2) {
           return $c1->getValue() > $c2->getValue();
        });
        $response =  $this->render('Game/Six_qp/PersonalBoard/personalBoard.html.twig',
            ['playerCards' => $cards,
                'game' => $game,
                'player' => $player,
            ]
        );
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp',
                ['id' => $game->getId()]).'personalBoard'.($player->getId()),
            $response);
    }

    private function publishMainBoard(GameSixQP $game): void
    {
        $response =  $this->render('Game/Six_qp/mainBoard.html.twig',
            ['rows' => $game->getRowSixQPs(),
             'game' => $game,
             'needToChoose' => false,
                ]
        );
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'mainBoard',
            $response);
    }
}
