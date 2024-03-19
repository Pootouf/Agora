<?php

namespace App\Entity\Platform;

use App\Repository\Platform\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $descrRule = null;

    #[ORM\Column(length: 255)]
    private ?string $imgURL = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column]
    private ?bool $isActive = false;

    #[ORM\Column]
    private ?int $minPlayers = null;


    #[ORM\Column]
    private ?int $maxPlayers = null;

    #[ORM\OneToMany(targetEntity: Board::class, mappedBy: 'game')]
    private Collection $boards; // Valeur par défaut définie à false

    public function __construct()
    {
        $this->isActive = false; // Initialisation dans le constructeur
        $this->boards = new ArrayCollection();
    }

    /**
     * Gets the ID of the game.
     *
     * @return int|null The ID of the game.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the name of the game.
     *
     * @return string|null The name of the game.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Sets the name of the game.
     *
     * @param string $name The name of the game.
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the description of the game rules.
     *
     * @return string|null The description of the game rules.
     */
    public function getDescrRule(): ?string
    {
        return $this->descrRule;
    }

    /**
     * Sets the description of the game rules.
     *
     * @param string $descrRule The description of the game rules.
     * @return static
     */
    public function setDescrRule(string $descrRule): static
    {
        $this->descrRule = $descrRule;

        return $this;
    }

    /**
     * Gets the URL of the game image.
     *
     * @return string|null The URL of the game image.
     */
    public function getImgURL(): ?string
    {
        return $this->imgURL;
    }

    /**
     * Sets the URL of the game image.
     *
     * @param string $imgURL The URL of the game image.
     * @return static
     */
    public function setImgURL(string $imgURL): static
    {
        $this->imgURL = $imgURL;

        return $this;
    }

    /**
     * Gets the label of the game.
     *
     * @return string|null The label of the game.
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Sets the label of the game.
     *
     * @param string $label The label of the game.
     * @return static
     */
    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Checks if the game is active.
     *
     * @return bool|null True if the game is active, false otherwise.
     */
    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * Sets the activity status of the game.
     *
     * @param bool $isActive The activity status of the game.
     * @return static
     */
    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Gets the minimum number of players required for the game.
     *
     * @return int|null The minimum number of players required for the game.
     */
    public function getMinPlayers(): ?int
    {
        return $this->minPlayers;
    }

    /**
     * Sets the minimum number of players required for the game.
     *
     * @param int $minPlayers The minimum number of players required for the game.
     * @return static
     */
    public function setMinPlayers(int $minPlayers): static
    {
        $this->minPlayers = $minPlayers;

        return $this;
    }

    /**
     * Gets the maximum number of players allowed for the game.
     *
     * @return int|null The maximum number of players allowed for the game.
     */
    public function getMaxPlayers(): ?int
    {
        return $this->maxPlayers;
    }

    /**
     * Sets the maximum number of players allowed for the game.
     *
     * @param int $maxPlayers The maximum number of players allowed for the game.
     * @return static
     */
    public function setMaxPlayers(int $maxPlayers): static
    {
        $this->maxPlayers = $maxPlayers;

        return $this;
    }

    /**
     * Gets the collection of boards associated with the game.
     *
     * @return Collection<int, Board> The collection of boards associated with the game.
     */
    public function getBoards(): Collection
    {
        return $this->boards;
    }

    /**
     * Adds a board to the collection of boards associated with the game.
     *
     * @param Board $board The board to add.
     * @return static
     */
    public function addBoard(Board $board): static
    {
        if (!$this->boards->contains($board)) {
            $this->boards->add($board);
            $board->setGame($this);
        }

        return $this;
    }

    /**
     * Removes a board from the collection of boards associated with the game.
     *
     * @param Board $board The board to remove.
     * @return static
     */
    public function removeBoard(Board $board): static
    {
        if ($this->boards->removeElement($board)) {
            // set the owning side to null (unless already changed)
            if ($board->getGame() === $this) {
                $board->setGame(null);
            }
        }

        return $this;
    }
}
