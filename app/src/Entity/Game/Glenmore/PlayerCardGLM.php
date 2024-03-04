<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\PlayerCardGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerCardGLMRepository::class)]
class PlayerCardGLM extends Component
{
    #[ORM\ManyToOne(inversedBy: 'playerCardGLM')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardGLM $personalBoard = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CardGLM $card = null;

    public function __construct(PersonalBoardGLM $personalBoard, CardGLM $card)
    {
        $this->personalBoard = $personalBoard;
        $this->card = $card;
    }

    public function getPersonalBoard(): ?PersonalBoardGLM
    {
        return $this->personalBoard;
    }

    public function setPersonalBoard(?PersonalBoardGLM $personalBoard): static
    {
        $this->personalBoard = $personalBoard;

        return $this;
    }

    public function getCard(): ?CardGLM
    {
        return $this->card;
    }

    public function setCard(?CardGLM $card): static
    {
        $this->card = $card;

        return $this;
    }
}
