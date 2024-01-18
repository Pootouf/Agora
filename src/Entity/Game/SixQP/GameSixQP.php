<?php

namespace App\Entity\Game\SixQP;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\SixQP\GameSixQPRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameSixQPRepository::class)]
class GameSixQP extends Component
{

    public static int $NUMBER_OF_ROWS_BY_GAME = 4;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: RowSixQP::class, orphanRemoval: true)]
    private Collection $rowSixQPs;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: PlayerSixQP::class, orphanRemoval: true)]
    private Collection $playerSixQPs;

    public function __construct()
    {
        $this->rowSixQPs = new ArrayCollection();
        for($i = 0; $i < GameSixQP::$NUMBER_OF_ROWS_BY_GAME; $i++) {
            $row = new RowSixQP();
            $this->addRowSixQP($row);
        }
        $this->playerSixQPs = new ArrayCollection();
    }

    /**
     * @return Collection<int, RowSixQP>
     */
    public function getRowSixQPs(): Collection
    {
        return $this->rowSixQPs;
    }

    public function addRowSixQP(RowSixQP $rowSixQP): static
    {
        if (!$this->rowSixQPs->contains($rowSixQP)) {
            $this->rowSixQPs->add($rowSixQP);
            $rowSixQP->setGame($this);
        }

        return $this;
    }

    public function removeRowSixQP(RowSixQP $rowSixQP): static
    {
        if ($this->rowSixQPs->removeElement($rowSixQP)) {
            // set the owning side to null (unless already changed)
            if ($rowSixQP->getGame() === $this) {
                $rowSixQP->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlayerSixQP>
     */
    public function getPlayerSixQPs(): Collection
    {
        return $this->playerSixQPs;
    }

    public function addPlayerSixQP(PlayerSixQP $playerSixQP): static
    {
        if (!$this->playerSixQPs->contains($playerSixQP)) {
            $this->playerSixQPs->add($playerSixQP);
            $playerSixQP->setGame($this);
        }

        return $this;
    }

    public function removePlayerSixQP(PlayerSixQP $playerSixQP): static
    {
        if ($this->playerSixQPs->removeElement($playerSixQP)) {
            // set the owning side to null (unless already changed)
            if ($playerSixQP->getGame() === $this) {
                $playerSixQP->setGame(null);
            }
        }

        return $this;
    }
}
