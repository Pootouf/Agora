<?php

namespace App\Service\Game;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class PublishService
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function publish(string $route, Response $data): Response
    {
        $update = new Update($route,
            html_entity_decode($data->getContent())
        );
        $this->hub->publish($update);

        return new Response('published!');
    }
}