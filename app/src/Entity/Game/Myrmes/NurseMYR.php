<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NurseMYRRepository::class)]
class NurseMYR extends Component
{

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\ManyToOne(inversedBy: 'nurseMYRs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerMYR $player = null;

    #[ORM\Column]
    private ?bool $available = null;

    #[ORM\ManyToOne(inversedBy: 'nurses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardMYR $personalBoardMYR = null;

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getPlayer(): ?PlayerMYR
    {
        return $this->player;
    }

    public function setPlayer(?PlayerMYR $player): static
    {
        $this->player = $player;

        return $this;
    }

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
}
