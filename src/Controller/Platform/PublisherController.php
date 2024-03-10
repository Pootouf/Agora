<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Notification;
use App\Entity\Platform\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;

class PublisherController extends AbstractController
{

    #[Route('/notifications', name: 'app_notification_receive')]
    public function notificationsByReceiver(Security $security, EntityManagerInterface $entityManager): Response
    {
        if($security->getUser()){
            $user = $security->getUser();
            $notifications = $entityManager->getRepository(Notification::class)
                ->findBy(
                    ['receiver' => $user],
                    ['createdAt' => 'DESC']
                );
        }else{
            $notifications = null;
        }

        return $this->render('platform/publisher/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
    #[Route('/notifications/send', name: 'app_notification_send')]
    public function notificationsSender(Request $request, EntityManagerInterface $entityManager, HubInterface $hub): Response
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
            $notification->setIsRead(false);
            $entityManager->persist($notification);
            $entityManager->flush();
//            Send a update with mercure for real time interaction
            $update = new Update(
                'agora/notifications/'. $notification->getReceiver()->getId(),
                json_encode(['content' => $notification->getContent(),
                    'date' => $notification->getCreatedAt()->format('Y-m-d H:i:s.u')])
            );

            $hub->publish($update);

            return $this->redirectToRoute('app_notification_receive');
        }

        return $this->render('platform/publisher/send.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
