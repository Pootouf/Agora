<?php

namespace AGORA\Game\SplendorBundle\Controller;

use AGORA\Game\GameBundle\Entity\Game;
use AGORA\Game\SplendorBundle\Entity\SplendorGame;
use AGORA\Game\SplendorBundle\Service\SplendorService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

class DefaultController extends Controller
{
    public function indexAction($gameId) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }

        $service = $this->container->get('agora_game.splendor');
        $player = $service->getPlayerFromUser($user, $gameId);
        if ($player == null) {
            return $this->redirect($this->generateUrl('agora_platform_joingame'));
        }

        $game = $service->getGame($gameId);
        $players = $service->getAllPlayers($gameId);

        return $this->render('AGORAGameSplendorBundle:Default:index.html.twig', array(
            'user' => $user,
            'game' => $game,
            'players' => $players
        ));
    }

    public function createAction() {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }

        //Recuperation du service
        //Creation de la partie à partir du service
        $service = $this->container->get('agora_game.splendor');
        $gameId = $service->createGame($_POST['lobbyName'], $_POST['nbPlayers'], $user);
        //$service->createCards($gameId);

        $service->initLeaderboard($user);
        $service->createPlayer($gameId, $user);

        return $this->redirect($this->generateUrl('agora_platform_gamelist_create'));
    }

    public function joinAction($gameId) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }

        $em = $this->getDoctrine()->getManager();
        $service = $this->container->get('agora_game.splendor');
        $gameInfo = $em->getRepository('AGORAPlatformBundle:GameInfo')->findOneBy(array('gameCode' => "spldr"));

        $game = $em->getRepository('AGORAGameGameBundle:Game')->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $gameInfo));
        $playerNb = $game->getPlayersNb();
        $spldrPlayerRepo = $em->getRepository('AGORAGameSplendorBundle:SplendorPlayer');
        $players = $spldrPlayerRepo->findBy(array('gameId' => $gameId));
        $plr = $spldrPlayerRepo->findBy(array('gameId' => $gameId, 'userId' => $user->getId()));

        if (count($players) < $playerNb && $user && $plr == null) {
            $player = $service->createPlayer($gameId, $user);
            if ($player != -1) {
                if (count($players) + 1 == $playerNb) {
                    $game->setState("started");
                    $em->persist($game);
                    $em->flush();
                }
            }
        }

        return $this->redirect($this->generateUrl('agora_platform_joingame'));
    }

    /**
	 * Fonction permettant de quitter la partie identifiée par $gameId si elle n'a pas encore commencée.
	 */
	public function quitAction($gameId) {
		//Récupération de l'utilisateur connecté
		$user = $this->getUser();

		if (!is_object($user) || !$user instanceof UserInterface) {
			return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}

		$service = $this->container->get('agora_game.splendor');
		$service->quitGame($user, $gameId);
	
		return $this->redirect($this->generateUrl('agora_platform_joingame'));
	}
    
    public function deleteAction($gameId) {
		$user = $this->getUser();
		if ($user == null || (!($user->hasRole('ROLE_ADMIN')) && !($user->hasRole('ROLE_MODO')))) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}
        $service = $this->container->get('agora_game.splendor');
        $service->supressGame($gameId);

        return $this->redirect($this->generateUrl('agora_platform_moderation'));
    }
}
