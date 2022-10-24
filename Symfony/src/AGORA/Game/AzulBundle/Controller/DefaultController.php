<?php

namespace AGORA\Game\AzulBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\UserBundle\Model\UserInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('AGORAGameAzulBundle:Default:index.html.twig');
    }

    /**
	 * Fonction permettant la création d'une partie. Les paramètres sont envoyé via la méthode POST.
	 */
	public function createGameAction() {
		//Récupération de l'utilisateur qui a créé la partie et vérification que celui-ci est connecté (si il ne l'est pas = refusé).
		$user = $this->getUser();
		if (!is_object($user) || !$user instanceof UserInterface) {
			return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}

		//On récupère l'objet Service (Singleton), qui nous permettra de faire le lien entre ce contrôleur et MorpionGameModel.
		$service = $this->container->get('agora_game.azul');

		//Création d'une partie de morpion. On récupère l'identifiant de la partie (Table : MorpionGame).
		$gameId = $service->createAzulGame($_POST['lobbyName'], $user, $_POST['nbPlayers']);

		//On initialise le classement du joueur s'il n'a pas déjà joué au jeu
		$service->initLeaderboard($user);
		$service->joinPlayer($user->getId(), $gameId);

		return $this->redirect($this->generateUrl('agora_platform_gamelist_create'));
	}

	/**
	 * Fonction permettant de rejoindre la partie d'Azul identifiée par $gameId.
	 */
	public function joinGameAction($gameId) {
		//Vérification que l'utilisateur est connecté (si il ne l'est pas = refusé).
		$user = $this->getUser();
		if (!is_object($user) || !$user instanceof UserInterface) {
			return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}

		$service = $this->container->get('agora_game.azul');
		//On essaie de faire rejoindre l'utilisateur à la partie. Si ce n'est pas possible alors la fonction retourne -1.
		$playerIsJoin = $service->joinPlayer($user, $gameId);

		//Si la partie est pleine (ou erreur), On redirige l'utilisateur vers une autre page.
		if ($playerIsJoin == -1) {
			
			return $this->redirect($this->generateUrl('agora_platform_joingame'));
		}
		//On initialise le classement du joueur s'il n'a pas déjà joué au jeu
		$service->initLeaderboard($user);
		//On redirige l'utilisateur vers la partie qu'il vient de rejoindre.
		return $this->redirectToRoute('agora_game_homepage_azul', ['gameId' => $gameId]);
		//return $this->PlayAction($gameId);
	}

	/**
	 * Fonction permettant de rejoindre la table de jeu d'Azul identifiée par $gameId.
	 */
	public function PlayAction($gameId) {
		//Récupération de l'utilisateur connecté
		$user = $this->getUser();

		if (!is_object($user) || !$user instanceof UserInterface) {
			return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}

		$service = $this->container->get('agora_game.azul');
		$player = $service->getPlayerEntityAzul($user->getId(), $gameId);
		if ($player == null) {
			return $this->redirect($this->generateUrl('agora_platform_joingame'));
		}

		$game = $service->getGame($gameId);

		return $this->render('AGORAGameAzulBundle:Default:index.html.twig', 
			array(
				'gameId' => $gameId,
				'userId' => $user->getId(),
				'playerId' => $player->getId(),
				'players' => $service->getPlayers($gameId),
				'username' => $service->getUserName($user->getId()),
				'nbFab' => count($game->getFabrics())
			));
	}

	/**
	 * Fonction permettant de quitter la partie d'Azul identifiée par $gameId si elle n'a pas encore commencée.
	 */
	public function quitAction($gameId) {
		//Récupération de l'utilisateur connecté
		$user = $this->getUser();

		if (!is_object($user) || !$user instanceof UserInterface) {
			return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}

		$service = $this->container->get('agora_game.azul');
		$service->quitGame($user, $gameId);
	
		return $this->redirect($this->generateUrl('agora_platform_joingame'));
	}

	public function deleteAction($gameId) {
		$user = $this->getUser();
		if ($user == null || (!($user->hasRole('ROLE_ADMIN')) && !($user->hasRole('ROLE_MODO')))) {
			return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}
	
		$service = $this->container->get('agora_game.azul');
		$service->supressGame($gameId);
	
		return $this->redirect($this->generateUrl('agora_platform_moderation'));
	  }	
}