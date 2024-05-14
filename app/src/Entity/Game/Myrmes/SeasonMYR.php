<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\SeasonMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeasonMYRRepository::class)]
class SeasonMYR extends Component
{
    #[ORM\Column]
    private ?int $diceResult = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'seasons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MainBoardMYR $mainBoard = null;

    #[ORM\Column]
    private ?bool $actualSeason = null;

    public function getDiceResult(): ?int
    {
        return $this->diceResult;
    }

    public function setDiceResult(int $diceResult): static
    {
        $this->diceResult = $diceResult;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMainBoard(): ?MainBoardMYR
    {
        return $this->mainBoard;
    }

    public function setMainBoard(?MainBoardMYR $mainBoard): static
    {
        $this->mainBoard = $mainBoard;

        return $this;
    }

    public function isActualSeason(): ?bool
    {
        return $this->actualSeason;
    }

    public function setActualSeason(bool $actualSeason): static
    {
        $this->actualSeason = $actualSeason;

        return $this;
    }
}
