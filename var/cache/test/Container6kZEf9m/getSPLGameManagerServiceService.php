<?php

namespace Container6kZEf9m;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSPLGameManagerServiceService extends App_KernelTestDebugContainer
{
    /**
     * Gets the public 'App\Service\Game\Splendor\SPLGameManagerService' shared autowired service.
     *
     * @return \App\Service\Game\Splendor\SPLGameManagerService
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/src/Service/Game/AbstractGameManagerService.php';
        include_once \dirname(__DIR__, 4).'/src/Service/Game/Splendor/SPLGameManagerService.php';

        return $container->services['App\\Service\\Game\\Splendor\\SPLGameManagerService'] = new \App\Service\Game\Splendor\SPLGameManagerService(($container->services['doctrine.orm.default_entity_manager'] ?? self::getDoctrine_Orm_DefaultEntityManagerService($container)), ($container->services['App\\Service\\Game\\Splendor\\SPLService'] ?? $container->load('getSPLServiceService')), ($container->services['App\\Repository\\Game\\Splendor\\PlayerSPLRepository'] ?? $container->load('getPlayerSPLRepositoryService')), ($container->services['App\\Service\\Game\\LogService'] ?? $container->load('getLogServiceService')));
    }
}