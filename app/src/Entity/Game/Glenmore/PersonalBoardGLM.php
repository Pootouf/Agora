<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\PersonalBoardGLMRepository;
use App\Entity\Game\Glenmore\PlayerCardGLM;
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

    #[ORM\OneToMany(targetEntity: PlayerTileGLM::class, mappedBy: 'personalBoard')]
    private Collection $playerTiles;

    #[ORM\OneToOne(mappedBy: 'personalBoard', cascade: ['persist', 'remove'])]
    private ?PlayerGLM $playerGLM = null;

    #[ORM\OneToMany(targetEntity: PlayerCardGLM::class, mappedBy: 'personalBoard', orphanRemoval: true)]
    private Collection $playerCardGLM;

    #[ORM\OneToMany(targetEntity: SelectedResourceGLM::class, mappedBy: 'personalBoardGLM', orphanRemoval: true)]
    private Collection $selectedResources;

    #[ORM\OneToMany(targetEntity: CreatedResourceGLM::class, mappedBy: 'personalBoardGLM', orphanRemoval: true)]
    private Collection $createdResources;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?BoardTileGLM $buyingTile = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?PlayerTileGLM $activatedTile = null;

    public function __construct()
    {
        $this->playerTiles = new ArrayCollection();
        $this->playerCardGLM = new ArrayCollection();
        $this->selectedResources = new ArrayCollection();
        $this->createdResources = new ArrayCollection();
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

    /**
     * @return Collection<int, PlayerCardGLM>
     */
    public function getPlayerCardGLM(): Collection
    {
        return $this->playerCardGLM;
    }

    public function addPlayerCardGLM(PlayerCardGLM $playerCardGLM): static
    {
        if (!$this->playerCardGLM->contains($playerCardGLM)) {
            $this->playerCardGLM->add($playerCardGLM);
            $playerCardGLM->setPersonalBoard($this);
        }

        return $this;
    }

    public function removePlayerCardGLM(PlayerCardGLM $playerCardGLM): static
    {
        if ($this->playerCardGLM->removeElement($playerCardGLM)) {
            // set the owning side to null (unless already changed)
            if ($playerCardGLM->getPersonalBoard() === $this) {
                $playerCardGLM->setPersonalBoard(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SelectedResourceGLM>
     */
    public function getSelectedResources(): Collection
    {
        return $this->selectedResources;
    }

    public function addSelectedResource(SelectedResourceGLM $selectedResource): static
    {
        if (!$this->selectedResources->contains($selectedResource)) {
            $this->selectedResources->add($selectedResource);
            $selectedResource->setPersonalBoardGLM($this);
        }

        return $this;
    }

    public function removeSelectedResource(SelectedResourceGLM $selectedResource): static
    {
        if ($this->selectedResources->removeElement($selectedResource)) {
            // set the owning side to null (unless already changed)
            if ($selectedResource->getPersonalBoardGLM() === $this) {
                $selectedResource->setPersonalBoardGLM(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CreatedResourceGLM>
     */
    public function getCreatedResources(): Collection
    {
        return $this->createdResources;
    }

    public function addCreatedResource(CreatedResourceGLM $createdResource): static
    {
        if (!$this->createdResources->contains($createdResource)) {
            $this->createdResources->add($createdResource);
            $createdResource->setPersonalBoardGLM($this);
        }

        return $this;
    }

    public function removeCreatedResource(CreatedResourceGLM $createdResource): static
    {
        if ($this->createdResources->removeElement($createdResource)) {
            // set the owning side to null (unless already changed)
            if ($createdResource->getPersonalBoardGLM() === $this) {
                $createdResource->setPersonalBoardGLM(null);
            }
        }

        return $this;
    }

    public function getBuyingTile(): ?BoardTileGLM
    {
        return $this->buyingTile;
    }

    public function setBuyingTile(?BoardTileGLM $buyingTile): static
    {
        $this->buyingTile = $buyingTile;

        return $this;
    }

    public function getActivatedTile(): ?PlayerTileGLM
    {
        return $this->activatedTile;
    }

    public function setActivatedTile(?PlayerTileGLM $activatedTile): static
    {
        $this->activatedTile = $activatedTile;

        return $this;
    }
}
