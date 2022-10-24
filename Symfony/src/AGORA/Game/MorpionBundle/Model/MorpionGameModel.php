<?php

namespace AGORA\Game\MorpionBundle\Model;

use AGORA\Game\GameBundle\Entity\Game;
use AGORA\Game\MorpionBundle\Entity\MorpionGame;
use Doctrine\ORM\EntityManager;

/**
 * Cette classe permet d'accéder au différents modèles liés à une partie de Morpion.
 * On retrouvera donc des méthodes pour créer, rejoindre une partie, etc.
 */

class MorpionGameModel
{

    private $BDDManager;

    /**
     * Contructeur de notre classe de gestion de partie de Morpion.
     * Prend un paramètre un EntityManager lié au composant ORM.
     */
    public function __construct(EntityManager $manager)
    {
        $this->BDDManager = $manager;
    }

    //Créé une nouvelle partie de Morpion.
    public function createMorpionGame($name, $host)
    {
        //On créé un nouveau jeu de Morpion dans la base de données.
        $morGame = new MorpionGame();
        $this->BDDManager->persist($morGame);
        $this->BDDManager->flush();

        //On créé un nouvel objet GAME, qui sera l'entré de la partie dans la table "Game".
        $game = new Game();

        $game->setGameId($morGame->getId());
        $game->setHostId($host);
        $game->setGameName($name);
        $game->setPlayersNb(2);
        $game->setState("waiting");
        $game->setCreationDate(new \DateTime("now"));

        //On récupére les informations du jeu dans la base de données et on l'enregistre dans notre objet Game.
        $gameInfoManager = $this->BDDManager->getRepository('AGORAPlatformBundle:GameInfo');
        $gameInfo = $gameInfoManager->findOneBy(array('gameCode' => "mor"));
        $game->setGameInfoId($gameInfo);

        //On enregistre la partie dans la table "Game" commune à tous les jeux, grâce à ORM - EntityManager.
        $this->BDDManager->persist($game);
        $this->BDDManager->flush();

        //On retourne l'identifiant de la partie.
        return $morGame->getId();
    }


}
