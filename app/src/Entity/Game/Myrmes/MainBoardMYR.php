<?php

namespace App\Entity\Game\Myrmes;

use App\Repository\Game\Myrmes\MainBoardMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MainBoardMYRRepository::class)]
class MainBoardMYR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $yearNum = null;

    #[ORM\OneToOne(inversedBy: 'mainBoardMYR', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?SeasonMYR $actualSeason = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
