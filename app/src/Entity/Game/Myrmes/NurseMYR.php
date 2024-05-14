<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NurseMYRRepository::class)]
class NurseMYR extends Component
{
    #[ORM\Column]
    private ?bool $available = null;

    #[ORM\ManyToOne(inversedBy: 'nurses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardMYR $personalBoardMYR = null;

    #[ORM\Column]
    private ?int $area = null;

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): static
    {
        $this->available = $available;

        return $this;
    }

    public function getPersonalBoardMYR(): ?PersonalBoardMYR
    {
        return $this->personalBoardMYR;
    }

    public function setPersonalBoardMYR(?PersonalBoardMYR $personalBoardMYR): static
    {
        $this->personalBoardMYR = $personalBoardMYR;

        return $this;
    }

    public function getArea(): ?int
    {
        return $this->area;
    }

    public function setArea(int $area): static
    {
        $this->area = $area;

        return $this;
    }
}
