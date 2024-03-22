<?php

namespace Container6kZEf9m;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getGameTestControllerService extends App_KernelTestDebugContainer
{
    /**
     * Gets the public 'App\Controller\Game\GameTestController' shared autowired service.
     *
     * @return \App\Controller\Game\GameTestController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/Game/GameTestController.php';

        $container->services['App\\Controller\\Game\\GameTestController'] = $instance = new \App\Controller\Game\GameTestController(($container->services['App\\Service\\Game\\GameManagerService'] ?? $container->load('getGameManagerServiceService')));

        $instance->setContainer(($container->privates['.service_locator.O2p6Lk7'] ?? $container->load('get_ServiceLocator_O2p6Lk7Service'))->withContext('App\\Controller\\Game\\GameTestController', $container));

        return $instance;
    }
}
