<?php

namespace App\Service\Platform;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NotificationService
{
    private HubInterface $hub;


    public function __construct(HubInterface $hub){
    $this->hub = $hub;
    }

    public function notifyGroup($topic, $content, $date): void
    {
        $update = new Update(
            $topic,
            json_encode(['content' => $content,
                'date' => $date])
        );

        $this->hub->publish($update);

    }

    public function notifyUser($user, $content, $date): void
    {
        $update = new Update(
            'agora/notifications/user/'. $user,
            json_encode(['content' => $content,
                'date' => $date]),
            true
        );

        $this->hub->publish($update);
    }


}