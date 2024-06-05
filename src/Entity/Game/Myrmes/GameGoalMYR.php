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

    #[ORM\ManyToOne(inversedBy: 'gameGoalsLevelOne')]
    private ?MainBoardMYR $mainBoardLevelOne = null;

    #[ORM\ManyToOne(inversedBy: 'gameGoalsLevelTwo')]
    private ?MainBoardMYR $mainBoardLevelTwo = null;

    #[ORM\ManyToOne(inversedBy: 'gameGoalsLevelThree')]
    private ?MainBoardMYR $mainBoardLevelThree = null;

    #[ORM\ManyToMany(targetEntity: PlayerMYR::class)]
    #[ORM\JoinTable(name: "game_goal_player_myr_already_done")]
    private Collection $goalAlreadyDone;

    public function __construct()
    {
        $this->precedentsPlayers = new ArrayCollection();
        $this->goalAlreadyDone = new ArrayCollection();
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

    public function getMainBoardLevelOne(): ?MainBoardMYR
    {
        return $this->mainBoardLevelOne;
    }

    public function setMainBoardLevelOne(?MainBoardMYR $mainBoardLevelOne): static
    {
        $this->mainBoardLevelOne = $mainBoardLevelOne;

        return $this;
    }

    public function getMainBoardLevelTwo(): ?MainBoardMYR
    {
        return $this->mainBoardLevelTwo;
    }

    public function setMainBoardLevelTwo(?MainBoardMYR $mainBoardLevelTwo): static
    {
        $this->mainBoardLevelTwo = $mainBoardLevelTwo;

        return $this;
    }

    public function getMainBoardLevelThree(): ?MainBoardMYR
    {
        return $this->mainBoardLevelThree;
    }

    public function setMainBoardLevelThree(?MainBoardMYR $mainBoardLevelThree): static
    {
        $this->mainBoardLevelThree = $mainBoardLevelThree;

        return $this;
    }

    /**
     * @return Collection<int, PlayerMYR>
     */
    public function getGoalAlreadyDone(): Collection
    {
        return $this->goalAlreadyDone;
    }

    public function addGoalAlreadyDone(PlayerMYR $goalAlreadyDone): static
    {
        if (!$this->goalAlreadyDone->contains($goalAlreadyDone)) {
            $this->goalAlreadyDone->add($goalAlreadyDone);
        }

        return $this;
    }

    public function removeGoalAlreadyDone(PlayerMYR $goalAlreadyDone): static
    {
        $this->goalAlreadyDone->removeElement($goalAlreadyDone);

        return $this;
    }
}
