<?php

namespace AGORA\Game\SplendorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SplendorGame
 *
 * @ORM\Table(name="splendor_game")
 * @ORM\Entity(repositoryClass="AGORA\Game\SplendorBundle\Repository\SplendorGameRepository")
 */
class SplendorGame
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
     * @var string
     *
     * [emerald_tokens, sapphire_tokens, ruby_tokens, diamond_tokens, onyx_tokens, JokerGold_tokens]
     *
     * @ORM\Column(name="tokens_list", type="string", length=50)
     */
    private $tokensList;

    /**
     * @var string
     *
     * @ORM\Column(name="cards_id", type="string", length=50)
     */
    private $cardsId;

    /**
     * @var string
     *
     * @ORM\Column(name="nobles_id", type="string", length=50)
     */
    private $noblesId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_turn_id", type="integer")
     */
    private $userTurnId;


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
     * Set tokensList.
     *
     * @param string $tokensList
     *
     * @return SplendorGame
     */
    public function setTokensList($tokensList)
    {
        $this->tokensList = $tokensList;

        return $this;
    }

    /**
     * Get tokensList.
     * [emerald_tokens, sapphire_tokens, ruby_tokens, diamond_tokens, onyx_tokens, JokerGold_tokens]
     *
     * @return array
     */
    public function getTokensList()
    {
        return array_map('intval', explode(',', $this->tokensList));
    }

    /**
     * Set cardsId.
     *
     * @param string $cardsId
     *
     * @return SplendorGame
     */
    public function setCardsId($cardsId)
    {
        $this->cardsId = $cardsId;

        return $this;
    }

    /**
     * Get cardsId.
     *
     * @return array
     */
    public function getCardsId()
    {
        return array_map('intval', explode(',', $this->cardsId));

    }

    /**
     * Set noblesId.
     *
     * @param string $noblesId
     *
     * @return SplendorGame
     */
    public function setNoblesId($noblesId)
    {
        $this->noblesId = $noblesId;

        return $this;
    }

    /**
     * Get noblesId.
     *
     * @return array
     */
    public function getNoblesId()
    {
        return array_map('intval', explode(',', $this->noblesId));
    }

    /**
     * Set userTurnId
     * @param int $userTurnId
     * @return SplendorGame
     */
    public function setUserTurnId($userTurnId)
    {
        $this->userTurnId = $userTurnId;
        return $this;
    }

    /**
     * Get userTurnId
     * @return int
     */
    public function getUserTurnId()
    {
        return $this->userTurnId;
    }
}
