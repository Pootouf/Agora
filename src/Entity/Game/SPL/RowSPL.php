<?php

namespace App\Entity\Game\SPL;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\SPL\RowSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RowSPLRepository::class)]
class RowSPL extends Component
{
    #[ORM\Column]
    private ?int $cardLevel = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSPL $game = null;

    #[ORM\OneToMany(targetEntity: DevelopmentCardsSPL::class, mappedBy: 'rowSPL')]
    private Collection $developmentCards;

    #[ORM\ManyToOne(inversedBy: 'rowsSPL')]
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

    public function getCardLevel(): ?int
    {
        return $this->cardLevel;
    }

    public function setCardLevel(int $cardLevel): static
    {
        $this->cardLevel = $cardLevel;

        return $this;
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
            $developmentCard->setRowSPL($this);
        }

        return $this;
    }

    public function removeDevelopmentCard(DevelopmentCardsSPL $developmentCard): static
    {
        if ($this->developmentCards->removeElement($developmentCard)) {
            // set the owning side to null (unless already changed)
            if ($developmentCard->getRowSPL() === $this) {
                $developmentCard->setRowSPL(null);
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
