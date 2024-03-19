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

    #[ORM\ManyToMany(targetEntity: self::class)]
    private Collection $contacts;


    public function __construct()
    {
        $this->boards = new ArrayCollection();
        $this->favoriteGames = new ArrayCollection();

        $this->notifications = new ArrayCollection();

        $this->contacts = new ArrayCollection();
    }

    /**
     * Gets the ID of the user.
     *
     * @return int|null The ID of the user.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the email address of the user.
     *
     * @return string|null The email address of the user.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the email address of the user.
     *
     * @param string $email The email address of the user.
     * @return static
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

   /**
     * Gets a unique identifier representing this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }


    /**
     * Gets the roles assigned to the user.
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Sets the roles assigned to the user.
     *
     * @param array $roles The roles assigned to the user.
     * @return static
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * Gets the hashed password of the user.
     *
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Sets the hashed password of the user.
     *
     * @param string $password The hashed password of the user.
     * @return static
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

 
    /**
     * Clears any sensitive information stored on the user.
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Gets the username of the user.
     *
     * @return string|null The username of the user.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Sets the username of the user.
     *
     * @param string $username The username of the user.
     * @return static
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Checks if the user is verified.
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * Sets whether the user is verified.
     *
     * @param bool $isVerified Whether the user is verified.
     * @return static
     */
    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

   /**
     * Gets the collection of boards associated with the user.
     *
     * @return Collection<int, Board> The collection of boards associated with the user.
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

    /**
     * Removes a board from the collection of boards associated with the user.
     *
     * @param Board $board The board to remove.
     * @return static
     */
    public function removeBoard(Board $board): static
    {
        if ($this->boards->removeElement($board)) {
            $board->removeListUser($this);
        }
        return $this;
    }


    /**
     * Gets the collection of favorite games associated with the user.
     *
     * @return Collection<int, Game> The collection of favorite games associated with the user.
     */
    public function getFavoriteGames(): Collection
    {
        return $this->favoriteGames;
    }

    /**
     * Adds a favorite game to the collection of favorite games associated with the user.
     *
     * @param Game $favoriteGames The favorite game to add.
     * @return static
     */
    public function addFavoriteGame(Game $favoriteGames): static
    {
        if (!$this->favoriteGames->contains($favoriteGames)) {
            $this->favoriteGames->add($favoriteGames);
        }

        return $this;
    }

    /**
     * Removes a favorite game from the collection of favorite games associated with the user.
     *
     * @param Game $favoriteGames The favorite game to remove.
     * @return static
     */
    public function removeFavoriteGame(Game $favoriteGames): static
    {
        $this->favoriteGames->removeElement($favoriteGames);

        return $this;
    }

    /**
     * Gets the collection of notifications received by the user.
     *
     * @return Collection<int, Notification> The collection of notifications received by the user.
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    /**
     * Adds a notification to the collection of notifications received by the user.
     *
     * @param Notification $notification The notification to add.
     * @return static
     */
    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setReceiver($this);
        }
        return $this;
    }

    /**
     * Gets the collection of contacts associated with the user.
     *
     * @return Collection<int, User> The collection of contacts associated with the user.
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    /**
     * Adds a contact to the collection of contacts associated with the user.
     *
     * @param User $contact The contact to add.
     * @return static
     */
    public function addContact(self $contact): static
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
        }

        return $this;
    }

    /**
     * Removes a notification from the collection of notifications received by the user.
     *
     * @param Notification $notification The notification to remove.
     * @return static
     */
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
    
    /**
     * Removes a contact from the collection of contacts associated with the user.
     *
     * @param User $contact The contact to remove.
     * @return static
     */
    public function removeContact(self $contact): static
    {
        $this->contacts->removeElement($contact);

        return $this;
    }
}