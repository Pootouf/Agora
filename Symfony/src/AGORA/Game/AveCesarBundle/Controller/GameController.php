<?php

namespace AGORA\Game\AveCesarBundle\Controller;

use AGORA\Game\AveCesarBundle\Service\AveCesarService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use FOS\UserBundle\Model\UserInterface;

class GameController extends Controller
{
    public function indexAction($gameId)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }

        /** @var AveCesarService $service */
        $service = $this->container->get('agora_game.ave_cesar');
        $avcGame = $service->getGame($gameId);
        $player = $service->getPlayerFromUserId($gameId, $user->getId());
        if ($avcGame == null || $player == null) {
            return $this->redirect($this->generateUrl('agora_platform_joingame'));
        }

        //$service->initPlayers($gameId);

        return $this->render(
            '@AGORAGameAveCesar/Default/game.html.twig',
            array(
                'user' => $user,
                'game' => $avcGame,
                'player' => $player,
                'players' => $service->getAllPlayers($gameId),
                'maxPlayers' => $service->getMaxPlayer($gameId),
                'gameName' => $service->getGameName($gameId),
                'firstPlayer' => $service->getFirstPlayer($gameId),
                'nextPlayer' => $service->getNextPlayer($gameId),
                'boardId' => $service->getGame($gameId)->getBoardId()
            )
        );
    }

    //Création de la partie
    public function createLobbyAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }
        /** @var AveCesarService $service */
        $service = $this->container->get('agora_game.ave_cesar');
        $gameId = $service->createRoom($_POST['lobbyName'], $_POST['nbPlayers'], $user);
        $em = $this->getDoctrine()->getManager();
        $service->initLeaderboard($user, $em);
        $service->createPlayer($user, $gameId);
        return $this->redirect($this->generateUrl('agora_platform_gamelist_create'));
    }

    /**
     * Fonction pour joindre un joueur à la partie de 6 qui prend d'ID $gameId.
     */
    public function joinLobbyAction(SessionInterface $session, $gameId)
    {
        //echo "Un autre game id : ".$gameId."\n";
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }

        //initialise le joueur dans le classement Elo si c'est sa première partie
        $em = $this->getDoctrine()->getManager();
        $service = $this->container->get('agora_game.ave_cesar');

        if (!$service->playerAlreadyCreated($gameId, $user->getId())) {
            $result = $service->createPlayer($user, $gameId);
            // Game not Full
            if ($result != -1) {
                $service->initLeaderboard($user, $em);
            }
        }

        return $this->redirect($this->generateUrl('agora_platform_joingame'));
    }

    /**
     * Fonction permettant de quitter la partie de Morpion identifiée par $gameId si elle n'a pas encore commencée.
     */
    public function quitAction($gameId)
    {
        //Récupération de l'utilisateur connecté
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }

        $service = $this->container->get('agora_game.ave_cesar');
        $service->quitGame($user, $gameId);

        return $this->redirect($this->generateUrl('agora_platform_joingame'));
    }

    //supprime une partie
    public function deleteAction($gameId)
    {
        $user = $this->getUser();
        if ($user == null || (!($user->hasRole('ROLE_ADMIN')) && !($user->hasRole('ROLE_MODO')))) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }
        $service = $this->container->get('agora_game.ave_cesar');
        $service->supressGame($gameId);

        return $this->redirect($this->generateUrl('agora_platform_moderation'));
    }
}
