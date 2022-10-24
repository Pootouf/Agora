<?php

namespace AGORA\Game\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game represente la table game dans la BDD et contient toutes les parties non
 *  terminees.
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="AGORA\Game\GameBundle\Repository\GameRepository")
 */
class Game
{
    /**
     * Identifie de maniere unique une entree dans la table.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Identifie un jeu dont la description se trouve dans la table game_info.
     *
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AGORA\PlatformBundle\Entity\GameInfo", cascade={"persist"})
     * @ORM\JoinColumn(name="game_info_id", referencedColumnName="id")
     */
    private $gameInfoId;

    /**
     * Identifie le numero de la partie du jeu, peut etre presente plusieurs
     *  fois car chaque jeu a sa partie numero x.
     *
     * @var int
     *
     * @ORM\Column(name="game_id", type="integer")
     */
    private $gameId;

    /**
     * Identifie le nombre de joueurs de la partie.
     *
     * @var int
     *
     * @ORM\Column(name="players_nb", type="integer")
     */
    private $playersNb;

    /**
     * Identifie l'etat de la partie :
     *    -en attente = waiting
     *    -en cours / demaree = started
     *    -terminee = finished
     *
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

    /**
     * Identifie le nom de la partie.
     *
     * @var string
     *
     * @ORM\Column(name="game_name", type="string", length=255)
     */
    private $gameName;

    /**
     * Identifie la date de creation de la partie.
     *
     * @var datetime
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=true)
     */
    private $creationDate;

    /**
     * Identifie l'identifiant du joueur ayant cree la partie.
     *
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AGORA\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="host_id", referencedColumnName="id")
     */
    private $hostId;
    
    /**
     * Identifie le joueur courrant lorsque les règles du jeu le permet.
     *
     * @var int
     *
     * @ORM\Column(name="current_player", type="integer", nullable=true)
     */
    private $currentPlayer;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set gameId.
     *
     * @param int $gameId
     *
     * @return Game
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * Get gameId.
     *
     * @return int
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * Set gameInfoId.
     *
     * @param int $gameInfoId
     *
     * @return Game
     */
    public function setGameInfoId($gameInfoId)
    {
        $this->gameInfoId = $gameInfoId;

        return $this;
    }

    /**
     * Get gameInfoId.
     *
     * @return int
     */
    public function getGameInfoId()
    {
        return $this->gameInfoId;
    }

    /**
     * Set playersNb.
     *
     * @param int $playersNb
     *
     * @return Game
     */
    public function setPlayersNb($playersNb)
    {
        $this->playersNb = $playersNb;

        return $this;
    }

    /**
     * Get playersNb.
     *
     * @return int
     */
    public function getPlayersNb()
    {
        return $this->playersNb;
    }

    /**
     * Set state.
     *
     * @param string $state
     *
     * @return Game
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set gameName.
     *
     * @param string $gameName
     *
     * @return Game
     */
    public function setGameName($gameName)
    {
        $this->gameName = $gameName;

        return $this;
    }

    /**
     * Get gameName.
     *
     * @return string
     */
    public function getGameName()
    {
        return $this->gameName;
    }

    /**
     * Set creationDate.
     *
     * @param \DateTime|null $creationDate
     *
     * @return Game
     */
    public function setCreationDate($creationDate = null)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate.
     *
     * @return \DateTime|null
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set hostId.
     *
     * @param int $hostId
     *
     * @return Game
     */
    public function setHostId($hostId)
    {
        $this->hostId = $hostId;

        return $this;
    }

    /**
     * Get hostId.
     *
     * @return int
     */
    public function getHostId()
    {
        return $this->hostId;
    }
    
    /**
     * Set currentPlayer.
     *
     * @param int $currentPlayer
     *
     * @return Game
     */
    public function setCurrentPlayer($currentPlayer)
    {
        $this->currentPlayer = $currentPlayer;

        return $this;
    }

    /**
     * Get currentPlayer.
     *
     * @return int
     */
    public function getCurrentPlayer()
    {
        return $this->currentPlayer;
    }
}
