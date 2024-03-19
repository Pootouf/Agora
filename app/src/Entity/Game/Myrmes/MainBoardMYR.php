<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\MainBoardMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MainBoardMYRRepository::class)]
class MainBoardMYR extends Component
{
    #[ORM\Column]
    private ?int $yearNum = null;

    #[ORM\OneToOne(inversedBy: 'mainBoardMYR', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?SeasonMYR $actualSeason = null;

    #[ORM\OneToOne(inversedBy: 'mainBoardMYR', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameMYR $game = null;

    #[ORM\OneToMany(targetEntity: GardenWorkerMYR::class, mappedBy: 'mainBoardMYR', orphanRemoval: true)]
    private Collection $gardenWorkers;

    public function __construct()
    {
        $this->gardenWorkers = new ArrayCollection();
    }

    public function getYearNum(): ?int
    {
        return $this->yearNum;
    }

    public function setYearNum(int $yearNum): static
    {
        $this->yearNum = $yearNum;

        return $this;
    }

    public function getActualSeason(): ?SeasonMYR
    {
        return $this->actualSeason;
    }

    public function setActualSeason(SeasonMYR $actualSeason): static
    {
        $this->actualSeason = $actualSeason;

        return $this;
    }

    public function getGame(): ?GameMYR
    {
        return $this->game;
    }

    public function setGame(GameMYR $game): static
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return Collection<int, GardenWorkerMYR>
     */
    public function getGardenWorkers(): Collection
    {
        return $this->gardenWorkers;
    }

    public function addGardenWorker(GardenWorkerMYR $gardenWorker): static
    {
        if (!$this->gardenWorkers->contains($gardenWorker)) {
            $this->gardenWorkers->add($gardenWorker);
            $gardenWorker->setMainBoardMYR($this);
        }

        return $this;
    }

    public function removeGardenWorker(GardenWorkerMYR $gardenWorker): static
    {
        if ($this->gardenWorkers->removeElement($gardenWorker)) {
            // set the owning side to null (unless already changed)
            if ($gardenWorker->getMainBoardMYR() === $this) {
                $gardenWorker->setMainBoardMYR(null);
            }
        }

        return $this;
    }
}
