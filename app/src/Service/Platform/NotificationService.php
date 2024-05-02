<?php

namespace App\Service\Platform;

use App\Entity\Platform\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NotificationService
{
    private HubInterface $hub;
    private EntityManagerInterface $entityManager;



    public function __construct(HubInterface $hub, EntityManagerInterface $entityManager){
    $this->hub = $hub;
    $this->entityManager = $entityManager;
    }

    public function notifyGroup($topic, $content, $date, $type): void
    {
        $update = new Update(
            $topic,
            json_encode(['content' => $content,
                'date' => $date, 'type' => $type])
        );

        $this->hub->publish($update);

    }

    public function notifyUser($user, $content, $date, $type): void
    {
        $update = new Update(
            'agora/notifications/user/'. $user,
            json_encode(['content' => $content,
                'date' => $date, 'type' => $type])
        );

        $this->hub->publish($update);
    }

    public function  storeNotification($user, $content, $type): void
    {
        $notification = new Notification();
        $notification->setContent($content);
        $notification->setType($type);
        $notification->setIsRead(false);
        $notification->setReceiver($user);
        $this->entityManager->persist($notification);
    }

    public function notifyManyUser($users, $content, $date, $type): void
    {
        foreach ($users as $user) {
            $this->storeNotification($user, $content, $type);
            $this->notifyUser($user->getId(), $content, $date->format('Y-m-d H:i:s.u'), $type);
        }
        $this->entityManager->flush();
    }

    public function getNotifications(Security $security)
    {
        $user = $security->getUser();
        if ($user){
            $notifications = $this->entityManager->getRepository(Notification::class)
                ->findBy(
                    ['receiver' => $user, 'isRead' => false],
                    ['createdAt' => 'DESC'],
                );
        }else{
            $notifications = null;
        }
        return $notifications;
    }

}