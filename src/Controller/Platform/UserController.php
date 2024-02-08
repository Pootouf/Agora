<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Form\Platform\EditUserType;
use Composer\DependencyResolver\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/{id}', name:'app_userbyid')]
    public function sendUserInfo(int $id, EntityManagerInterface $entityManager): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $user = $entityManager->getRepository(User::class)->find($id);

        if(!$user){
            throw $this->createNotFoundException(
                "user not found".$id);
        }


        return new Response('testing controller');
    }

    #[Route('/user/edit/{id}', name:'app_editUser')]
    public function editUser(int $id, EntityManagerInterface $entityManager, Request $request) :Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $user = $entityManager->getRepository(User::class)->find($id);

        if(!$user){
            throw $this->createNotFoundException(
                "user not found ".$id);
        }

        $form = $this->createForm(EditUserType::class, User::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('/');
        }

        return  $this->render();




        return new Response();
    }




}