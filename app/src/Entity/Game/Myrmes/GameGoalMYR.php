<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\GameGoalMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameGoalMYRRepository::class)]
class GameGoalMYR extends Component
{
    #[ORM\ManyToMany(targetEntity: PlayerMYR::class, inversedBy: 'gameGoalMYRs')]
    private Collection $precedentsPlayers;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GoalMYR $goal = null;

    public function __construct()
    {
        $this->precedentsPlayers = new ArrayCollection();
    }

    /**
     * @return Collection<int, PlayerMYR>
     */
    public function getPrecedentsPlayers(): Collection
    {
        return $this->precedentsPlayers;
    }

    public function addPrecedentsPlayer(PlayerMYR $precedentsPlayer): static
    {
        if (!$this->precedentsPlayers->contains($precedentsPlayer)) {
            $this->precedentsPlayers->add($precedentsPlayer);
        }

        return $this;
    }

    public function removePrecedentsPlayer(PlayerMYR $precedentsPlayer): static
    {
        $this->precedentsPlayers->removeElement($precedentsPlayer);

        return $this;
    }

    public function getGoal(): ?GoalMYR
    {
        return $this->goal;
    }

    public function setGoal(?GoalMYR $goal): static
    {
        $this->goal = $goal;

        return $this;
    }
}
