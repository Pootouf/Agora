<?php

namespace AGORA\PlatformBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AGORA\PlatformBundle\Entity\Contact;
use AGORA\PlatformBundle\Form\ContactType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use FOS\UserBundle\Model\UserInterface;

use AGORA\Game\SQPBundle\Entity\SQPPlayer as SQPPlayer;

class DefaultController extends Controller
{
    /*
     * Page d'Accueil de la plateforme.
     */
    public function indexAction()
    {
        $user = $this->getUser();
        if ($user != null && $user->hasRole('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('agora_platform_moderation'));
        }
        return $this->render('AGORAPlatformBundle:Accueil:accueil.html.twig');
    }

    public function theProjectAction()
    {
        return $this->render('AGORAPlatformBundle:Accueil:theProject.html.twig');
    }

    /*
     * Page Contact. 
     */
    public function contactAction(Request $request) {
		$user = $this->getUser();
		if ($user != null && $user->hasRole('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('agora_platform_moderation'));
        }
        $em = $this->getDoctrine()->getManager();
        $gameInfoRepository = $em->getRepository('AGORAPlatformBundle:GameInfo');
        $allGameInfo = $gameInfoRepository->findAll();
        $Contact = new Contact();
        $form = $this->createForm(ContactType::class, $Contact);
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $message = \Swift_Message::newInstance()
                ->setSubject('Demande de contact')
                ->setFrom('agora.dev.test@gmail.com')   
                ->setTo('agora.dev.test@gmail.com')   //adresse qui receptionnera la demande de contact
                ->setBody($this->renderView('AGORAPlatformBundle:mail:contactEmail.txt.twig', array('contact' => $Contact)));
                $this->get('mailer')->send($message);
                $this->addFlash('contact-notice', 'Votre message a bien été transmis. Merci !');
                return $this->redirect($this->generateUrl('agora_platform_contact'));
            }
        }
        return $this->render('AGORAPlatformBundle:Accueil:contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function leaderboardAction($game = null) {
		$user = $this->getUser();
		if ($user != null && $user->hasRole('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('agora_platform_moderation'));
        }
        $em = $this->getDoctrine()->getManager();
        $leaderboardRepository = $em->getRepository('AGORAPlatformBundle:Leaderboard');
        $gameInfoRepository = $em->getRepository('AGORAPlatformBundle:GameInfo');
        $allGameInfo = $gameInfoRepository->findAll();
        if ($game == "*") {
            return $this->render('AGORAPlatformBundle:Accueil:listLeaderboard.html.twig',array(
                "gameList" => $allGameInfo
            ));
        }
        
        $gameInfo = $gameInfoRepository->find($game);
        $leaderboard = $leaderboardRepository->getLeaderboardWithUser($game);

        return $this->render('AGORAPlatformBundle:Accueil:leaderboard.html.twig', array(
            "gameInfo" => $gameInfo,
            "leaderboard" => $leaderboard
        ));
    }

    public function setAdminAction() {
        $user = $this->getUser();
        $user->addRole('ROLE_ADMIN');

        $em = $this->getDoctrine()->getEntityManager();

        $em->persist($user);
        $em->flush();

        return $this->render('AGORAPlatformBundle:Accueil:acceuil.html.twig');
    }
    
    public function gameListAction($game = null) {
		$user = $this->getUser();
		if ($user != null && $user->hasRole('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('agora_platform_moderation'));
        }
        $em = $this->getDoctrine()->getManager();
        $gameInfoRepository = $em->getRepository('AGORAPlatformBundle:GameInfo');

        if (isset($game) && $game != "*") {
            $gameInfo = $gameInfoRepository->find($game);

            if ($gameInfo == null) {
                throw $this->createNotFoundException('La page demandée n\'existe pas ! ');
            }
            return $this->render('AGORAPlatformBundle:Accueil:gameDetails.html.twig',array(
                "gameInfo" => $gameInfo));   
        } else {
            $allGameInfo = $gameInfoRepository->findAll();
            return $this->render('AGORAPlatformBundle:Game:gameList.html.twig', array(
                "gameList" => $allGameInfo
            ));
        }
	}

    public function gameListCreateAction($game = null) {
		$user = $this->getUser();
		if ($user != null && $user->hasRole('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('agora_platform_moderation'));
        }
        $em = $this->getDoctrine()->getManager();
        $gameInfoRepository = $em->getRepository('AGORAPlatformBundle:GameInfo');

        if (isset($game) && $game != "*") {
            $gameInfo = $gameInfoRepository->find($game);

            if ($gameInfo == null) {
                throw $this->createNotFoundException('La page demandée n\'existe pas ! ');
            }
            return $this->render('AGORAPlatformBundle:Accueil:gameDetails.html.twig',array(
                "gameInfo" => $gameInfo));
        } else {
            if (!is_object($user) || !$user instanceof UserInterface) {
                return $this->redirect($this->generateUrl('agora_platform_homepage'));
            }
            $allGameInfo = $gameInfoRepository->findAll();
            return $this->render('AGORAPlatformBundle:Game:gameListCreate.html.twig',array(
                "gameList" => $allGameInfo));
        }
    }

    public function profileAction() {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $gameInfoRepository = $em->getRepository('AGORAPlatformBundle:GameInfo');
        $allGameInfo = $gameInfoRepository->findAll();
        
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('AGORAPlatformBundle:Profile:profile.html.twig', array(
            'user' => $user
        ));
    }

    public function createGameAction($gameId = null) {
		$user = $this->getUser();
		if ($user != null && $user->hasRole('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('agora_platform_moderation'));
        }
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            //throw new AccessDeniedException('Accès refusé, l\'utilisateur n\'est pas connecté.');
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }
        $em = $this->getDoctrine()->getManager();
        $gameInfoRepository = $em->getRepository('AGORAPlatformBundle:GameInfo');
        $gameInfo = null;
        if (isset($gameId) && $gameId != "*") {
            $gameInfo = $gameInfoRepository->find($gameId);
            if ($gameInfo == null) {
                throw $this->createNotFoundException('La page demandée n\'existe pas ! ');
            }
        } else {
            throw $this->createNotFoundException('La page demandée n\'existe pas ! ');
        }

        return $this->render('AGORAPlatformBundle:Accueil:createGame.html.twig', array(
            "gameInfo" => $gameInfo,
            "user" => $user,
        ));
    }

    private function getPlayersForAllGames($games, $manager) {
        $services['avc'] = $this->container->get('agora_game.ave_cesar');
        $services['sqp'] = $this->container->get('agora_game_sqp.sqpapi');
        $services['spldr'] = $this->container->get('agora_game.splendor');
        $services['aug'] = $this->container->get('agora_game.augustus');
        $services['mor'] = $this->container->get('agora_game.morpion');
        $services['azul'] = $this->container->get('agora_game.azul');
        $services['p4'] = $this->container->get('agora_game.puissance4');
        $services['rr'] = $this->container->get('agora_game.rr');
        $players = array();

        foreach ($games as $game) {
            $logger = $this->get('logger');
            $logger->info(json_encode($games));

            $gameCode = $game->getGameInfoId()->getGameCode();
            $service = $services[$gameCode];
            $gamePlayers = $service->getPlayers($game->getGameId());

            if (empty($gamePlayers) || $game->getState() == "finished") {
                $service->supressGame($game->getGameId());
            } else {
                $players[$gameCode][''.$game->getId()] = $gamePlayers;
            }
        }
        return $players;
    }
    
    public function joinGameAction() {
		$user = $this->getUser();
		if ($user != null && $user->hasRole('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('agora_platform_moderation'));
        }
        if (!is_object($user) || !$user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }
        $em = $this->getDoctrine()->getManager();
        //$games = $em->getRepository('AGORAGameGameBundle:Game')->findAll();
        $games = $em->getRepository('AGORAGameGameBundle:Game')->findBy(array('state' => "waiting"));
        $players = $this->getPlayersForAllGames($games, $em);
        
		return $this->render('AGORAPlatformBundle:Accueil:joinGame.html.twig' ,array(
            "games" => $games,
            "players" => $players
		));
	}

    public function moderationAction() {
		$user = $this->getUser();
        if ($user == null || (!($user->hasRole('ROLE_ADMIN')) && !($user->hasRole('ROLE_MODO')))) {
			return $this->render('AGORAPlatformBundle:Accueil:accueil.html.twig');
        }
        
        $em = $this->getDoctrine()->getManager();
        $games = $em->getRepository('AGORAGameGameBundle:Game')->findAll();
        $users = $em->getRepository('AGORAUserBundle:User')->findAll();
        $players = $this->getPlayersForAllGames($games, $em);

        return $this->render('AGORAPlatformBundle:Accueil:moderation.html.twig' ,array(
            "users" => $users,
            "games" => $games,
            "players" => $players
        ));
    }

    public function playerPartiesAction($userId) {
		$user = $this->getUser();
		if ($user != null && $user->hasRole('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('agora_platform_moderation'));
        }
        if (!is_object($user) || !$user instanceof UserInterface || $user->getId() != $userId) {
            return $this->redirect($this->generateUrl('agora_platform_homepage'));
        }

        $em = $this->getDoctrine()->getManager();
        //$games = $em->getRepository('AGORAGameGameBundle:Game')->findAll();
        $games = $em->getRepository('AGORAGameGameBundle:Game')->findBy(array('state' => "started"));
        $players = $this->getPlayersForAllGames($games, $em);;

        return $this->render('AGORAPlatformBundle:Accueil:playerParties.html.twig' ,array(
            "games" => $games,
            "players" => $players,
            "userId" => $userId,
        ));
    }
}
