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
        ]);
    }
}
