<?php

namespace Container6kZEf9m;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSixQPGameManagerServiceService extends App_KernelTestDebugContainer
{
    /**
     * Gets the public 'App\Service\Game\SixQP\SixQPGameManagerService' shared autowired service.
     *
     * @return \App\Service\Game\SixQP\SixQPGameManagerService
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/src/Service/Game/AbstractGameManagerService.php';
        include_once \dirname(__DIR__, 4).'/src/Service/Game/SixQP/SixQPGameManagerService.php';

        return $container->services['App\\Service\\Game\\SixQP\\SixQPGameManagerService'] = new \App\Service\Game\SixQP\SixQPGameManagerService(($container->services['doctrine.orm.default_entity_manager'] ?? self::getDoctrine_Orm_DefaultEntityManagerService($container)), ($container->services['App\\Repository\\Game\\SixQP\\PlayerSixQPRepository'] ?? $container->load('getPlayerSixQPRepositoryService')), ($container->services['App\\Service\\Game\\SixQP\\SixQPService'] ?? $container->load('getSixQPServiceService')), ($container->services['App\\Service\\Game\\LogService'] ?? $container->load('getLogServiceService')));
    }
}