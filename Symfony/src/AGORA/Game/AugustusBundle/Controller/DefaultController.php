<?php

namespace AGORA\Game\AugustusBundle\Controller;

use AGORA\Game\AugustusBundle\Service\AugustusService;

use AGORA\Game\AugustusBundle\Entity\AugustusGame;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AugustusBundle:Default:index.html.twig');
    }
}
