<?php

namespace App\Controller\Game;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\SixQPService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SixQPController extends GameController
{
    #[Route('/game/{id]}/sixqp/select/{id}', name: 'app_game_sixqp_select')]
    public function selectCard(HubInterface $hub,
        PlayerSixQPRepository $playerSixQPRepository,
        SixQPService $service,
        GameSixQP $game, CardSixQP $card): Response
    {
        $player = $this->getPlayerFromUser($playerSixQPRepository);
        if ($player == null) {
           return $this->redirectToRoute('/');
        }

        try {
            $service->chooseCard($player, $card);
        } catch (\Exception) {
            return $this->redirectToRoute('app_game_sixqp_index');
        }

        $response = $this->render(''); //TODO: render chosen cards with valid information

        $this->publish($hub, $this->generateUrl('app_game_sixqp_select'), $response);

        return $this->redirectToRoute('app_game_sixqp_index');
    }

    private function getPlayerFromUser(PlayerSixQPRepository $playerSixQPRepository): ?PlayerSixQP
    {
        $user = $this->getUser();
        if ($user == null) {
            return null;
        }
        $id = $user->getId(); //TODO : add platform user

        return $playerSixQPRepository->findOneBy(['id' => $id]);
    }
}
