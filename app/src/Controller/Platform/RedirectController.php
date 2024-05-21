<?php

namespace App\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    /**
     * Displays the homepage
     *
     * @return Response HTTP response: Homepage
     */
    #[Route('/redirect', name: 'app_redirect')]
    public function index(Security $security): Response
    {
        if($security->getUser()) {
            $user = $security->getUser();
            if (in_array("ROLE_ADMIN", $user->getRoles())){
                return $this->redirectToRoute("app_dashboard_allusers");
            }else{
                return $this->redirectToRoute("app_dashboard_user");
            }
        } else {
            return $this->redirectToRoute("app_home");
        }
    }

}
