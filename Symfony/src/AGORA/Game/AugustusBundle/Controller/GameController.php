<?php
namespace AGORA\Game\AugustusBundle\Controller;

use AGORA\Game\AugustusBundle\Service\AugustusService;
use AGORA\Game\AugustusBundle\Entity\AugustusGame;

use AGORA\Game\Socket\ConnectionStorage;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use FOS\UserBundle\Model\UserInterface;


class GameController extends Controller {

    /**
     * @var $connectionStorage Les connexions liées a un jeu.
     */
    protected $connectionStorage;

    function __construct() {
        $this->connectionStorage = new ConnectionStorage();
    }


    //Création de la partie
    public function createRoomAction() {
        //Recupération de l'utilisateur qui a créé la partie et vérification que celui-çi est connecté
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }

        //recupere dnas la base de donnée la ou on stock les partie d'Augustus
        $service = $this->container->get('agora_game.augustus');

        //création de la salle de jeu et récupération de l'id
        $gameId = $service->createRoom($_POST['lobbyName'], $_POST['nbPlayers'], $user);
        $service->initLeaderboard($user);
        $service->joinPlayer($user, $gameId);

        return $this->redirect($this->generateUrl('agora_platform_gamelist_create'));
    }

    public function joinRoomAction(SessionInterface $session, $gameId) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }

        //initialise le joueur dans le classement Elo si c'est ça première partie
        $service = $this->container->get('agora_game.augustus');
        $service->joinPlayer($user, $gameId);

        return $this->redirect($this->generateUrl('agora_platform_joingame'));
    }


    public function indexAction($gameId) {
        //Récupération de l'utilisateur connecté
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }

        $service = $this->container->get('agora_game.augustus');
        $player = $service->getPlayerFromUser($user, $gameId);
        if ($player == null) {
            return $this->redirect($this->generateUrl('agora_platform_joingame'));
        }

        return $this->renderIndex($gameId, $player->getId());
    }


    private function renderIndex($gameId, $playerId) {
        $service = $this->container->get('agora_game.augustus');

        $game = $service->getGame($gameId);
        $player = $service->getPlayerFromId($playerId, $gameId);

        //Envoie Au twig tout les infomartions qu'il doit afficher
        return $this->render('AugustusBundle:Default:game.html.twig',
            array(
                'game'  => $game,
                'board' => $game->getBoard(),
                'me'    => $player
            )
        );
    }


    public function bodyAction($gameId, $playerId) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }
        
        $service = $this->container->get('agora_game.augustus');
        $augGame = $service->getGame($gameId);
        $player = $service->getPlayerFromId($playerId, $gameId);
        if ($player->getUserId()->getId() != $user->getId() || $player == null) {
            return $this->redirect($this->generateUrl('agora_platform_joingame'));
        }

        //Envoie Au twig tout les infomartions qu'il doit afficher
        if ($augGame->getState() == "endGame") {
            $game = $service->getAugustusGameFromGame($gameId);
            if ($game->getstate() != "finished") {
                // Calcul du classement
                $winner = null;
                $players = $this->manager->getRepository('AugustusBundle:AugustusPlayer')
                    ->findBy(array('gameId' => $gameId), array('score' => 'DESC'));
                foreach ($players as $player) {
                    if ($player->getIsWinner()) {
                        $winner = $player;
                        break;
                    }
                }
                $service->endGame($players, $gameId, $winner);

                $game->setState("finished");
                $em = $this->getDoctrine()->getManager();
                $em->persist($game);
                $em->flush();
            }
            return $this->render('AugustusBundle:Default:endBody.html.twig',
                array(
                    'game' => $augGame
                )
            );
        } else {
            return $this->render('AugustusBundle:Default:gameBody.html.twig',
                array(
                    'game'  => $augGame,
                    'board' => $augGame->getBoard(),
                    'me'    => $player,
                )
            );
        }
    }


    public function handleAction($conn, $gameId, $playerId, $action) {
        $service = $this->container->get('agora_game.augustus');

        if ($action->type == "connect") {
            $this->connectionStorage->addConnection($gameId, $playerId, $conn);

            foreach ($this->connectionStorage->getAllConnections($gameId) as $c) {
                echo "Did something\n";
                //$c->send($this->bodyAction($gameId, $player->getId()));
                $c->send("refresh");
            }

            return;
        }

        $player = $service->getPlayerFromId($playerId, $gameId);
        switch ($action->type) {
            case "legion":
                if (isset($action->removeToken)) {
                    for ($i = 0; $i < count($action->removeToken->token); $i++) {
                        $cards = $this->cleanArray($player->getCards()->toArray());
                        $card = $cards[$action->removeToken->card[$i]];

                        // $card->getCtrlTokens()[$action->removeToken->token[$i]];
                        $service->playerModel->removeLegionFromCard($player->getId(), $card->getId(), $card->getTokens()[$action->removeToken->token[$i]]);
                    }
                }
                if (isset($action->addToken)) {
                    for ($i = 0; $i < count($action->addToken->token); $i++) {
                        $cards = $this->cleanArray($player->getCards()->toArray());
                        $card = $cards[$action->addToken->card[$i]];
                        // $card->getCtrlTokens()[$action->addToken->token[$i]];
                        $service->playerModel->putLegionOnCard($player->getId(), $card->getId(), $card->getTokens()[$action->addToken->token[$i]]);
                    }
                }
                $service->setLastMovePlayedForPlayer($player->getId());

                $service->manager->flush();
                break;
            case "aveCesar":
                $game = $service->getGame($gameId);
                $board = $game->getBoard();
                $card = $board->getObjLine()[$action->aveCesar->card];
                $player->addCard($service->gameModel->boardModel->takeCardFromCenter($board->getId(), $card->getId()));
                $service->setLastMovePlayedForPlayer($player->getId());
                $service->manager->flush();
                break;
            case "removeAllLegion":
                if (isset($action->removeAllLegion)) {
                    $cards = $this->cleanArray($player->getCards()->toArray());
                    $card = $cards[$action->removeAllLegion];
                    $tokens = $card->getTokens();
                    foreach($tokens as $t) {
                        $service->cardModel->getBackToken($card->getId(), $t);
                    }
                }
                $service->setLastMovePlayedForPlayer($player->getId());
                $service->manager->flush();
                break;
            case "completeCard":
                $cards = $this->cleanArray($player->getCards()->toArray());
                $card = $cards[$action->completeCard];
                $legions = $service->cardModel->ctrlTokenNb($card->getId());
                $service->playerModel->completeCard($card->getId());
                $player = $card->getPlayer();
                $player->setLegion($player->getLegion() - count($card->getTokens()) + $legions);
                $service->manager->flush();
                break;
            case "takeLoot":
                if ($action->aveCesar->takeLoot) {
                    $service->gameModel->claimReward($gameId, $playerId);
                }
                $service->setLastMovePlayedForPlayer($player->getId());

                break;
            default:
                break;
        }

        $service->getPlayerFromId($playerId,$gameId)->setIsLock(true);
        $service->manager->flush();
        $conn->send("refresh");
        //Add case is finished
        if ($service->areAllPlayersReady($gameId)) {
            $players = $service->getPlayers($gameId);

            $service->gameModel->applyStep($gameId);

            foreach ($service->getPlayers($gameId) as $player) {
                $c = $this->connectionStorage->getConnection($gameId, $player->getId());
                $c->send($this->bodyAction($gameId, $player->getId()));
            }

            //return
        }
    }


    public function quitAction($gameId) {
		//Récupération de l'utilisateur connecté
		$user = $this->getUser();

		if (!is_object($user) || !$user instanceof UserInterface) {
			return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}

		$service = $this->container->get('agora_game.augustus');
		$service->quitGame($user, $gameId);
	
		return $this->redirect($this->generateUrl('agora_platform_joingame'));
	}


    //supprime une partie pour l'espace modération
    public function deleteAction($gameId) {
		$user = $this->getUser();
		if ($user == null || (!($user->hasRole('ROLE_ADMIN')) && !($user->hasRole('ROLE_MODO')))) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
		}
        
        $service = $this->container->get('agora_game.augustus');
        $service->supressGame($gameId);

        return $this->redirect($this->generateUrl('agora_platform_moderation'));
    }

    private function cleanArray($tab) {
        $units = array();
        foreach ($tab as $u) {
            if ($u != null) {
                array_push($units, $u);
            }
        }
        return $units;
    }
}
