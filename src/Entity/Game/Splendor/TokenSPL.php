<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Token;
use App\Repository\Game\Splendor\TokenSPLRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenSPLRepository::class)]
class TokenSPL extends Token
{
    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\ManyToOne(inversedBy: 'tokens')]
    private ?PersonalBoardSPL $personalBoardSPL = null;

    #[ORM\ManyToOne(inversedBy: 'tokens')]
    private ?MainBoardSPL $mainBoardSPL = null;

    #[ORM\ManyToOne(inversedBy: 'selectedTokens')]
    private ?PersonalBoardSPL $tempPersonalBoardSPL = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

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

    public function getMainBoardSPL(): ?MainBoardSPL
    {
        return $this->mainBoardSPL;
    }

    public function setMainBoardSPL(?MainBoardSPL $mainBoardSPL): static
    {
        $this->mainBoardSPL = $mainBoardSPL;

        return $this;
    }

    public function getTempPersonalBoardSPL(): ?PersonalBoardSPL
    {
        return $this->tempPersonalBoardSPL;
    }

    public function setTempPersonalBoardSPL(?PersonalBoardSPL $tempPersonalBoardSPL): static
    {
        $this->tempPersonalBoardSPL = $tempPersonalBoardSPL;

        return $this;
    }
}
