<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SixQPController extends AbstractController
{
    #[Route('/six/q/p', name: 'app_six_q_p')]
    public function index(): Response
    {
        return $this->render('/Game/Six_qp/index.html.twig', [
            'controller_name' => 'SixQPController',
            //chosenCards is a list of the cards path that need to be displayed in the chosen cards zone
            'chosenCards' => ['1.png', '2.png', '3.png', '4.png', '5.png', '6.png', '7.png', '8.png', '9.png', '10.png'],
            //placeholderPlayerCards is a list of the cards path that need to be displayed in the personal board of the player
            'placeholderPlayerCards' => ['37.png','60.png','80.png','34.png','65.png','59.png','79.png','1.png','11.png','45.png'],
            //playersNumber is an integer that indicates the number of actual players
            'playersNumber' => 10,
            'users' => [['user1', 100], ['user2', 20], ['user2', 5], ['user3', 00000] ],
            'createdAt' => time(),
            //rowStartCard is an array that display the first card in each row
            'rowStartCard' => ['25.png', '29.png', '40.png', '55.png'],
            //rowOne is an array that display the cards in the first row
            'rowOne' => ['26.png', '27.png', '28.png'],
            //rowTwo is an array that display the cards in the second row
            'rowTwo' => ['30.png', '31.png', '32.png', '33.png'],
            //rowThree is an array that display the cards in the third row
            'rowThree' => ['47.png', '49.png'],
            //rowFour is an array that display the cards in the fourth row
            'rowFour' => [],
        ]);
    }
}
