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

    #[ORM\OneToOne(mappedBy: 'actualSeason', cascade: ['persist', 'remove'])]
    private ?MainBoardMYR $mainBoardMYR = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function getDiceResult(): ?int
    {
        return $this->diceResult;
    }

    public function setDiceResult(int $diceResult): static
    {
        $this->diceResult = $diceResult;

        return $this;
    }

    public function getMainBoardMYR(): ?MainBoardMYR
    {
        return $this->mainBoardMYR;
    }

    public function setMainBoardMYR(MainBoardMYR $mainBoardMYR): static
    {
        // set the owning side of the relation if necessary
        if ($mainBoardMYR->getActualSeason() !== $this) {
            $mainBoardMYR->setActualSeason($this);
        }

        $this->mainBoardMYR = $mainBoardMYR;

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
}
