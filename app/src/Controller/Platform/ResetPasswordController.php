<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Form\Platform\ResetPassword;
use App\Form\Platform\ResetPasswordEmail;
use App\Security\Platform\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class ResetPasswordController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    #[Route('/resetpassword', name: 'app_reset_password')]
    public function requestPasswordReset(Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(ResetPasswordEmail::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                // generate a signed url and email it to the user
                $this->emailVerifier->sendResetPasswordConfirmation('app_reset_password_confirm', $user,
                    (new TemplatedEmail())
                        ->from(new Address('agora@univ-rouen.fr', 'Agora assistant'))
                        ->to($user->getEmail())
                        ->subject('Changement de mot de passe compte agora')
                        ->htmlTemplate('platform/registration/confirmation_email.html.twig')
                );
                // do anything else you need here, like send an email

                $this->addFlash('success-account', 'Nous vous avons envoyé un mail à cette adresse email donnée');
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('warning', "Votre utilisateur n'existe pas dans notre base.");
                return $this->redirectToRoute('app_reset_password');
            }
        }

        return $this->render('platform/security/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/resetpassword/confirm', name: 'app_reset_password_confirm')]
    public function resetPasswordConfirm(Request $request,  TranslatorInterface $translator, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher,): Response
    {
        $id = $request->query->get('id'); // retrieve the user id from the url
        // Verify the user id exists and is not null
        if (null === $id) {
            return $this->redirectToRoute('app_login');
        }

        $user = $entityManager->getRepository(User::class)->find($id);

        $form = $this->createForm(ResetPassword::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $password = $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                );
            try {
                $this->emailVerifier->handleResetPassword($user, $password);
                $this->addFlash('success-account', 'Votre mot de passe a bien été changé.');
                return $this->redirectToRoute('app_login');
            } catch (VerifyEmailExceptionInterface $exception) {
                $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('platform/security/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
