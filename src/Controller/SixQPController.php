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
        return $this->render('six_qp/index.html.twig', [
            'controller_name' => 'SixQPController',
            //chosenCards is a list of the cards path that need to be displayed in the chosen cards zone
            'chosenCards' => ['1.png', '2.png'],
            //playersNumber is an integer that indicates the number of actual players
            'playersNumber' => 5,
        ]);
    }
}
