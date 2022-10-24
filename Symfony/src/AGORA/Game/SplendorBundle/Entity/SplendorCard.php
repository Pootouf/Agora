<?php

namespace AGORA\Game\SplendorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SplendorCard
 *
 * @ORM\Table(name="splendor_card")
 * @ORM\Entity(repositoryClass="AGORA\Game\SplendorBundle\Repository\SplendorCardRepository")
 */
class SplendorCard
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
     * @ORM\Column(name="prestige", type="integer")
     */
    private $prestige;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * @var int
     *
     * @ORM\Column(name="emerald_tokens", type="integer")
     */
    private $emeraldTokens;

    /**
     * @var int
     *
     * @ORM\Column(name="sapphire_tokens", type="integer")
     */
    private $sapphireTokens;

    /**
     * @var int
     *
     * @ORM\Column(name="ruby_tokens", type="integer")
     */
    private $rubyTokens;

    /**
     * @var int
     *
     * @ORM\Column(name="diamond_tokens", type="integer")
     */
    private $diamondTokens;

    /**
     * @var int
     *
     * @ORM\Column(name="onyx_tokens", type="integer")
     */
    private $onyxTokens;

    /**
     * @var string
     *
     * @ORM\Column(name="bonus", type="string", length=50)
     */
    private $bonus;


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
     * Set prestige.
     *
     * @param int $prestige
     *
     * @return SplendorCard
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
     * Set level.
     *
     * @param int $level
     *
     * @return SplendorCard
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set emeraldTokens.
     *
     * @param int $emeraldTokens
     *
     * @return SplendorCard
     */
    public function setEmeraldTokens($emeraldTokens)
    {
        $this->emeraldTokens = $emeraldTokens;

        return $this;
    }

    /**
     * Get emeraldTokens.
     *
     * @return int
     */
    public function getEmeraldTokens()
    {
        return $this->emeraldTokens;
    }

    /**
     * Set sapphireTokens.
     *
     * @param int $sapphireTokens
     *
     * @return SplendorCard
     */
    public function setSapphireTokens($sapphireTokens)
    {
        $this->sapphireTokens = $sapphireTokens;

        return $this;
    }

    /**
     * Get sapphireTokens.
     *
     * @return int
     */
    public function getSapphireTokens()
    {
        return $this->sapphireTokens;
    }

    /**
     * Set rubyTokens.
     *
     * @param int $rubyTokens
     *
     * @return SplendorCard
     */
    public function setRubyTokens($rubyTokens)
    {
        $this->rubyTokens = $rubyTokens;

        return $this;
    }

    /**
     * Get rubyTokens.
     *
     * @return int
     */
    public function getRubyTokens()
    {
        return $this->rubyTokens;
    }

    /**
     * Set diamondTokens.
     *
     * @param int $diamondTokens
     *
     * @return SplendorCard
     */
    public function setDiamondTokens($diamondTokens)
    {
        $this->diamondTokens = $diamondTokens;

        return $this;
    }

    /**
     * Get diamondTokens.
     *
     * @return int
     */
    public function getDiamondTokens()
    {
        return $this->diamondTokens;
    }

    /**
     * Set onyxTokens.
     *
     * @param int $onyxTokens
     *
     * @return SplendorCard
     */
    public function setOnyxTokens($onyxTokens)
    {
        $this->onyxTokens = $onyxTokens;

        return $this;
    }

    /**
     * Get onyxTokens.
     *
     * @return int
     */
    public function getOnyxTokens()
    {
        return $this->onyxTokens;
    }

    /**
     * Set bonus.
     *
     * @param string $bonus
     *
     * @return SplendorCard
     */
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;

        return $this;
    }

    /**
     * Get bonus.
     *
     * @return string
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    public function getTokens($i) {
        switch ($i) {
            case 0: return $this->getEmeraldTokens();
            case 1: return $this->getSapphireTokens();
            case 2: return $this->getRubyTokens();
            case 3: return $this->getDiamondTokens();
            case 4: return $this->getOnyxTokens();
        }
    }
}
