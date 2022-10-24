<?php

namespace AGORA\Game\Puissance4Bundle\Model;

use AGORA\Game\GameBundle\Entity\Game;
use AGORA\Game\Puissance4Bundle\Entity\Puissance4Game;
use Doctrine\ORM\EntityManager;

/**
 * Cette classe permet d'accéder au différents modèles liés à une partie de Puissance4.
 * On retrouvera donc des méthodes pour créer, rejoindre une partie, etc.
 */

class Puissance4GameModel
{

    private $BDDManager;

    /**
     * Contructeur de notre classe de gestion de partie de Puissance4.
     * Prend un paramètre un EntityManager lié au composant ORM.
     */
    public function __construct(EntityManager $manager)
    {
        $this->BDDManager = $manager;
    }

    //Créé une nouvelle partie de Puissance4.
    public function createPuissance4Game($name, $host)
    {
        //On créé un nouveau jeu de Puissance4 dans la base de données.
        $puiGame = new Puissance4Game();
        $this->BDDManager->persist($puiGame);
        $this->BDDManager->flush();

        //On créé un nouvel objet GAME, qui sera l'entré de la partie dans la table "Game".
        $game = new Game();

        $game->setGameId($puiGame->getId());
        $game->setHostId($host);
        $game->setGameName($name);
        $game->setPlayersNb(2);
        $game->setState("waiting");
        $game->setCreationDate(new \DateTime("now"));

        //On récupére les informations du jeu dans la base de données et on l'enregistre dans notre objet Game.
        $gameInfoManager = $this->BDDManager->getRepository('AGORAPlatformBundle:GameInfo');
        $gameInfo = $gameInfoManager->findOneBy(array('gameCode' => "p4"));
        $game->setGameInfoId($gameInfo);

        //On enregistre la partie dans la table "Game" commune à tous les jeux, grâce à ORM - EntityManager.
        $this->BDDManager->persist($game);
        $this->BDDManager->flush();

        //On retourne l'identifiant de la partie.
        return $puiGame->getId();
    }


}
