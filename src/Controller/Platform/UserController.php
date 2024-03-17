<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Service\Platform\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Class_;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
        dump($resultCode);
        if($resultCode < 0){
            $this->addFlash(
                'failure',
                'Erreur lors de l\'ajout du contact'
            );
            return  $this->redirectToRoute('app_home');
        }
        return $this->redirectToRoute('app_home');
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
            return  $this->redirectToRoute('app_home');
        }
        return $this->redirectToRoute('app_home');
    }

    #[Route('/dashboard/profile/{user_id}', name: 'app_other_user_profile', requirements: ['user_id' => '\d+'])]
    public function getUserProfile(EntityManagerInterface $entityManager, int $user_id) : Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($user_id);

        return $this->render('platform/dashboard/otherUserProfile.html.twig', [
        'user' => $user,
        ]);
    }

}