<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Player;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerMYRRepository::class)]
class PlayerMYR extends Player
{
    #[ORM\Column]
    private ?int $goalLevel = null;

    #[ORM\OneToMany(targetEntity: GardenWorkerMYR::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $gardenWorkerMYRs;

    #[ORM\ManyToMany(targetEntity: GameGoalMYR::class, mappedBy: 'precedentsPlayers')]
    private Collection $gameGoalMYRs;

    #[ORM\OneToMany(targetEntity: AnthillHoleMYR::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $anthillHoleMYRs;

    #[ORM\OneToOne(mappedBy: 'player', cascade: ['persist', 'remove'])]
    private ?PersonalBoardMYR $personalBoardMYR = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameMYR $gameMYR = null;

    #[ORM\OneToMany(targetEntity: PheromonMYR::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $pheromonMYRs;

    #[ORM\OneToMany(targetEntity: PreyMYR::class, mappedBy: 'player')]
    private Collection $preyMYRs;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\Column]
    private ?int $phase = null;

    #[ORM\Column]
    private ?int $remainingHarvestingBonus = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $workshopActions = [];

    public function __construct(string $name, GameMYR $game)
    {
        $this->gardenWorkerMYRs = new ArrayCollection();
        $this->gameGoalMYRs = new ArrayCollection();
        $this->anthillHoleMYRs = new ArrayCollection();
        $this->username = $name;
        $this->gameMYR = $game;
        $this->pheromonMYRs = new ArrayCollection();
        $this->preyMYRs = new ArrayCollection();
    }

    public function getGoalLevel(): ?int
    {
        return $this->goalLevel;
    }

    public function setGoalLevel(int $goalLevel): static
    {
        $this->goalLevel = $goalLevel;

        return $this;
    }


    /**
     * @return Collection<int, GardenWorkerMYR>
     */
    public function getGardenWorkerMYRs(): Collection
    {
        return $this->gardenWorkerMYRs;
    }

    public function addGardenWorkerMYR(GardenWorkerMYR $gardenWorkerMYR): static
    {
        if (!$this->gardenWorkerMYRs->contains($gardenWorkerMYR)) {
            $this->gardenWorkerMYRs->add($gardenWorkerMYR);
            $gardenWorkerMYR->setPlayer($this);
        }

        return $this;
    }

    public function removeGardenWorkerMYR(GardenWorkerMYR $gardenWorkerMYR): static
    {
        if ($this->gardenWorkerMYRs->removeElement($gardenWorkerMYR)) {
            // set the owning side to null (unless already changed)
            if ($gardenWorkerMYR->getPlayer() === $this) {
                $gardenWorkerMYR->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GameGoalMYR>
     */
    public function getGameGoalMYRs(): Collection
    {
        return $this->gameGoalMYRs;
    }

    public function addGameGoalMYR(GameGoalMYR $gameGoalMYR): static
    {
        if (!$this->gameGoalMYRs->contains($gameGoalMYR)) {
            $this->gameGoalMYRs->add($gameGoalMYR);
            $gameGoalMYR->addPrecedentsPlayer($this);
        }

        return $this;
    }

    public function removeGameGoalMYR(GameGoalMYR $gameGoalMYR): static
    {
        if ($this->gameGoalMYRs->removeElement($gameGoalMYR)) {
            $gameGoalMYR->removePrecedentsPlayer($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, AnthillHoleMYR>
     */
    public function getAnthillHoleMYRs(): Collection
    {
        return $this->anthillHoleMYRs;
    }

    public function addAnthillHoleMYR(AnthillHoleMYR $anthillHoleMYR): static
    {
        if (!$this->anthillHoleMYRs->contains($anthillHoleMYR)) {
            $this->anthillHoleMYRs->add($anthillHoleMYR);
            $anthillHoleMYR->setPlayer($this);
        }

        return $this;
    }

    public function removeAnthillHoleMYR(AnthillHoleMYR $anthillHoleMYR): static
    {
        if ($this->anthillHoleMYRs->removeElement($anthillHoleMYR)) {
            // set the owning side to null (unless already changed)
            if ($anthillHoleMYR->getPlayer() === $this) {
                $anthillHoleMYR->setPlayer(null);
            }
        }

        return $this;
    }

    public function getPersonalBoardMYR(): ?PersonalBoardMYR
    {
        return $this->personalBoardMYR;
    }

    public function setPersonalBoardMYR(PersonalBoardMYR $personalBoardMYR): static
    {
        // set the owning side of the relation if necessary
        if ($personalBoardMYR->getPlayer() !== $this) {
            $personalBoardMYR->setPlayer($this);
        }

        $this->personalBoardMYR = $personalBoardMYR;

        return $this;
    }

    public function getGameMyr(): ?GameMYR
    {
        return $this->gameMYR;
    }

    public function setGameMyr(?GameMYR $gameMYR): static
    {
        $this->gameMYR = $gameMYR;

        return $this;
    }

    /**
     * @return Collection<int, PheromonMYR>
     */
    public function getPheromonMYRs(): Collection
    {
        return $this->pheromonMYRs;
    }

    public function addPheromonMYR(PheromonMYR $pheromonMYR): static
    {
        if (!$this->pheromonMYRs->contains($pheromonMYR)) {
            $this->pheromonMYRs->add($pheromonMYR);
            $pheromonMYR->setPlayer($this);
        }

        return $this;
    }

    public function removePheromonMYR(PheromonMYR $pheromonMYR): static
    {
        if ($this->pheromonMYRs->removeElement($pheromonMYR)) {
            // set the owning side to null (unless already changed)
            if ($pheromonMYR->getPlayer() === $this) {
                $pheromonMYR->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PreyMYR>
     */
    public function getPreyMYRs(): Collection
    {
        return $this->preyMYRs;
    }

    public function addPreyMYR(PreyMYR $preyMYR): static
    {
        if (!$this->preyMYRs->contains($preyMYR)) {
            $this->preyMYRs->add($preyMYR);
            $preyMYR->setPlayer($this);
        }

        return $this;
    }

    public function removePreyMYR(PreyMYR $preyMYR): static
    {
        if ($this->preyMYRs->removeElement($preyMYR)) {
            // set the owning side to null (unless already changed)
            if ($preyMYR->getPlayer() === $this) {
                $preyMYR->setPlayer(null);
            }
        }

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getPhase(): ?int
    {
        return $this->phase;
    }

    public function setPhase(int $phase): static
    {
        $this->phase = $phase;

        return $this;
    }

    public function getRemainingHarvestingBonus(): ?int
    {
        return $this->remainingHarvestingBonus;
    }

    public function setRemainingHarvestingBonus(int $remainingHarvestingBonus): static
    {
        $this->remainingHarvestingBonus = $remainingHarvestingBonus;

        return $this;
    }

    public function &getWorkshopActions(): array
    {
        return $this->workshopActions;
    }

    public function setWorkshopActions(array $workshopActions): static
    {
        $this->workshopActions = $workshopActions;

        return $this;
    }
}
