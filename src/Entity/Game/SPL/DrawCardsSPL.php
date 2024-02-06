<?php

namespace App\Entity\Game\SPL;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\SPL\DrawCardsSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DrawCardsSPLRepository::class)]
class DrawCardsSPL extends Component
{
    #[ORM\ManyToOne(inversedBy: 'drawCardsSPL')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSPL $game = null;

    #[ORM\Column]
    private ?int $cardLevel = null;

    #[ORM\OneToMany(targetEntity: DevelopmentCardsSPL::class, mappedBy: 'drawCardsSPL')]
    private Collection $developmentCards;

    #[ORM\ManyToOne(inversedBy: 'drawCards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MainBoardSPL $mainBoardSPL = null;

    public function __construct()
    {
        $this->developmentCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?GameSPL
    {
        return $this->game;
    }

    public function setGame(?GameSPL $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getCardLevel(): ?int
    {
        return $this->cardLevel;
    }

    public function setCardLevel(int $cardLevel): static
    {
        $this->cardLevel = $cardLevel;

        return $this;
    }

    /**
     * @return Collection<int, DevelopmentCardsSPL>
     */
    public function getDevelopmentCards(): Collection
    {
        return $this->developmentCards;
    }

    public function addDevelopmentCard(DevelopmentCardsSPL $developmentCard): static
    {
        if (!$this->developmentCards->contains($developmentCard)) {
            $this->developmentCards->add($developmentCard);
            $developmentCard->setDrawCardsSPL($this);
        }

        return $this;
    }

    public function removeDevelopmentCard(DevelopmentCardsSPL $developmentCard): static
    {
        if ($this->developmentCards->removeElement($developmentCard)) {
            // set the owning side to null (unless already changed)
            if ($developmentCard->getDrawCardsSPL() === $this) {
                $developmentCard->setDrawCardsSPL(null);
            }
        }

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
}
