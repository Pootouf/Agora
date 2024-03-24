<?php

namespace Container6kZEf9m;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSplendorControllerService extends App_KernelTestDebugContainer
{
    /**
     * Gets the public 'App\Controller\Game\SplendorController' shared autowired service.
     *
     * @return \App\Controller\Game\SplendorController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/Game/SplendorController.php';

        $container->services['App\\Controller\\Game\\SplendorController'] = $instance = new \App\Controller\Game\SplendorController(($container->services['doctrine.orm.default_entity_manager'] ?? self::getDoctrine_Orm_DefaultEntityManagerService($container)), ($container->services['App\\Service\\Game\\Splendor\\TokenSPLService'] ?? $container->load('getTokenSPLServiceService')), ($container->services['App\\Service\\Game\\Splendor\\SPLService'] ?? $container->load('getSPLServiceService')), ($container->services['App\\Service\\Game\\LogService'] ?? $container->load('getLogServiceService')), ($container->services['App\\Service\\Game\\PublishService'] ?? $container->load('getPublishServiceService')));

        $instance->setContainer(($container->privates['.service_locator.O2p6Lk7'] ?? $container->load('get_ServiceLocator_O2p6Lk7Service'))->withContext('App\\Controller\\Game\\SplendorController', $container));

        return $instance;
    }
}