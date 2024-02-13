<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Splendor\DrawCardsSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DrawCardsSPLRepository::class)]
class DrawCardsSPL extends Component
{
    public static int $LEVEL_ONE = 0;
    public static int $LEVEL_TWO = 1;
    public static int $LEVEL_THREE = 2;

    #[ORM\ManyToOne(inversedBy: 'drawCardsSPL')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSPL $game = null;

    #[ORM\Column]
    private ?int $cardLevel = null;

    #[ORM\OneToMany(targetEntity: DevelopmentCardsSPL::class, mappedBy: 'drawCardsSPL')]
    private Collection $developmentCards;

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
        }

        return $this;
    }

    public function removeDevelopmentCard(DevelopmentCardsSPL $developmentCard): static
    {
        $this->developmentCards->removeElement($developmentCard);

        return $this;
    }

}
