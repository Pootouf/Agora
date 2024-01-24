<?php

namespace App\Controller\Game;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\SixQPService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SixQPController extends GameController
{

    private HubInterface $hub;

    private EntityManagerInterface $entityManager;

    private ChosenCardSixQPRepository $chosenCardSixQPRepository;

    private PlayerSixQPRepository $playerSixQPRepository;

    private SixQPService $service;

    public function __construct(HubInterface $hub, EntityManagerInterface $entityManager,
                                ChosenCardSixQPRepository $chosenCardSixQPRepository,
                                PlayerSixQPRepository $playerSixQPRepository,
                                SixQPService $service)
    {
        $this->hub = $hub;
        $this->entityManager = $entityManager;
        $this->chosenCardSixQPRepository = $chosenCardSixQPRepository;
        $this->playerSixQPRepository = $playerSixQPRepository;
        $this->service = $service;
    }

    public static function getPlayerFromUser(?UserInterface $user,
                                             int $gameId,
                                             PlayerSixQPRepository $playerSixQPRepository): ?PlayerSixQP
    {
        if ($user == null) {
            return null;
        }
        $id = $user->getId(); //TODO : add platform user

        return $playerSixQPRepository->findOneBy(['id' => $id, 'game' => $gameId]);
    }


    #[Route('/game/{id}/sixqp/select/{id}', name: 'app_game_sixqp_select')]
    public function selectCard(GameSixQP $game, CardSixQP $card): Response
    {
        $player = SixQPController::getPlayerFromUser($this->getUser(),
            $game->getId(),
            $this->playerSixQPRepository);
        if ($player == null) {
           return $this->redirectToRoute('/');
        }

        if ($this->doesPlayerAlreadyHavePlay($player)) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }

        try {
            $this->service->chooseCard($player, $card);
        } catch (\Exception) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }
        $response = $this->renderChosenCards($game);
        $this->publish($this->hub, $this->generateUrl('app_game_sixqp_select'), $response);

        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        if (!$this->isEndOfRoundReached($chosenCards, $game)) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }

        $response = $this->placeCardAutomatically($game, $player, $chosenCards);
        if ($response == null) {
            return $response;
        }


        $this->clearCards($chosenCards);
        return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
    }

    private function doesPlayerAlreadyHavePlay(PlayerSixQP $player) {
        $chosenCard = $this->chosenCardSixQPRepository->findOneBy(['player'=>$player->getId()]);
        return $chosenCard != null;
    }

    private function isEndOfRoundReached(array $chosenCards, GameSixQP $game): bool {
        return count($chosenCards) == count($game->getPlayerSixQPs());
    }

    private function placeCardAutomatically(GameSixQP $game, PlayerSixQP $player, array $chosenCards): ?Response {
        usort($chosenCards, function (ChosenCardSixQP $a, ChosenCardSixQP $b) {
            return $a->getCard()->getValue() > $b->getCard()->getValue();});

        $chosenCards = $this->removeAlreadyPlacedCard($chosenCards, $game);

        foreach ($chosenCards as $chosenCard) {
            $returnValue = $this->service->placeCard($chosenCard);
            if ($returnValue == -1) {
                $this->publish($this->hub,
                    $this->generateUrl('app_game_show', ['id' => $game->getId()]).($player->getId()),
                    new Response());
                return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
            } else {
                $this->publish($this->hub,
                    $this->generateUrl('app_game_show', ['id' => $game->getId()]),
                    $this->renderMainBoard($game));
            }
        }

        return null;
    }

    private function removeAlreadyPlacedCard(array $chosenCards, GameSixQP $game): array {
        for ($i = 0; $i < count($chosenCards); $i++) {
            foreach ($game->getRowSixQPs() as $row) {
                if ($row->isCardInRow($chosenCards[$i]->getCard())) {
                    array_splice($chosenCards, $i, 1);
                }
            }
        }
        return $chosenCards;
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
