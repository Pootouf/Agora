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


    /**
     * NotificationService constructor.
     *
     * @param HubInterface $hub The Mercure hub interface for publishing updates.
     * @param EntityManagerInterface $entityManager The entity manager for database interaction.
     */
    public function __construct(HubInterface $hub, EntityManagerInterface $entityManager){
    $this->hub = $hub;
    $this->entityManager = $entityManager;
    }

    /**
     * Publishes a notification to a specific group.
     *
     * @param string $topic The topic to publish the notification to.
     * @param string $content The content of the notification.
     * @param \DateTime $date The date of the notification.
     * @param string $type The type of the notification.
     */
    public function notifyGroup($topic, $content, $date, $type): void
    {
        $update = new Update(
            $topic,
            json_encode(['content' => $content,
                'date' => $date, 'type' => $type])
        );

        $this->hub->publish($update);

    }

    /**
     * Publishes a notification to a specific user.
     *
     * @param mixed $user The user to notify.
     * @param string $content The content of the notification.
     * @param \DateTime $date The date of the notification.
     * @param string $type The type of the notification.
     */
    public function notifyUser($user, $content, $date, $type): void
    {
        $update = new Update(
            'agora/notifications/user/'. $user,
            json_encode(['content' => $content,
                'date' => $date, 'type' => $type])
        );

        $this->hub->publish($update);
    }

    /**
     * Stores a notification in the database.
     *
     * @param mixed $user The user receiving the notification.
     * @param string $content The content of the notification.
     * @param string $type The type of the notification.
     */
    public function  storeNotification($user, $content, $type): void
    {
        $notification = new Notification();
        $notification->setContent($content);
        $notification->setType($type);
        $notification->setIsRead(false);
        $notification->setReceiver($user);
        $this->entityManager->persist($notification);
    }

    /**
     * Notifies multiple users and stores notifications in the database.
     *
     * @param array $users The users to notify.
     * @param string $content The content of the notification.
     * @param \DateTime $date The date of the notification.
     * @param string $type The type of the notification.
     */
    public function notifyManyUser($users, $content, $date, $type): void
    {
        foreach ($users as $user) {
            $this->storeNotification($user, $content, $type);
            $this->notifyUser($user->getId(), $content, $date->format('Y-m-d H:i:s.u'), $type);
        }
        $this->entityManager->flush();
    }

    /**
     * Retrieves unread notifications for the current user.
     *
     * @param Security $security The security service for user authentication.
     * @return array|null The array of unread notifications or null if user is not authenticated.
     */
    public function getNotifications(Security $security)
    {
        $user = $security->getUser();
        if ($user) {
            $notifications = $this->entityManager->getRepository(Notification::class)
                ->findBy(
                    ['receiver' => $user, 'isRead' => false],
                    ['createdAt' => 'DESC'],
                );
        } else {
            $notifications = null;
        }
        return $notifications;
    }

}
