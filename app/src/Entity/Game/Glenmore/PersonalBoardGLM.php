<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\PersonalBoardGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonalBoardGLMRepository::class)]
class PersonalBoardGLM extends Component
{
    #[ORM\Column]
    private ?int $leaderCount = null;

    #[ORM\Column]
    private ?int $money = null;

    #[ORM\ManyToMany(targetEntity: CardGLM::class)]
    private Collection $cards;

    #[ORM\OneToMany(targetEntity: PlayerTileGLM::class, mappedBy: 'personalBoard')]
    private Collection $playerTiles;

    #[ORM\OneToOne(mappedBy: 'personalBoard', cascade: ['persist', 'remove'])]
    private ?PlayerGLM $playerGLM = null;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
        $this->playerTiles = new ArrayCollection();
    }

    public function getLeaderCount(): ?int
    {
        return $this->leaderCount;
    }

    public function setLeaderCount(int $leaderCount): static
    {
        $this->leaderCount = $leaderCount;

        return $this;
    }

    public function getMoney(): ?int
    {
        return $this->money;
    }

    public function setMoney(int $money): static
    {
        $this->money = $money;

        return $this;
    }

    /**
     * @return Collection<int, CardGLM>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(CardGLM $card): static
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
        }

        return $this;
    }

    public function removeCard(CardGLM $card): static
    {
        $this->cards->removeElement($card);

        return $this;
    }

    /**
     * @return Collection<int, PlayerTileGLM>
     */
    public function getPlayerTiles(): Collection
    {
        return $this->playerTiles;
    }

    public function addPlayerTile(PlayerTileGLM $playerTile): static
    {
        if (!$this->playerTiles->contains($playerTile)) {
            $this->playerTiles->add($playerTile);
            $playerTile->setPersonalBoard($this);
        }

        return $this;
    }

    public function removePlayerTile(PlayerTileGLM $playerTile): static
    {
        if ($this->playerTiles->removeElement($playerTile)) {
            // set the owning side to null (unless already changed)
            if ($playerTile->getPersonalBoard() === $this) {
                $playerTile->setPersonalBoard(null);
            }
        }

        return $this;
    }

    public function getPlayerGLM(): ?PlayerGLM
    {
        return $this->playerGLM;
    }

    public function setPlayerGLM(PlayerGLM $playerGLM): static
    {
        // set the owning side of the relation if necessary
        if ($playerGLM->getPersonalBoard() !== $this) {
            $playerGLM->setPersonalBoard($this);
        }

        $this->playerGLM = $playerGLM;

        return $this;
    }
}
