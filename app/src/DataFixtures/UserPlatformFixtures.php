<?php
namespace App\DataFixtures;

use App\Entity\Platform\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPlatformFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        // Création de 50 utilisateurs
        $users = [];
        for ($i = 1; $i <= 50; $i++) {
            $user = new User();
            $user->setUsername('user' . $i);
            $user->setEmail("user{$i}@univ-rouen.fr");
            $user->setIsVerified(true);
            $password = $this->hasher->hashPassword($user, 'agora');
            $user->setPassword($password);
            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();

        // Ajout de contacts une fois que tous les utilisateurs ont été créés
        foreach ($users as $user) {
            // Ajout de quelques contacts à chaque utilisateur
            for ($j = 1; $j <= 5; $j++) {
                // Sélectionner un utilisateur aléatoire comme contact
                $randomUser = $users[array_rand($users)];

                // Ajouter le contact uniquement s'il est différent de l'utilisateur actuel
                if ($randomUser !== $user) {
                    $user->addContact($randomUser);
                }
            }
        }

        $manager->flush();
    }
}