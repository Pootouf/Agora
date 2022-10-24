<?php

namespace AGORA\Game\SQPBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AGORA\Game\SQPBundle\Entity\SQPGame;
use AGORA\Game\GameBundle\Entity\Game;
use AGORA\Game\SQPBundle\Entity\SQPPlayer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DefaultController extends Controller {

  /**
   * Création d'une partie de 6 qui prend
   */
  public function createLobbyAction() {
    $user = $this->getUser();
    if (!is_object($user) || !$user instanceof UserInterface) {
      return $this->redirect($this->generateUrl('agora_platform_homepage'));
    }

    $em = $this->getDoctrine()->getManager();
    $gameInfo = $em->getRepository('AGORAPlatformBundle:GameInfo')->findOneBy(array('gameCode' => "sqp"));
    $service = $this->container->get('agora_game_sqp.sqpapi');

    // Initialisation d'une partie dans la table sqp_game
    $sqpGame = new SQPGame();
    $sqpGame->setBoard(";;;");
    $deck = array();
    for ($i = 1; $i <= 104; ++$i) {
      $deck[$i - 1] = $i;
    }
    shuffle($deck);
    $deckToString = "";
    for ($i = 0; $i < 104; ++$i) {
      $deckToString .= intval($deck[$i]).",";
    }
    $sqpGame->setDeck($deckToString);
    $sqpGame->setTurn(1);
    $sqpGame->setState("waiting");
    $em->persist($sqpGame);
    $em->flush();

    // Initialisation du leaderboard
    $service->initLeaderboard($user);

    // Initialisation du joueur
    $player = new SQPPlayer();
    $player->setUserId($user);
    $player->setHand(",,,,,,,,,");
    $player->setScore(0);
    $player->setGameId($sqpGame);
    $player->setOrderTurn(0);
    $em->persist($player);
    $em->flush();

    // Initialisation de la partie dans la table 'game'
    $game = new Game();
    $game->setGameId($sqpGame->getId());
    $game->setGameInfoId($gameInfo);
    $game->setGameName($_POST['lobbyName']);
    $game->setPlayersNb($_POST['nbPlayers']);
    $game->setHostId($user);
    $game->setState("waiting");
    $game->setCreationDate(new \DateTime("now"));
    $em->persist($game);
    $em->flush();

    // Le joueur est redirige vers la page de creation de partie
    return $this->redirect($this->generateUrl('agora_platform_gamelist_create'));
  }


  /**
   * Fonction pour joindre un joueur à la partie de 6 qui prend d'ID $gameId.
   */
  public function joinLobbyAction($gameId) {
    $user = $this->getUser();
    if (!is_object($user) || !$user instanceof UserInterface) {
      return $this->redirect($this->generateUrl('agora_platform_homepage'));
    }

    $em = $this->getDoctrine()->getManager();
    $gameInfo = $em->getRepository('AGORAPlatformBundle:GameInfo')->findOneBy(array('gameCode' => "sqp"));

    $game = $em->getRepository('AGORAGameGameBundle:Game')->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $gameInfo));
    if ($game == null) {
      return $this->redirect($this->generateUrl('agora_platform_joingame'));
    }

    // Recuperation des joueurs du SQP
    $playersRep = $em->getRepository('AGORAGameSQPBundle:SQPPlayer');
    $player = $playersRep->findOneBy(array('gameId' => $gameId, 'userId' => $user->getId()));
    $players = $playersRep->getAllPlayersFromLobby($gameId);

    // Verification du nombre de joueurs de la partie
    $started = false;
    $playersNb = count($players);
    $playersNbRequired = $game->getPlayersNb();
    if ($playersNb == $playersNbRequired) {
      $started = true;
    }
    
    // Le joueur est initialise s'il n'existe pas et si la partie n'est pas pleine.
    if ($player == null && !$started) {
      $sqpGame = $em->getRepository('AGORAGameSQPBundle:SQPGame')->find($gameId);

      $player = new SQPPlayer();
      $player->setHand(",,,,,,,,,");
      $player->setScore(0);
      $player->setGameId($sqpGame);
      $player->setUserId($user);
      $player->setOrderTurn(0);

      $em->persist($player);
      $em->flush();

      //initialise le joueur dans le classement Elo si c'est ça première partie
      $service = $this->container->get('agora_game_sqp.sqpapi');
      $service->initLeaderboard($user);

      if (($playersNb + 1) == $playersNbRequired) {
        $game->setState("started");
        $em->persist($game);
        $em->flush();

        $sqpGame->setState("full");
        $em->persist($sqpGame);
        $em->flush();
      }
    }
    return $this->redirect($this->generateUrl('agora_platform_joingame'));
  }

  /**
   * Fonction pour rediriger vers la page de la partie.
   */
  public function playAction($gameId) {
		//Récupération de l'utilisateur connecté
		$user = $this->getUser();
		if (!is_object($user) || !$user instanceof UserInterface) {
			return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}

    $em = $this->getDoctrine()->getManager();
    $gameInfo = $em->getRepository('AGORAPlatformBundle:GameInfo')->findOneBy(array('gameCode' => "sqp"));
    $service = $this->container->get('agora_game_sqp.sqpapi');

    $game = $em->getRepository('AGORAGameGameBundle:Game')->findOneBy(array('gameId' => $gameId, 'gameInfoId' => $gameInfo));
    $sqpGame = $em->getRepository('AGORAGameSQPBundle:SQPGame')->find($gameId);
    $player = $em->getRepository('AGORAGameSQPBundle:SQPPlayer')->findOneBy(array('gameId' => $gameId, 'userId' => $user->getId()));
    if ($game == null || $sqpGame == null || $player == null) {
      return $this->redirect($this->generateUrl('agora_platform_joingame'));
    }
    
    $started = true;
    if ($sqpGame->getState() == "full" && $player->getUserId() == $game->getHostId()) {
      $started = false;
      $sqpGame->setState("ongoing");
      $em->persist($sqpGame);
      $em->flush();
    }
    $players = $service->getPlayersFromLobbyInOrder($gameId);

		return $this->render('AGORAGameSQPBundle:Default:index.html.twig', array(
      "game" => $game,
      "sqpGame" => $sqpGame,
      "player" => $player,
      "players" => $players,
      "started" => $started
    ));
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

		$service = $this->container->get('agora_game_sqp.sqpapi');
		$service->quitGame($user, $gameId);
	
		return $this->redirect($this->generateUrl('agora_platform_joingame'));
	}

  /**
   * Fonction de suppression d'une partie de 6 qui prend.
   */
  public function deleteAction($gameId) {
    $user = $this->getUser();
    if ($user == null || (!($user->hasRole('ROLE_ADMIN')) && !($user->hasRole('ROLE_MODO')))) {
      return $this->redirect($this->generateUrl('agora_platform_homepage'));
    }

    $service = $this->container->get('agora_game_sqp.sqpapi');
		$service->supressGame($gameId);

    return $this->redirect($this->generateUrl('agora_platform_moderation'));
  }

  private function shuffle($var) {
    for ($i = 103; $i > 0; $i--) {
      $j = floor(random() * ($i + 1));
      $x = $var[$i];
      $var[$i] = $var[$j];
      $var[$j] = $x;
    }
    return $var;
  }
}
