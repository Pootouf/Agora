<?php

namespace AGORA\Game\Puissance4Bundle\Model;

use AGORA\Game\GameBundle\Entity\Game;
use AGORA\Game\Puissance4Bundle\Entity\Puissance4Game;
use AGORA\Game\Puissance4Bundle\Entity\Puissance4Player;
use Doctrine\ORM\EntityManager;

/**
 * Cette classe permet d'accéder au différents modèles lié à une partie de Puissance4.
 * On retrouvera donc des méthodes pour créer, rejoindre une partie, etc.
 */
class Puissance4PlayerModel  {

    private $BDDManager;

    /**
     * Contructeur de notre classe de gestion des joueurs de Puissance4.
     * Prend un paramètre un EntityManager lié au composant ORM.
     */
    public function __construct(EntityManager $manager) {
        $this->BDDManager = $manager;
    }

    /**
     * Créé un nouveau joueur dans la partie de Puissance4.
     */
    public function createPuissance4Player($user, $gameId){
        //On récupère la partie de Puissance4 à partir de son Identifiant. On vérifie que la partie existe.
        $puiGame = $this->BDDManager->getRepository('AGORAGamePuissance4Bundle:Puissance4Game')->find($gameId);
        if ($puiGame == null) {
            throw new \Exception();
        }

        //On récupère les informations de la partie de Puissance4 stockées dans la table : Game
        $game = $this->BDDManager->getRepository('AGORAGameGameBundle:Game')->findOneBy(array('gameId' => $gameId));

        //On récupère les lignes où sont stockés les joueurs de la partie de Puissance4s (table : Puissance4Player)
        $players = $this->BDDManager->getRepository('AGORAGamePuissance4Bundle:Puissance4Player')->findBy(array('gameId' => $gameId));

        //On vérifie que la partie n'est pas déja pleine. Si c'est le cas on retourne : -1
        $nbPlayer = count($players);
        if ($nbPlayer >= $game->getPlayersNb()) {
            return -1;
        }

        if($nbPlayer == 0){
            $symbole = "red";
        }
        else {
            $symbole = "yellow";
        }

        //On créé un nouveau joueur lié à la partie de Puissance4.
        $puiPlayer = new Puissance4Player();
        $puiPlayer->setGameId($puiGame);
        $puiPlayer->setUserId($user);
        $puiPlayer->setSymbole($symbole);

        //On enregistre le nouveau joueur dans la base de données.
        $this->BDDManager->persist($puiPlayer);
        $this->BDDManager->flush();

        //On retourne l'Identifiant de joueur de partie.
        return $puiPlayer->getId();
    }


}
