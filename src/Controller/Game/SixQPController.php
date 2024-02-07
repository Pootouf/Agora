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
            if ($this->service->getValidRowForCard($card, $game->getRowSixQPs()) == null) {
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
            'needToChoose' => $needToChoose
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

        $response = $this->renderChosenCards($game, $player);
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp',
                ['id' => $game->getId()]).'chosenCards',
            $response);

        $response = $this->renderPersonalBoard($game, $player);
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp',
                ['id' => $game->getId()]).'personalBoard'.($player->getId()),
            $response);

        if ($this->service->doesAllPlayersHaveChosen($game)) {
            $response = $this->placeCards($game, $player);
            if ($response != null) {
                return $response;
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
        if ($this->service->getValidRowForCard($chosenCard, $game->getRowSixQPs()) != null) {
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

        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp',
                ['id' => $game->getId()]).'ranking',
            new Response(''.$player->getUsername().' '.$player->getDiscardSixQP()->getTotalPoints()));

        $response = $this->placeCards($game, $player);
        if ($response != null) {
            return $response;
        }
        return new Response('Card placed', Response::HTTP_OK);
    }

    private function placeCards(GameSixQP $game, PlayerSixQP $player): ?Response
    {
        if ($this->placeCardAutomatically($game, $player) != 0) {
            return new Response('Choose a row', Response::HTTP_OK);
        }

        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        $this->clearCards($chosenCards);
        $response = $this->renderChosenCards($game, $player);
        $this->publishService->publish(
            $this->generateUrl('app_game_show_sixqp',
                ['id' => $game->getId()]).'chosenCards',
            $response);
        return null;
    }

    private function placeCardAutomatically(GameSixQP $game,
        PlayerSixQP $player): int {

        $chosenCards = $this->service->getNotPlacedCard($game);

        usort($chosenCards, function (ChosenCardSixQP $a, ChosenCardSixQP $b) {
            return $a->getCard()->getValue() - $b->getCard()->getValue();});
        foreach ($chosenCards as $chosenCard) {
            $returnValue = $this->service->placeCard($chosenCard);
            if ($returnValue == -1) {
                $this->publishService->publish($this->generateUrl('app_game_show_sixqp',
                        ['id' => $game->getId()]).'notifyPlayer'.($chosenCard->getPlayer()->getId()),
                    new Response());
                return -1;
            } else {
                $this->publishService->publish(
                    $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'mainBoard',
                    $this->renderMainBoard($game, $player));
                $this->publishService->publish(
                    $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'ranking',
                    new Response(''.$player->getUsername().' '.$player->getDiscardSixQP()->getTotalPoints()));
                $this->publishService->publish(
                    $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'animRow'.$returnValue,
                    new Response($chosenCard->getCard()->getValue()));
                $message = "Le système a placé la carte " . $chosenCard->getCard()->getValue()
                    . "durant la partie " . $game->getId();
                $this->logService->sendLog($game, $player, $message);
            }
        }
        if ($this->service->isGameEnded($game)) {
            $this->publishService->publish(
                $this->generateUrl('app_game_show_sixqp', ['id' => $game->getId()]).'endOfGame',
                new Response());
        }
        return 0;
    }

    private function renderChosenCards(GameSixQP $gameSixQP, PlayerSixQP $playerSixQP): Response
    {
        $cards = $this->chosenCardSixQPRepository->findBy(['game' => $gameSixQP->getId()]);
        return $this->render('Game/Six_qp/chosenCards.html.twig',
            ['chosenCards' => $cards,
             'game' => $gameSixQP,
             'player' => $playerSixQP]
        );
    }

    private function renderPersonalBoard(GameSixQP $gameSixQP, PlayerSixQP $playerSixQP): Response
    {
        return $this->render('Game/Six_qp/PersonalBoard/personalBoard.html.twig',
            ['playerCards' => $playerSixQP->getCards(),
                'game' => $gameSixQP,
                'player' => $playerSixQP,
            ]
        );
    }

    private function renderRanking(GameSixQP $gameSixQP, PlayerSixQP $playerSixQP): Response
    {
        return $this->render('Game/Six_qp/ranking.html.twig',
            ['playersNumber' => $gameSixQP->getPlayerSixQPs()->count(),
                'game' => $gameSixQP,
                'player' => $playerSixQP,
                'ranking' => $this->service->getRanking($gameSixQP),
                'createdAt' => time(),
                'isGameFinished' => $this->service->isGameEnded($gameSixQP)
            ]
        );
    }

    private function renderMainBoard(GameSixQP $gameSixQP, PlayerSixQP $playerSixQP): Response
    {
        return $this->render('Game/Six_qp/mainBoard.html.twig',
            ['rows' => $gameSixQP->getRowSixQPs(),
             'game' => $gameSixQP,
             'player' => $playerSixQP,
             'needToChoose' => false
                ]
        );
    }

    private function clearCards(array $chosenCards): void
    {
        foreach ($chosenCards as $chosenCard) {
            $this->entityManager->remove($chosenCard);
        }
        $this->entityManager->flush();
    }
}
