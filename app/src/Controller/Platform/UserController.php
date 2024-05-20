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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends AbstractController
{
    private UserService $userService;
    private Security $security;

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;

    private TokenStorageInterface $tokenStorage;
    public function __construct(
        UserService $userService,
        Security $security,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->userService = $userService;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $passwordHasher;
        $this->tokenStorage = $tokenStorage;
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

    #[Route('/dashboard/{id}/editProfile', name: 'app_user_edit_profile')]
        #[Route('/admin/{id}/editProfile', name: 'app_admin_edit_profile')]
    public function editAccount(Request $request,  User $user) :Response
    {
        // Récupérer la route de destination
        $routeDest = 'app_dashboard_settings';
        if ($request->attributes->get('_route') === 'app_admin_edit_profile') {
            $routeDest = 'app_admin_settings';
        }

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
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute($routeDest);
        }
        $this->addFlash('error', 'invalid form');
        
        return $this->redirectToRoute($routeDest);
    }



    #[Route('/user/delete/{id}', name: 'app_user_delete')]
public function deleteUser(int $id, SessionInterface $session): RedirectResponse
{
    // Récupérer l'utilisateur à supprimer
    $user = $this->entityManager->getRepository(User::class)->find($id);

    // Récupérer et supprimer les notifications de l'utilisateur
    $notifications = $user->getNotifications();
    foreach ($notifications as $notification) {
        $this->entityManager->remove($notification);
    }
    $this->entityManager->flush();
   
    // RETIRER LES COMMENTAIRES DES QUE LE COMPTE ADMIN SERA CRÉÉ
    // Vérifier si l'utilisateur existe et s'il est autorisé à supprimer d'autres utilisateurs
   // if (!$user || !$this->isGranted('ROLE_ADMIN')) {
     // Rediriger vers une page appropriée en cas d'erreur ou d'autorisation insuffisante
   //     return $this->redirectToRoute('app_home');
   // }
    
    // Supprimer l'utilisateur
    $this->entityManager->remove($user);
    $this->entityManager->flush();

    // Déconnecter l'utilisateur en invalidant le token de sécurité
    $this->tokenStorage->setToken(null);
    $session->getFlashBag()->add('success', 'L\'utilisateur a bien été supprimé.');
    return $this->redirectToRoute('app_home');
}

    #[Route('/user/autodelete', name: 'app_user_autodelete')]
    public function autoDeleteUser(SessionInterface $session): RedirectResponse
    {    $user = $this->getUser();
            if (!$user) {
            return $this->redirectToRoute('app_home');
        }
            // Récupérer et supprimer les notifications de l'utilisateur
        $notifications = $user->getNotifications();
         foreach ($notifications as $notification) {
         $this->entityManager->remove($notification);
        }
        $this->entityManager->flush();

        // Supprimer l'utilisateur
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    
         // Déconnecter l'utilisateur en invalidant le token de sécurité
         $this->tokenStorage->setToken(null);
         $session->getFlashBag()->add('success', 'Votre compte a bien été supprimé.');
         return $this->redirectToRoute('app_home');
    }

}