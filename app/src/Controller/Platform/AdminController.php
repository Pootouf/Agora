<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Form\Platform\GenerateAccountsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_dashboard_admin')]
    public function index(): Response
    {
        return $this->render('platform/admin/index.html.twig');
    }

    #[Route('/admin/generate', name: 'app_dashboard_generate')]
    public function createaccount(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(GenerateAccountsType::class);
        $form->handleRequest($request);

        $users = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $numberOfAccounts = $formData['nbAccounts'];
            // Generate and save accounts
            for ($i = 0; $i < $numberOfAccounts; $i++) {
                $user = new User();
                $randomName = $this->generateRandomName();
                $user->setUsername($randomName . $i);
                $users[] = $randomName . $i;
                $user->setEmail($randomName."{$i}@univ-rouen.fr");
                $user->setIsVerified(true);
                $password = $hasher->hashPassword($user, 'agora');
                $user->setPassword($password);
                $user->setRoles(['ROLE_USER']);
                $entityManager->persist($user);
            }
            $entityManager->flush();
            $this->addFlash('success-account', "Vos comptes sont créés avec les noms d'utilisateurs listés avec le mot de passe: agora");
            return $this->render('platform/admin/generateAccount.html.twig', [
                'form' => $form->createView(),
                'users' => $users,
            ]);
        }
        return $this->render('platform/admin/generateAccount.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    private function generateRandomName(): string
    {
        // Liste des prénoms
        $names = ['alice', 'bob', 'toto', 'elisa', 'eve'];

        $randomNumber = mt_rand(0000000001, 9999999999);

        $name = $names[array_rand($names)];

        // Combinaison du nombre et du prénom
        $pseudo = $name. '_' . $randomNumber ;

        return $pseudo;
    }

}
