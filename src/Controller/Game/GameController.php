<?php

namespace App\Controller\Game;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    public function publish(HubInterface $hub, string $route, Response $data): Response
    {
        $update = new Update(
            $this->generateUrl($route),
            html_entity_decode($data->getContent())
        );
        $hub->publish($update);

        return new Response('published!');
    }
}
