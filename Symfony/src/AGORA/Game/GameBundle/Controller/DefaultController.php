<?php

namespace AGORA\Game\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AGORAGameGameBundle:Default:index.html.twig');
    }
}
