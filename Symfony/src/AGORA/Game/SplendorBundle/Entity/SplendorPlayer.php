<?php

namespace AGORA\Game\SplendorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SplendorPlayer
 *
 * @ORM\Table(name="splendor_player")
 * @ORM\Entity(repositoryClass="AGORA\Game\SplendorBundle\Repository\SplendorPlayerRepository")
 */
class SplendorPlayer
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
     * @ORM\ManyToOne(targetEntity="AGORA\Game\SplendorBundle\Entity\SplendorGame", cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $gameId;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="AGORA\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="prestige", type="integer")
     */
    private $prestige;

    /**
     * @var string
     *
     * [emeraldTokens, sapphireTokens, rubyTokens, diamondTokens, onyxTokens, JokerGoldTokens]
     *
     * @ORM\Column(name="tokens_list", type="string", length=50)
     */
    private $tokensList;

    /**
     * @var string
     *
     * @ORM\Column(name="reserved_cards", type="string", length=50)
     */
    private $reservedCards;

    /**
     * @var string
     *
     * @ORM\Column(name="buyed_cards", type="string", length=500)
     */
    private $buyedCards;

    /**
     * @var string
     *
     * @ORM\Column(name="hidden_cards", type="string", length=50)
     */
    private $hiddenCards;

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
     * Set gameId.
     *
     * @param int $gameId
     *
     * @return SplendorPlayer
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
     * Set userId.
     *
     * @param int $userId
     *
     * @return SplendorPlayer
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
     * Set prestige.
     *
     * @param int $prestige
     *
     * @return SplendorPlayer
     */
    public function setPrestige($prestige)
    {
        $this->prestige = $prestige;

        return $this;
    }

    /**
     * Get prestige.
     *
     * @return int
     */
    public function getPrestige()
    {
        return $this->prestige;
    }

    /**
     * Set tokensList.
     *
     * @param string $tokensList
     *
     * @return SplendorPlayer
     */
    public function setTokensList($tokensList)
    {
        $this->tokensList = $tokensList;

        return $this;
    }

    /**
     * Get tokensList.
     * [emeraldTokens, sapphireTokens, rubyTokens, diamondTokens, onyxTokens, JokerGoldTokens]
     *
     * @return array
     */
    public function getTokensList()
    {
        return array_map('intval', explode(',', $this->tokensList));

    }

    /**
     * Set reservedCards.
     *
     * @param string $reservedCards
     *
     * @return SplendorPlayer
     */
    public function setReservedCards($reservedCards)
    {
        $this->reservedCards = $reservedCards;

        return $this;
    }

    /**
     * Get reservedCards.
     *
     * @return array
     */
    public function getReservedCards()
    {
//        if ($this->reservedCards == "") {
//            return [];
//        }
        return array_map('intval', explode(',', $this->reservedCards));

    }

    /**
     * Set buyedCards.
     *
     * @param string $buyedCards
     *
     * @return SplendorPlayer
     */
    public function setBuyedCards($buyedCards)
    {
        $this->buyedCards = $buyedCards;

        return $this;
    }

    /**
     * Get buyedCards.
     *
     * @return array
     */
    public function getBuyedCards()
    {
//        if ($this->getBuyedCards() == "") {
//            return [];
//        }
        return array_map('intval', explode(',', $this->buyedCards));
    }

    /**
     * @param string $hiddenCards
     */
    public function setHiddenCards($hiddenCards)
    {
        $this->hiddenCards = $hiddenCards;
    }

    /**
     * @return string
     */
    public function getHiddenCards()
    {
        return array_map('intval', explode(',', $this->hiddenCards));
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
     * @return SplendorPlayer
     */
    public function setLastMovePlayed($lastMovePlayed = null)
    {
        $this->lastMovePlayed = $lastMovePlayed;

        return $this;
    }
}
