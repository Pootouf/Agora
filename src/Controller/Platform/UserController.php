<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Form\Platform\EditUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/{id}', name:'app_userbyid')]
    public function sendUserInfo(int $id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $user = $entityManager->getRepository(User::class)->find($id);

        if(!$user){
            throw $this->createNotFoundException(
                "user not found".$id);
        }

        return new Response(
        );
    }

    #[Route('/user/edit/{id}', name:'app_editUser')]
    public function editUser(int $id, EntityManagerInterface $entityManager) :Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $user = $entityManager->getRepository(User::class)->find($id);

        if(!$user){
            throw $this->createNotFoundException(
                "user not found ".$id);
        }

        $form = $this->createForm(EditUserType::class, User::class);

        return new Response();
    }




}