<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Token;
use App\Repository\Game\Splendor\TokenSPLRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenSPLRepository::class)]
class TokenSPL extends Token
{

    public static string $COLOR_BLUE = 'blue';
    public static string $COLOR_RED = 'red';
    public static string $COLOR_YELLOW = 'yellow';
    public static string $COLOR_GREEN = 'green';
    public static string $COLOR_WHITE = 'white';
    public static string $COLOR_BLACK = 'black';

    #[ORM\Column(length: 255)]
    private ?string $color = null;

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
}
