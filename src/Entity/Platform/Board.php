<?php

namespace App\Entity\Platform;

use App\Repository\Platform\BoardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: BoardRepository::class)]
class Board
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbUserMax = null;

    #[ORM\Column(length: 20)]
    private ?string $status
        = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $invitationTimer = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $inactivityTimer = null;

    #[ORM\Column(length: 255)]
    private ?string $invitationHash = null;

    #[ORM\Column]
    private ?int $nbInvitations = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'boards')]
    private Collection $listUsers;

    #[ORM\Column]
    private ?int $inactivityHours = null;


    public function __construct()
    {
        $this->status = "WAITING";
        $this->invitationHash = sha1(random_bytes(10));
        $this->listUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbUserMax(): ?int
    {
        return $this->nbUserMax;
    }

    public function setNbUserMax(int $nbUserMax): static
    {
        $this->nbUserMax = $nbUserMax;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getInvitationTimer(): ?\DateTimeInterface
    {
        return $this->invitationTimer;
    }

    public function setInvitationTimer(\DateTimeInterface $invitationTimer): static
    {
        $this->invitationTimer = $invitationTimer;

        return $this;
    }

    public function getInactivityTimer(): ?\DateTimeInterface
    {
        return $this->inactivityTimer;
    }

    public function setInactivityTimer(\DateTimeInterface $inactivityTimer): static
    {
        $this->inactivityTimer = $inactivityTimer;

        return $this;
    }

    public function getInvitationHash(): ?string
    {
        return $this->invitationHash;
    }

    public function setInvitationHash(string $invitationHash): static
    {
        $this->invitationHash = $invitationHash;

        return $this;
    }

    public function getNbInvitations(): ?int
    {
        return $this->nbInvitations;
    }

    public function setNbInvitations(int $nbInvitations): static
    {
        $this->nbInvitations = $nbInvitations;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getListUsers(): Collection
    {
        return $this->listUsers;
    }

    //Add user to the Board
    //Pre : $user not in $this->listUsers
    //      $this->listUsers->count < $this->nbMaxUser
    //Post : $user in $this->listUsers
    //       $this->listUser->count += 1
    public function addListUser(User $user): static
    {
        if (!$this->listUsers->contains($user) && $this->isAvailble() ) {
            $this->listUsers->add($user);
        }

        return $this;
    }

    //return the number of players who have joined the table

    public function getUsersNb(): ?int
    {
        return $this->getListUsers()->count();
    }

    //Remove user of the list of player
    public function removeListUser(User $user): static
    {
        if ($this->listUsers->contains($user)) {
            $this->listUsers->remove($user);
        }

        return $this;
    }

    //Return true if the board is availble for a player to join
    public function isAvailble(){
        return $this->status!="IN_GAME" && $this->listUsers->count() + $this->nbInvitations < $this->nbUserMax;
    }

    public function getInactivityHours(): ?int
    {
        return $this->inactivityHours;
    }

    public function setInactivityHours(int $inactivityHours): static
    {
        $this->inactivityHours = $inactivityHours;

        return $this;
    }

    public function hasUser(User $user):bool
    {
        return $this->listUsers->contains($user);
    }

}

