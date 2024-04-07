<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Form\Platform\EditProfileType;
use App\Service\Platform\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Class_;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class UserController extends AbstractController
{
    private UserService $userService;
    private Security $security;
    public function __construct(
        UserService $userService,
        Security $security
    )
    {
        $this->userService = $userService;
        $this->security = $security;
    }

    #[Route('/user/addContact/{contact_id}', name: 'app_user_add_contact', requirements: ['user_id' => '\d+'])]
    public function addContact(int $contact_id) : Response
    {
        $userId = $this->security->getUser()->getId();
        $resultCode = $this->userService->addContact($userId, $contact_id);
        if($resultCode < 0){
            $this->addFlash(
                'failure',
                'Erreur lors de l\'ajout du contact'
            );
            return  $this->redirectToRoute('app_other_user_profile', ['user_id' => $contact_id]);
        }
        return $this->redirectToRoute('app_other_user_profile', ['user_id' => $contact_id]);
    }

    #[Route('/user/removeContact/{contact_id}', name: 'app_user_remove_contact', requirements: ['user_id' => '\d+'])]
    public function removeContact(int $contact_id) : Response
    {
        $userId = $this->security->getUser()->getId();
        $resultCode = $this->userService->removeContact($userId, $contact_id);
        if($resultCode < 0){
            $this->addFlash(
                'failure',
                'Erreur lors du retrait du contact'
            );
            return  $this->redirectToRoute('app_other_user_profile', ['user_id' => $contact_id]);
        }
        return $this->redirectToRoute('app_other_user_profile', ['user_id' => $contact_id]);
    }

    #[Route('/dashboard/contacts', name: 'app_contacts')]
    public function contacts(): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();
    
        // Récupérer la liste de contacts pour cet utilisateur
        $contacts = $user->getContacts();
    
        // Afficher la liste de contacts dans le template
        return $this->render('platform/dashboard/contacts.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    #[Route('/user/{id}/editProfile', name: 'app_user_edit_profile')]
    public function editAccount(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHarsher, User $user) :Response
    {
        /*
         * we can use this part of code for edit account without passing by the id in the URL
         * get the logged user
         * $userId = $this->security->getUser()->getId();
         * $user = $entityManager->getRepository(User::class)->find($userId);
         * */

        //create the form based on user class
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$user` variable has also been updated
            $this->addFlash('success', 'the user data has been modified');
            $user->setPassword(
                $userPasswordHarsher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard_settings');
        }

        return $this->render('platform/users/editUserProfile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/delete', name: 'app_user_delete')]
public function deleteUser(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, TokenStorageInterface $tokenStorage,  SessionInterface $session): RedirectResponse
{    $user = $this->getUser();
        if (!$user) {
        return $this->redirectToRoute('app_homepage');
    }
    
    // Supprimer l'utilisateur
    $entityManager->remove($user);
    $entityManager->flush();

     // Déconnecter l'utilisateur en invalidant le token de sécurité
     $tokenStorage->setToken(null);
     $session->getFlashBag()->add('success', 'Votre compte a bien été supprimé.');
     return $this->redirectToRoute('app_home');
}

}