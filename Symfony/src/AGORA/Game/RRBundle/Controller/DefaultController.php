<?php

namespace AGORA\Game\RRBundle\Controller;

use AGORA\Game\RRBundle\Entity\Game;
use AGORA\Game\RRBundle\Model\ActionType;
use AGORA\Game\RRBundle\Model\RailType;
use AGORA\Game\RRBundle\Model\RailwayType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AGORA\Game\RRBundle\Entity\Player;
use Error;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\UserBundle\Model\UserInterface;

class DefaultController extends Controller {

    public function indexAction()
    {
        $service = $this->container->get('agora_game.rr');

        $player = $service->getPlayer(1);
        $game = $service->getGame();
        try{
            $reponse = $this->render('AGORAGameRRBundle:Default:index.html.twig', [ 
                "spt" => $player->getRailWays()[RailwayType::stPetersburg], 
                "kiev" => $player->getRailWays()[RailwayType::kiev], 
                "trans"=> $player->getRailWays()[RailwayType::transsiberian],
                "playerId" => $player->getId(),
                "actions" => $game ->getBoard()->getActionsArray()
                ]);
        } catch(Error $e){
            echo "error :";
            var_dump($e);
        }
        return $reponse;
    }

    public function createGameAction() {
		//Récupération de l'utilisateur qui a créé la partie et vérification que celui-ci est connecté (si il ne l'est pas = refusé).
		$user = $this->getUser();
		if (!is_object($user) || !$user instanceof UserInterface) {
			return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}

		//On récupère l'objet Service (Singleton), qui nous permettra de faire le lien entre ce contrôleur et MorpionGameModel.
		$service = $this->container->get('agora_game.rr');

        $gameId = $service->createRoom($_POST['lobbyName'], $_POST['nbPlayers']);

        $service->joinPlayer($user->getId(), $gameId);

		return $this->redirect($this->generateUrl('agora_platform_gamelist_create'));
    }

    public function joinGameAction($gameId) {
        $user = $this->getUser();
		if (!is_object($user) || !$user instanceof UserInterface) {
			return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}
		$service = $this->container->get('agora_game.rr');
		//On essaie de faire rejoindre l'utilisateur à la partie. 
        //Si ce n'est pas possible alors la fonction retourne -1.
		$hasPlayerJoined = $service->joinPlayer($user, $gameId);
        if ($hasPlayerJoined == -1) {
            return $this->redirect($this->generateUrl('agora_platform_joingame'));
		}
        $service->initLeaderboard($user);
		return $this->redirectToRoute('agora_game_homepage_rr', ['gameId' => $gameId]);
    }

    public function playAction($gameId) {
        $user = $this->getUser();

		if (!is_object($user) || !$user instanceof UserInterface) {
			return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}

		$service = $this->container->get('agora_game.rr');
		$player = $service->getPlayer($gameId, $user->getId());
		if ($player == null) {
			return $this->redirect($this->generateUrl('agora_platform_joingame'));
		}

		$game = $service->getGame($gameId);

        return $this->render('AGORAGameRRBundle:Default:index.html.twig', 
			array(
				'gameId' => $gameId,
				'userId' => $user->getId(),
				'playerId' => $player->getId(),
				'players' => $service->getPlayers($gameId),
                'actions' => $service->getActions($gameId),
                'nbPlayers' => $game->getNbPlayer()
			));

    }
}
