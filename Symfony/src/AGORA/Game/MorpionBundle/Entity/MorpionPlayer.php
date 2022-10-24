<?php

namespace AGORA\Game\MorpionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MorpionPlayer
 *
 * @ORM\Table(name="morpion_player")
 * @ORM\Entity(repositoryClass="AGORA\Game\MorpionBundle\Repository\MorpionPlayerRepository")
 */
class MorpionPlayer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AGORA\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @var int
     * 
     * @ORM\ManyToOne(targetEntity="AGORA\Game\MorpionBundle\Entity\MorpionGame", cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", nullable=false)
     */
    private $gameId;

    /**
     * @var string     
     *
     * @ORM\Column(name="symbole", type="string", length=255)
     */
    private $symbole;

    /**
     * Identifie la date du dernier coup joue par le joueur.
     *
     * @var datetime
     *
     * @ORM\Column(name="last_move_played", type="datetime", nullable=true)
     */
    private $lastMovePlayed;

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
     * Set userId.
     *
     * @param int $userId
     *
     * @return MorpionPlayer
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set gameId.
     *
     * @param int $gameId
     *
     * @return MorpionPlayer
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
     * Get symbole.
     *
     * @return string
     */
    public function getSymbole()
    {
        return $this->symbole;
    }

    /**
     * Set symbole.
     *
     * @param string $symbole
     *
     * @return MorpionPlayer
     */
    public function setSymbole($symbole)
    {
        $this->symbole = $symbole;

        return $this;
    }

    /**
     * Get lastMovePlayed.
     *
     * @return \DateTime|null
     */
    public function getLastMovePlayed()
    {
        return $this->lastMovePlayed;
    }

    /**
     * Set lastMovePlayed.
     *
     * @param \DateTime|null $lastMovePlayed
     *
     * @return MorpionPlayer
     */
    public function setLastMovePlayed($lastMovePlayed = null)
    {
        $this->lastMovePlayed = $lastMovePlayed;

        return $this;
    }
}
