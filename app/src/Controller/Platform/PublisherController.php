<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Notification;
use App\Entity\Platform\User;
use App\Service\Platform\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PublisherController extends AbstractController
{
    private NotificationService $notificationService;
    private Security $security;

    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, NotificationService $notificationService, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;

    }



    #[Route('/notifications', name: 'app_notification_receive')]
    public function notificationsByReceiver(): Response
    {
        if($this->security->getUser()){
            $user = $this->security->getUser();
            $notifications = $this->entityManager->getRepository(Notification::class)
                ->findBy(
                    ['receiver' => $user],
                    ['createdAt' => 'DESC']
                );
        }else{
            $notifications = null;
        }

        return $this->render('platform/publisher/send.html.twig', [
            'notifications' => $notifications,
        ]);
    }
    #[Route('/notifications/send', name: 'app_notification_send')]
    public function notificationsSender(Request $request): Response
    {
        // Create a notification
        $notification = new Notification();
        $form = $this->createFormBuilder($notification)
            ->add('receiver', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Receiver',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Content',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Send Notification',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->getForm();

        $form->handleRequest($request);
        // Send notification and make a real time object
        if ($form->isSubmitted() && $form->isValid()) {
            // Store notification in database
            $notification->setIsRead(false);
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
            // Sent a notification
            $receiverId = $notification->getReceiver()->getId();
            $content = $notification->getContent();
            $date = $notification->getCreatedAt()->format('Y-m-d H:i:s.u');

            $this->notificationService->notifyUser($receiverId, $content, $date);

            return $this->redirectToRoute('app_notification_receive');
        }

        return $this->render('platform/publisher/send.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
