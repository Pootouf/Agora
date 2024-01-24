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

    #[Route('/game/{id}/sixqp/select/{id}', name: 'app_game_sixqp_select')]
    public function selectCard(HubInterface $hub,
        EntityManagerInterface $entityManager,
        ChosenCardSixQPRepository $chosenCardSixQPRepository,
        PlayerSixQPRepository $playerSixQPRepository,
        SixQPService $service,
        GameSixQP $game, CardSixQP $card): Response
    {
        $player = SixQPController::getPlayerFromUser($this->getUser(),
            $game->getId(),
            $playerSixQPRepository);
        if ($player == null) {
           return $this->redirectToRoute('/');
        }

        $chosenCard = $chosenCardSixQPRepository->findOneBy(['player'=>$player->getId()]);
        if ($chosenCard != null) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }

        try {
            $service->chooseCard($player, $card);
        } catch (\Exception) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }

        $response = $this->renderChosenCards($chosenCardSixQPRepository, $game);

        $this->publish($hub, $this->generateUrl('app_game_sixqp_select'), $response);

        $chosenCards = $chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        if (count($chosenCards) != count($game->getPlayerSixQPs())) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }

        usort($chosenCards, function (ChosenCardSixQP $a, ChosenCardSixQP $b) {
            return $a->getCard()->getValue() > $b->getCard()->getValue();});

        foreach ($chosenCards as $chosenCard) {
            $returnValue = $service->placeCard($chosenCard);
            if ($returnValue == -1) {
                $this->publish($hub,
                    $this->generateUrl('app_game_show', ['id' => $game->getId()]).($player->getId()),
                    new Response());
                return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
            } else {
                $this->publish($hub,
                    $this->generateUrl('app_game_show', ['id' => $game->getId()]),
                    $this->renderMainBoard($game));
            }
        }

        foreach ($chosenCards as $chosenCard) {
            $entityManager->remove($chosenCard);
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
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

    private function renderChosenCards(ChosenCardSixQPRepository $chosenCardSixQPRepository,
        GameSixQP $gameSixQP): Response
    {
        $cards = $chosenCardSixQPRepository->findBy(['game' => $gameSixQP->getId()]);
        return $this->render('Game/Six_qp/chosenCards.html.twig',
            ['chosenCards' => $cards]
        );
    }

    private function renderMainBoard(GameSixQP $gameSixQP): Response
    {
        return $this->render('Game/Six_qp/mainBoard.html.twig',
            ['rows' => $gameSixQP->getRowSixQPs()]);
    }
}
