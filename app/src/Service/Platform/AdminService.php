<?php

namespace App\Service\Platform;

use App\Entity\Platform\User;
use App\Repository\Platform\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminService
{

    private $userRepository;
    private $entityManager;
    private $hasher;


    /**
     * AdminService constructor.
     *
     * @param EntityManagerInterface $entityManager The entity manager for database interaction.
     * @param UserPasswordHasherInterface $hasher The user password hasher for hashing passwords.
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
    }

    /**
     * Generates a specified number of user accounts.
     *
     * @param int $numberOfAccounts The number of user accounts to generate.
     * @return array The array containing generated usernames.
     */
    public function generateAccounts(int $numberOfAccounts): array
    {
        $users = [];
        $startingNumber = $this->userRepository->countUsers('user_') + 1;
        $currentNumber = $startingNumber;

        for ($i = 0; $i < $numberOfAccounts; $i++) {
            $username = "user_" . $currentNumber;

            // Vérifiez si le nom d'utilisateur existe déjà
            while ($this->userRepository->findOneBy(['username' => $username])) {
                $currentNumber++;
                $username = "user_" . $currentNumber;
            }

            // Créer et persister le nouvel utilisateur
            $user = new User();
            $user->setUsername($username);
            $users[] = $username;
            $user->setEmail($username . "@univ-rouen.fr");
            $user->setIsVerified(true);
            $password = $this->hasher->hashPassword($user, 'agora');
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $this->entityManager->persist($user);

            // Incrémenter le numéro pour le prochain utilisateur
            $currentNumber++;
        }

        $this->entityManager->flush();

        return $users;
    }


}