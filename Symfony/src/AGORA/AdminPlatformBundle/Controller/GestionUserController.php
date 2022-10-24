<?php

namespace AGORA\AdminPlatformBundle\Controller;

use AGORA\AdminPlatformBundle\Entity\TaskGenUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GestionUserController extends Controller
{
    public function addUserAction(Request $request)   {
        $taskGenUser = new TaskGenUser();
        $form = $this->createFormBuilder($taskGenUser)
            ->add('prefix',    TextType::class, array('label'  => 'Nom d\'utilisateur',))
            ->add('password',  PasswordType::class, array('label'  => 'Mot de passe',))
            ->add('quantity',  IntegerType::class, array('label'  => 'Quantité',))
            ->add('Créer',      SubmitType::class)
            ->getForm()
        ;

        if ($request->isMethod('POST')) {
            for ($i = 0; $i < $request->get('quantity'); $i++) {
                $this->register("[".$request->get('prefix').$i."]", $request->get('prefix').$i, $request->get('password'));
            }
            return new Response("Succès");
        }
        return $this->render('AGORAAdminPlatformBundle:GestionUser:createUser.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function removeUserAction(Request $request) {
        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $userRepository = $em->getRepository('AGORAUserBundle:User');
            $user = $userRepository->find($request->get('id'));
            $em->remove($user);
            $em->flush();
            return new Response("Succes");

        }
        return new Response("Echec");
    }

    public function promoteUserAction(Request $request) {
        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $userRepository = $em->getRepository('AGORAUserBundle:User');
            $user = $userRepository->find($request->get('id'));
            $user->addRole('ROLE_MODO');
            $em->persist($user);
            $em->flush();
            return new Response("Succes");
        }
        return new Response("Echec");
    }

    public function demoteUserAction(Request $request) {
        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $userRepository = $em->getRepository('AGORAUserBundle:User');
            $user = $userRepository->find($request->get('id'));
            $user->removeRole('ROLE_MODO');
            $em->persist($user);
            $em->flush();
            return new Response("Succes");
        }
        return new Response("Echec");
    }



    public function getUserAction() {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('AGORAUserBundle:User');
        $users = $userRepository->findAll();
        return $this->render('AGORAAdminPlatformBundle:GestionUser:getUser.html.twig', array(
            'users' => $users
        ));
    }

    private function register($email, $username, $password) {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setEnabled(1); // enable the user or enable it later with a confirmation token in the email
        // this method will encrypt the password with the default settings :)
        $user->setPlainPassword($password);
        $userManager->updateUser($user);
    }
}
