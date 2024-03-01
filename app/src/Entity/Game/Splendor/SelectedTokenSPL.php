<?php

namespace App\Entity\Game\Splendor;

use App\Repository\Game\Splendor\SelectedTokenSPLRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SelectedTokenSPLRepository::class)]
class SelectedTokenSPL
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TokenSPL $token = null;

    #[ORM\ManyToOne(inversedBy: 'selectedTokens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardSPL $personalBoardSPL = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?TokenSPL
    {
        return $this->token;
    }

    public function setToken(?TokenSPL $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getPersonalBoardSPL(): ?PersonalBoardSPL
    {
        return $this->personalBoardSPL;
    }

    public function setPersonalBoardSPL(?PersonalBoardSPL $personalBoardSPL): static
    {
        $this->personalBoardSPL = $personalBoardSPL;

        return $this;
    }
}
