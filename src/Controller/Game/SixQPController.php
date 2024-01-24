<?php

namespace App\Controller\Game;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\SixQPService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SixQPController extends GameController
{

    #[Route('/game/{id}/sixqp/select/{id}', name: 'app_game_sixqp_select')]
    public function selectCard(HubInterface $hub,
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

        try {
            $service->chooseCard($player, $card);
        } catch (\Exception) {
            return $this->redirectToRoute('app_game_show', ['id'=>$game->getId()]);
        }

        $response = $this->renderChosenCards($chosenCardSixQPRepository, $game);

        $this->publish($hub, $this->generateUrl('app_game_sixqp_select'), $response);

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
}
