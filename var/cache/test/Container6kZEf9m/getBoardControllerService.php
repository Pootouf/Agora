<?php

namespace Container6kZEf9m;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getBoardControllerService extends App_KernelTestDebugContainer
{
    /**
     * Gets the public 'App\Controller\Platform\BoardController' shared autowired service.
     *
     * @return \App\Controller\Platform\BoardController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/Platform/BoardController.php';
        include_once \dirname(__DIR__, 4).'/src/Service/Platform/GameViewerService.php';

        $container->services['App\\Controller\\Platform\\BoardController'] = $instance = new \App\Controller\Platform\BoardController(($container->services['doctrine.orm.default_entity_manager'] ?? self::getDoctrine_Orm_DefaultEntityManagerService($container)), ($container->services['App\\Service\\Platform\\BoardManagerService'] ?? $container->load('getBoardManagerServiceService')), ($container->services['App\\Service\\Platform\\GameViewerService'] ??= new \App\Service\Platform\GameViewerService()), ($container->privates['security.helper'] ?? $container->load('getSecurity_HelperService')));

        $instance->setContainer(($container->privates['.service_locator.O2p6Lk7'] ?? $container->load('get_ServiceLocator_O2p6Lk7Service'))->withContext('App\\Controller\\Platform\\BoardController', $container));

        return $instance;
    }
}