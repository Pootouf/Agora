<?php

namespace AGORA\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameInfo represente la table game_info dans la BDD et contient toutes les
 *  informations des jeux de la plateforme.
 *
 * @ORM\Table(name="game_info")
 * @ORM\Entity(repositoryClass="AGORA\PlatformBundle\Repository\GameInfoRepository")
 */
class GameInfo
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
     * Identifie le nombre minimum de joueurs autorises pour le jeu.
     *
     * @var int|null
     *
     * @ORM\Column(name="min_players", type="integer")
     */
    private $minPlayers;

    /**
     * Identifie le nombre maximum de joueurs autorises pour le jeu.
     *
     * @var int|null
     *
     * @ORM\Column(name="max_players", type="integer")
     */
    private $maxPlayers;

    /**
     * Identifie la description du jeu.
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * Identifie l'image du jeu.
     *
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * Identifie les regles du jeu.
     *
     * @var string|null
     *
     * @ORM\Column(name="rules", type="string", length=255, nullable=true, unique=false)
     */
    private $rules;

    /**
     * Identifie le nom du jeu.
     *
     * @var string
     *
     * @ORM\Column(name="game_name", type="string", length=255, unique=true)
     */
    private $gameName;

    /**
     * Identifie le code du jeu, soit quelques lettres pour identifier le jeu
     *  avec un acronyme, un code.
     *
     * @var string|null
     *
     * @ORM\Column(name="game_code", type="string", length=255, unique=true)
     */
    private $gameCode;


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
     * Set minPlayers.
     *
     * @param integer|null $minPlayers
     *
     * @return GameInfo
     */
    public function setMinPlayers($minPlayers = null)
    {
        $this->minPlayers = $minPlayers;

        return $this;
    }

    /**
     * Get minPlayers.
     *
     * @return int
     */
    public function getMinPlayers()
    {
        return $this->minPlayers;
    }

    /**
     * Set maxPlayers.
     *
     * @param integer|null $maxPlayers
     *
     * @return GameInfo
     */
    public function setMaxPlayers($maxPlayers = null)
    {
        $this->maxPlayers = $maxPlayers;

        return $this;
    }

    /**
     * Get maxPlayers.
     *
     * @return int
     */
    public function getMaxPlayers()
    {
        return $this->maxPlayers;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return GameInfo
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set image.
     *
     * @param string|null $image
     *
     * @return GameInfo
     */
    public function setImage($image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set rules.
     *
     * @param string|null $rules
     *
     * @return GameInfo
     */
    public function setRules($rules = null)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Get rules.
     *
     * @return string|null
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Set gameName.
     *
     * @param string $gameName
     *
     * @return GameInfo
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
     * Set gameCode.
     *
     * @param string $gameCode
     *
     * @return GameInfo
     */
    public function setGameCode($gameCode)
    {
        $this->gameCode = $gameCode;

        return $this;
    }

    /**
     * Get gameCode.
     *
     * @return string
     */
    public function getGameCode()
    {
        return $this->gameCode;
    }
}
