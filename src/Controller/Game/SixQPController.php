<?php

namespace App\Controller\Game;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\GameManagerService;
use App\Service\Game\LogService;
use App\Service\Game\SixQP\SixQPService;
use App\Service\Game\PublishService;
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
    private PublishService $publishService;
    private LogService $logService;

    public function __construct(EntityManagerInterface $entityManager,
                                ChosenCardSixQPRepository $chosenCardSixQPRepository,
                                PlayerSixQPRepository $playerSixQPRepository,
                                SixQPService $service,
                                LogService $logService,
                                PublishService $publishService)
    {
        parent::__construct($service);
        $this->publishService = $publishService;
        $this->entityManager = $entityManager;
        $this->chosenCardSixQPRepository = $chosenCardSixQPRepository;
        $this->playerSixQPRepository = $playerSixQPRepository;
        $this->logService = $logService;
        $this->service = $service;
    }


    #[Route('/game/{idGame}/sixqp/select/{idCard}', name: 'app_game_sixqp_select')]
    public function selectCard(#[MapEntity(id: 'idGame')] GameSixQP $game, #[MapEntity(id: 'idCard')] CardSixQP $card): Response
    {
        /** @var PlayerSixQP $player */
        $player = $this->service->getPlayerFromUser($this->getUser(),
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
        $response = $this->renderChosenCards($game, $player);
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
        $message = $player->getUsername() . " a choisi la carte " . $card->getValue()
            . "durant la partie " . $game->getId();
        $this->logService->sendLog($game, $player, $message);
        return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
    }

    #[Route('/game/{idGame}/sixqp/place/row/{idRow}')]
    public function placeCardOnRow(#[MapEntity(id: 'idGame')] GameSixQP $game,
        #[MapEntity(id: 'idRow')] RowSixQP $row) : Response{
        /** @var PlayerSixQP $player */
        $player = $this->service->getPlayerFromUser($this->getUser(),
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
        $message = $player->getUsername() . " a placé la carte " . $chosenCard->getCard()->getValue()
            . "durant la partie " . $game->getId() . "sur la ligne " . $row->getPosition();
        $this->logService->sendLog($game, $player, $message);
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
                $message = "Le système a placé la carte " . $chosenCard->getValue()
                    . "durant la partie " . $game->getId();
                $this->logService->sendLog($game, $player, $message);
                $this->publishService->publish(
                    $this->generateUrl('app_game_show', ['id' => $game->getId()]).'mainBoard',
                    $this->renderMainBoard($game));
            }
        }

        return null;
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
