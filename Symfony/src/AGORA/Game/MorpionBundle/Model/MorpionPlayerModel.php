<?php

namespace AGORA\Game\MorpionBundle\Model;

use AGORA\Game\GameBundle\Entity\Game;
use AGORA\Game\MorpionBundle\Entity\MorpionGame;
use AGORA\Game\MorpionBundle\Entity\MorpionPlayer;
use Doctrine\ORM\EntityManager;

/**
 * Cette classe permet d'accéder au différents modèles lié à une partie de Morpion.
 * On retrouvera donc des méthodes pour créer, rejoindre une partie, etc.
 */
class MorpionPlayerModel  {

    private $BDDManager;

    /**
     * Contructeur de notre classe de gestion des joueurs de Morpion.
     * Prend un paramètre un EntityManager lié au composant ORM.
     */
    public function __construct(EntityManager $manager) {
        $this->BDDManager = $manager;
    }

    /**
     * Créé un nouveau joueur dans la partie de Morpion.
     */
    public function createMorpionPlayer($user, $gameId){
        //On récupère la partie de Morpion à partir de son Identifiant. On vérifie que la partie existe.
        $morGame = $this->BDDManager->getRepository('AGORAGameMorpionBundle:MorpionGame')->find($gameId);
        if ($morGame == null) {
            throw new \Exception();
        }

        //On récupère les informations de la partie de Morpion stockées dans la table : Game
        $game = $this->BDDManager->getRepository('AGORAGameGameBundle:Game')->findOneBy(array('gameId' => $gameId));

        //On récupère les lignes où sont stockés les joueurs de la partie de Morpions (table : MorpionPlayer)
        $players = $this->BDDManager->getRepository('AGORAGameMorpionBundle:MorpionPlayer')->findBy(array('gameId' => $gameId));

        //On vérifie que la partie n'est pas déja pleine. Si c'est le cas on retourne : -1
        $nbPlayer = count($players);
        if ($nbPlayer >= $game->getPlayersNb()) {
            return -1;
        }

        if($nbPlayer == 0){
            $symbole = "o";
        }
        else {
            $symbole = "x";
        }

        //On créé un nouveau joueur lié à la partie de Morpion.
        $morPlayer = new MorpionPlayer();
        $morPlayer->setGameId($morGame);
        $morPlayer->setUserId($user);
        $morPlayer->setSymbole($symbole);

        //On enregistre le nouveau joueur dans la base de données.
        $this->BDDManager->persist($morPlayer);
        $this->BDDManager->flush();

        //On retourne l'Identifiant de joueur de partie.
        return $morPlayer->getId();
    }


}
