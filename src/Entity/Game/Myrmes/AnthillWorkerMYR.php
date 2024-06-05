<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\AnthillWorkerMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnthillWorkerMYRRepository::class)]
class AnthillWorkerMYR extends Component
{
    #[ORM\Column]
    private ?int $workFloor = null;

    #[ORM\ManyToOne(inversedBy: 'anthillWorkers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardMYR $personalBoardMYR = null;

    public function getWorkFloor(): ?int
    {
        return $this->workFloor;
    }

    public function setWorkFloor(int $workFloor): static
    {
        $this->workFloor = $workFloor;

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
