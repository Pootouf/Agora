<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Card;
use App\Repository\Game\Glenmore\CardGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardGLMRepository::class)]
class CardGLM extends Card
{

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?TileBuyBonusGLM $bonus = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBonus(): ?TileBuyBonusGLM
    {
        return $this->bonus;
    }

    public function setBonus(?TileBuyBonusGLM $bonus): static
    {
        $this->bonus = $bonus;

        return $this;
    }
}
