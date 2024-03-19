<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\PersonalBoardMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonalBoardMYRRepository::class)]
class PersonalBoardMYR extends Component
{

    #[ORM\OneToOne(inversedBy: 'personalBoardMYR', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerMYR $player = null;

    #[ORM\Column]
    private ?int $anthillLevel = null;

    #[ORM\Column]
    private ?int $larvaCount = null;

    #[ORM\OneToMany(targetEntity: NurseMYR::class, mappedBy: 'personalBoardMYR', orphanRemoval: true)]
    private Collection $nurses;

    #[ORM\Column]
    private ?int $warriorsCount = null;

    #[ORM\OneToMany(targetEntity: AnthillWorkerMYR::class, mappedBy: 'personalBoardMYR', orphanRemoval: true)]
    private Collection $anthillWorkers;

    #[ORM\Column]
    private ?int $bonus = null;

    #[ORM\Column]
    private ?int $huntedPreyCount = null;

    #[ORM\OneToMany(targetEntity: PlayerResourceMYR::class, mappedBy: 'personalBoard', orphanRemoval: true)]
    private Collection $playerResourceMYRs;

    public function __construct()
    {
        $this->nurses = new ArrayCollection();
        $this->anthillWorkers = new ArrayCollection();
        $this->playerResourceMYRs = new ArrayCollection();
    }

    public function getPlayer(): ?PlayerMYR
    {
        return $this->player;
    }

    public function setPlayer(PlayerMYR $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getAnthillLevel(): ?int
    {
        return $this->anthillLevel;
    }

    public function setAnthillLevel(int $anthillLevel): static
    {
        $this->anthillLevel = $anthillLevel;

        return $this;
    }

    public function getLarvaCount(): ?int
    {
        return $this->larvaCount;
    }

    public function setLarvaCount(int $larvaCount): static
    {
        $this->larvaCount = $larvaCount;

        return $this;
    }

    /**
     * @return Collection<int, NurseMYR>
     */
    public function getNurses(): Collection
    {
        return $this->nurses;
    }

    public function addNurse(NurseMYR $nurse): static
    {
        if (!$this->nurses->contains($nurse)) {
            $this->nurses->add($nurse);
            $nurse->setPersonalBoardMYR($this);
        }

        return $this;
    }

    public function removeNurse(NurseMYR $nurse): static
    {
        if ($this->nurses->removeElement($nurse)) {
            // set the owning side to null (unless already changed)
            if ($nurse->getPersonalBoardMYR() === $this) {
                $nurse->setPersonalBoardMYR(null);
            }
        }

        return $this;
    }

    public function getWarriorsCount(): ?int
    {
        return $this->warriorsCount;
    }

    public function setWarriorsCount(int $warriorsCount): static
    {
        $this->warriorsCount = $warriorsCount;

        return $this;
    }

    /**
     * @return Collection<int, AnthillWorkerMYR>
     */
    public function getAnthillWorkers(): Collection
    {
        return $this->anthillWorkers;
    }

    public function addAnthillWorker(AnthillWorkerMYR $anthillWorker): static
    {
        if (!$this->anthillWorkers->contains($anthillWorker)) {
            $this->anthillWorkers->add($anthillWorker);
            $anthillWorker->setPersonalBoardMYR($this);
        }

        return $this;
    }

    public function removeAnthillWorker(AnthillWorkerMYR $anthillWorker): static
    {
        if ($this->anthillWorkers->removeElement($anthillWorker)) {
            // set the owning side to null (unless already changed)
            if ($anthillWorker->getPersonalBoardMYR() === $this) {
                $anthillWorker->setPersonalBoardMYR(null);
            }
        }

        return $this;
    }

    public function getBonus(): ?int
    {
        return $this->bonus;
    }

    public function setBonus(int $bonus): static
    {
        $this->bonus = $bonus;

        return $this;
    }

    public function getHuntedPreyCount(): ?int
    {
        return $this->huntedPreyCount;
    }

    public function setHuntedPreyCount(int $huntedPreyCount): static
    {
        $this->huntedPreyCount = $huntedPreyCount;

        return $this;
    }

    /**
     * @return Collection<int, PlayerResourceMYR>
     */
    public function getPlayerResourceMYRs(): Collection
    {
        return $this->playerResourceMYRs;
    }

    public function addPlayerResourceMYR(PlayerResourceMYR $playerResourceMYR): static
    {
        if (!$this->playerResourceMYRs->contains($playerResourceMYR)) {
            $this->playerResourceMYRs->add($playerResourceMYR);
            $playerResourceMYR->setPersonalBoard($this);
        }

        return $this;
    }

    public function removePlayerResourceMYR(PlayerResourceMYR $playerResourceMYR): static
    {
        if ($this->playerResourceMYRs->removeElement($playerResourceMYR)) {
            // set the owning side to null (unless already changed)
            if ($playerResourceMYR->getPersonalBoard() === $this) {
                $playerResourceMYR->setPersonalBoard(null);
            }
        }

        return $this;
    }
}
