<?php

namespace App\DataFixtures;

use App\Entity\Platform\Notification;
use App\Entity\Platform\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NotificationService extends Fixture
{

    public function __construct(){}
    public function load(ObjectManager $manager)
    {

        $users = $manager->getRepository(User::class)->findAll();

        //Fake data from the form

        for ($i=0; $i <= 30; $i++) {
            $notif = new Notification();
            $notif->setContent("salut l'ami");
            $notif->setType("Message");
            $notif->setReceiver($users[rand(0,$i)]);
            $manager->persist($notif);
        }
        $manager->flush();
    }
}