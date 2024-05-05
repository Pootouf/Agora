<?php

namespace App\Service\Platform;

use App\Entity\Platform\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminService
{
    private $entityManager;
    private $hasher;

    /**
     * AdminService constructor.
     *
     * @param EntityManagerInterface $entityManager The entity manager for database interaction.
     * @param UserPasswordHasherInterface $hasher The user password hasher for hashing passwords.
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
    }

    /**
     * Generates a specified number of user accounts.
     *
     * @param int $numberOfAccounts The number of user accounts to generate.
     * @return array The array containing generated usernames.
     */
    public function generateAccounts($numberOfAccounts)
    {
        $users = [];

        for ($i = 0; $i < $numberOfAccounts; $i++) {
            $user = new User();
            $randomName = $this->generateRandomName();
            $user->setUsername($randomName . $i);
            $users[] = $randomName . $i;
            $user->setEmail($randomName . "{$i}@univ-rouen.fr");
            $user->setIsVerified(true);
            $password = $this->hasher->hashPassword($user, 'agora');
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        return $users;
    }

    /**
     * Generates a random name.
     *
     * @return string The randomly generated name.
     */
    private function generateRandomName(): string
    {
        $names = ['alice', 'bob', 'toto', 'elisa', 'eve'];
        $randomNumber = mt_rand(0000000001, 9999999999);
        $name = $names[array_rand($names)];
        $pseudo = $name. '_' . $randomNumber ;
        return $pseudo;
    }
}