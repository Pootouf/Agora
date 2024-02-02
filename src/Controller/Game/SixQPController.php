<?php

namespace App\Controller\Game;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\GameService;
use App\Service\Game\PublishService;
use App\Service\Game\SixQPService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;

class SixQPController extends GameController
{
    private EntityManagerInterface $entityManager;
    private ChosenCardSixQPRepository $chosenCardSixQPRepository;
    private PlayerSixQPRepository $playerSixQPRepository;
    private SixQPService $service;
    private GameService $gameService;
    private PublishService $publishService;

    public function __construct(HubInterface $hub, EntityManagerInterface $entityManager,
                                ChosenCardSixQPRepository $chosenCardSixQPRepository,
                                PlayerSixQPRepository $playerSixQPRepository,
                                GameService $gameService,
                                SixQPService $service,
                                PublishService $publishService)
    {
        parent::__construct($gameService, $service);
        $this->publishService = $publishService;
        $this->entityManager = $entityManager;
        $this->chosenCardSixQPRepository = $chosenCardSixQPRepository;
        $this->playerSixQPRepository = $playerSixQPRepository;
        $this->service = $service;
        $this->gameService = $gameService;
    }


    #[Route('/game/{idGame}/sixqp/select/{idCard}', name: 'app_game_sixqp_select')]
    public function selectCard(#[MapEntity(id: 'idGame')] GameSixQP $game, #[MapEntity(id: 'idCard')] CardSixQP $card): Response
    {
        /** @var PlayerSixQP $player */
        $player = $this->gameService->getPlayerFromUser($this->getUser(),
            $game->getId(),
            $this->playerSixQPRepository);
        if ($player == null) {
           return $this->redirectToRoute('/');
        }

        if ($this->service->doesPlayerAlreadyHasPlayed($player)) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }

        try {
            $this->service->chooseCard($player, $card);
        } catch (\Exception) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }
        $response = $this->renderChosenCards($game);
        $this->publishService->publish(
            $this->generateUrl('app_game_show',
                ['id' => $game->getId()]).'chosenCards',
            $response);

        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        if (!$this->service->doesAllPlayersHaveChosen($game)) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }

        $response = $this->placeCardAutomatically($game, $player);
        if ($response != null) {
            return $response;
        }


        $this->clearCards($chosenCards);
        return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
    }

    #[Route('/game/{idGame}/sixqp/place/row/{idRow}')]
    public function placeCardOnRow(#[MapEntity(id: 'idGame')] GameSixQP $game,
        #[MapEntity(id: 'idRow')] RowSixQP $row) : Response{
        /** @var PlayerSixQP $player */
        $player = $this->gameService->getPlayerFromUser($this->getUser(),
            $game->getId(),
            $this->playerSixQPRepository);
        if ($player == null) {
            return $this->redirectToRoute('/');
        }
        $chosenCard = $player->getChosenCardSixQP();
        if ($chosenCard == null) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }
        if ($this->service->placeCard($chosenCard) != 0) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }
        $row->addCard($chosenCard->getCard());
        $this->entityManager->persist($row);

        $response = $this->placeCardAutomatically($game, $player);
        if ($response != null) {
            return $response;
        }

        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        $this->clearCards($chosenCards);
        return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
    }

    private function placeCardAutomatically(GameSixQP $game,
        PlayerSixQP $player): ?Response {

        $chosenCards = $this->service->getNotPlacedCard($game);

        usort($chosenCards, function (ChosenCardSixQP $a, ChosenCardSixQP $b) {
            return $a->getCard()->getValue() > $b->getCard()->getValue();});

        foreach ($chosenCards as $chosenCard) {
            $returnValue = $this->service->placeCard($chosenCard);
            if ($returnValue == -1) {
                $this->publishService->publish($this->generateUrl('app_game_show',
                        ['id' => $game->getId()]).'notifyPlayer'.($player->getId()),
                    new Response());
                return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
            } else {
                $this->publishService->publish(
                    $this->generateUrl('app_game_show', ['id' => $game->getId()]).'mainBoard',
                    $this->renderMainBoard($game));
            }
        }

        return null;
    }

    private function renderChosenCards(GameSixQP $gameSixQP): Response
    {
        $cards = $this->chosenCardSixQPRepository->findBy(['game' => $gameSixQP->getId()]);
        return $this->render('Game/Six_qp/chosenCards.html.twig',
            ['chosenCards' => $cards]
        );
    }

    private function renderMainBoard(GameSixQP $gameSixQP): Response
    {
        return $this->render('Game/Six_qp/mainBoard.html.twig',
            ['rows' => $gameSixQP->getRowSixQPs()]);
    }

    private function clearCards(array $chosenCards): void
    {
        foreach ($chosenCards as $chosenCard) {
            $this->entityManager->remove($chosenCard);
        }
        $this->entityManager->flush();
    }
}
