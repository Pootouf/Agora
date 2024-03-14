<?php

namespace App\Entity\Platform;

use App\Repository\Platform\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Nous avons déjà enrigistré un compte avec cet email')]
#[UniqueEntity(fields: ['username'], message: 'Nous avons déjà enrigistré un compte avec ce nom d\'utilisateur')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(
        message: "L'adresse email '{{ value }}' n'est pas valide."
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\Unique]
    #[Assert\Length(
        min: 4,
        max: 20,
        minMessage: 'Votre mot de passe doit être composé au moins de {{ limit }} caractères',
        maxMessage: 'Votre mot de passe doit être composé au plus de {{ limit }} caractères',
    )]
    private ?string $username = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;


    #[ORM\ManyToMany(targetEntity: Game::class)]
    private Collection $favoriteGames;


    #[ORM\ManyToMany(targetEntity: Board::class, mappedBy: 'listUsers')]
    private Collection $boards;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'receiver')]
    private Collection $notifications;

    public function __construct()
    {
        $this->boards = new ArrayCollection();
        $this->favoriteGames = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Board>
     */
    public function getBoards(): Collection
    {
        return $this->boards;
    }

    //Add Board to the User's board list
    //Pre : $board->isAvaible()
    //      $this not in $board
    //Post : $board in $this->boards
    //       $boards->count() = OLD:$boards->count() + 1
    public function addBoard(Board $board): static
    {
        if (!$this->boards->contains($board) && $board->isAvailble()) {
            $this->boards->add($board);
            $board->addListUser($this);
        }

        return $this;

    }

    public function removeBoard(Board $board): static
    {
        if ($this->boards->removeElement($board)) {
            $board->removeListUser($this);
        }
        return $this;
    }


    /**
     * @return Collection<int, Game>
     */
    public function getFavoriteGames(): Collection
    {
        return $this->favoriteGames;
    }

    public function addFavoriteGame(Game $favoriteGames): static
    {
        if (!$this->favoriteGames->contains($favoriteGames)) {
            $this->favoriteGames->add($favoriteGames);
        }

        return $this;
    }


    public function removeFavoriteGame(Game $favoriteGames): static
    {
        $this->favoriteGames->removeElement($favoriteGames);

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setReceiver($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getReceiver() === $this) {
                $notification->setReceiver(null);
            }
        }

        return $this;
    }
}