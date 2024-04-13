<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Form\Platform\RegistrationFormType;
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

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * Handles user registration and redirect to login page after a successful registration
     * 
     * @param Request $request The HTTP containing the registration form data
     * @param UserPasswordHasherInterface  $userPasswordHasher The component used for hashing user passwords
     * @param EntityManagerInterface $entityManager the entity manager to interact with the database
     * 
     * @return Response HTTP response containing the registration form or a redirection to the login page
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser()) {
            return $this->redirect('/');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // Set Role to user
            $user->setRoles(['ROLE_PLAYER']);

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('agora@univ-rouen.fr', 'Agora assistant'))
                    ->to($user->getEmail())
                    ->subject('Confirmation de mail pour votre compte agora')
                    ->htmlTemplate('platform/registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            $this->addFlash('success-account', 'Bienvenue chez Agora, votre compte a été bien créé. Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_login');
        }else{
            foreach ($form->getErrors(true, true) as $error) {
                $errors[] = $error->getMessage();
            }
        }

        return $this->render('platform/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * Send email verification mail
     * 
     * @param Request $request HTTP request object
     * @param TranslatorInterface $translator for translating exception messages
     * 
     * @return Response HTTP response redirects to homepage after successfully verifying the email, redirects to the registration page in case of a failure of sending the mail
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_dashboard');
    }
}
