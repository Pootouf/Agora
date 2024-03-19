<?php

namespace App\Entity\Game\Myrmes;

use App\Repository\Game\Myrmes\PlayerMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerMYRRepository::class)]
class PlayerMYR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column]
    private ?int $goalLevel = null;

    #[ORM\OneToMany(targetEntity: NurseMYR::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $nurseMYRs;

    #[ORM\OneToMany(targetEntity: AnthillWorkerMYR::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $anthillWorkerMYRs;

    #[ORM\OneToOne(mappedBy: 'firstPlayer', cascade: ['persist', 'remove'])]
    private ?GameMYR $gameMYR = null;

    #[ORM\OneToMany(targetEntity: GardenWorkerMYR::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $gardenWorkerMYRs;

    #[ORM\OneToMany(targetEntity: GardenTileMYR::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $gardenTileMYRs;

    #[ORM\ManyToMany(targetEntity: GameGoalMYR::class, mappedBy: 'precedentsPlayers')]
    private Collection $gameGoalMYRs;

    #[ORM\OneToMany(targetEntity: AnthillHoleMYR::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $anthillHoleMYRs;

    #[ORM\OneToOne(mappedBy: 'player', cascade: ['persist', 'remove'])]
    private ?PersonalBoardMYR $personalBoardMYR = null;

    public function __construct()
    {
        $this->nurseMYRs = new ArrayCollection();
        $this->anthillWorkerMYRs = new ArrayCollection();
        $this->gardenWorkerMYRs = new ArrayCollection();
        $this->gardenTileMYRs = new ArrayCollection();
        $this->gameGoalMYRs = new ArrayCollection();
        $this->anthillHoleMYRs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
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
     * @return Collection<int, NurseMYR>
     */
    public function getNurseMYRs(): Collection
    {
        return $this->nurseMYRs;
    }

    public function addNurseMYR(NurseMYR $nurseMYR): static
    {
        if (!$this->nurseMYRs->contains($nurseMYR)) {
            $this->nurseMYRs->add($nurseMYR);
            $nurseMYR->setPlayer($this);
        }

        return $this;
    }

    public function removeNurseMYR(NurseMYR $nurseMYR): static
    {
        if ($this->nurseMYRs->removeElement($nurseMYR)) {
            // set the owning side to null (unless already changed)
            if ($nurseMYR->getPlayer() === $this) {
                $nurseMYR->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AnthillWorkerMYR>
     */
    public function getAnthillWorkerMYRs(): Collection
    {
        return $this->anthillWorkerMYRs;
    }

    public function addAnthillWorkerMYR(AnthillWorkerMYR $anthillWorkerMYR): static
    {
        if (!$this->anthillWorkerMYRs->contains($anthillWorkerMYR)) {
            $this->anthillWorkerMYRs->add($anthillWorkerMYR);
            $anthillWorkerMYR->setPlayer($this);
        }

        return $this;
    }

    public function removeAnthillWorkerMYR(AnthillWorkerMYR $anthillWorkerMYR): static
    {
        if ($this->anthillWorkerMYRs->removeElement($anthillWorkerMYR)) {
            // set the owning side to null (unless already changed)
            if ($anthillWorkerMYR->getPlayer() === $this) {
                $anthillWorkerMYR->setPlayer(null);
            }
        }

        return $this;
    }

    public function getGameMYR(): ?GameMYR
    {
        return $this->gameMYR;
    }

    public function setGameMYR(GameMYR $gameMYR): static
    {
        // set the owning side of the relation if necessary
        if ($gameMYR->getFirstPlayer() !== $this) {
            $gameMYR->setFirstPlayer($this);
        }

        $this->gameMYR = $gameMYR;

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
     * @return Collection<int, GardenTileMYR>
     */
    public function getGardenTileMYRs(): Collection
    {
        return $this->gardenTileMYRs;
    }

    public function addGardenTileMYR(GardenTileMYR $gardenTileMYR): static
    {
        if (!$this->gardenTileMYRs->contains($gardenTileMYR)) {
            $this->gardenTileMYRs->add($gardenTileMYR);
            $gardenTileMYR->setPlayer($this);
        }

        return $this;
    }

    public function removeGardenTileMYR(GardenTileMYR $gardenTileMYR): static
    {
        if ($this->gardenTileMYRs->removeElement($gardenTileMYR)) {
            // set the owning side to null (unless already changed)
            if ($gardenTileMYR->getPlayer() === $this) {
                $gardenTileMYR->setPlayer(null);
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
}
