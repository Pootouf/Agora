<?php

namespace AGORA\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Leaderboard représente le classement des joueurs pour les differents jeux.
 *
 * @ORM\Table(name="leaderboard")
 * @ORM\Entity(repositoryClass="AGORA\PlatformBundle\Repository\LeaderboardRepository")
 */
class Leaderboard
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
     * Identifie l'identifiant du joueur.
     *
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AGORA\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", nullable=false)
     */
    private $userId;

    /**
     * Identifie un jeu dont la description se trouve dans la table game_info.
     *
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AGORA\PlatformBundle\Entity\GameInfo", cascade={"persist"})
     * @ORM\JoinColumn(name="game_info_id", nullable=false)
     */
    private $gameInfoId;

    /**
     * Identifie le niveau d'un joueur.
     *
     * @var int
     *
     * @ORM\Column(name="elo", type="integer")
     */
    private $elo;

    /**
     * Identifie le nombre de victoire d'un joueur pour un jeu.
     *
     * @var int
     *
     * @ORM\Column(name="victory_nb", type="integer")
     */
    private $victoryNb;

    /**
     * Identifie le nombre d'egalie d'un joueur pour un jeu.
     *
     * @var int
     *
     * @ORM\Column(name="equality_nb", type="integer")
     */
    private $equalityNb;

    /**
     * Identifie le nombre de defaite d'un joueur pour un jeu.
     *
     * @var int
     *
     * @ORM\Column(name="lose_nb", type="integer")
     */
    private $loseNb;

     /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Leaderboard
     */
    public function setId($id)
    {
        return $this;
    }

    /**
     * Get loseNb
     *
     * @return int
     */
    public function getLoseNb()
    {
        return $this->loseNb;
    }

    /**
     * Set loseNb
     *
     * @param integer $loseNb
     *
     * @return Leaderboard
     */
    public function setLoseNb($loseNb)
    {
        $this->loseNb = $loseNb;

        return $this;
    }

    /**
     * Get victoryNb
     *
     * @return int
     */

     public function getVictoryNb()
    {
        return $this->victoryNb;
    }

    /**
     * Set victoryNb
     *
     * @param integer $victoryNb
     *
     * @return Leaderboard
     */
    public function setVictoryNb($victoryNb)
    {
        $this->victoryNb = $victoryNb;

        return $this;
    }

    /**
     * Get equalityNb
     *
     * @return int
     */

    public function getEqualityNb()
    {
        return $this->equalityNb;
    }

    /**
     * Set equalityNb
     *
     * @param integer $equalityNb
     *
     * @return Leaderboard
     */
    public function setEqualityNb($equalityNb)
    {
        $this->equalityNb = $equalityNb;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Leaderboard
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Set gameInfoId
     *
     * @param integer $gameInfoId
     *
     * @return Leaderboard
     */
    public function setGameInfoId($gameInfoId)
    {
        $this->gameInfoId = $gameInfoId;

        return $this;
    }

    /**
     * Get gameInfoId
     *
     * @return int
     */
    public function getGameInfoId()
    {
        return $this->gameInfoId;
    }

    /**
     * Set elo
     *
     * @param integer $elo
     *
     * @return Leaderboard
     */
    public function setElo($elo)
    {
        $this->elo = $elo;

        return $this;
    }

    /**
     * Get elo
     *
     * @return int
     */
    public function getElo()
    {
        return $this->elo;
    }
}
