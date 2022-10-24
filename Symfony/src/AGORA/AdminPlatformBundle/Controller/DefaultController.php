<?php

namespace AGORA\AdminPlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AGORAAdminPlatformBundle:Default:index.html.twig');
    }

}
