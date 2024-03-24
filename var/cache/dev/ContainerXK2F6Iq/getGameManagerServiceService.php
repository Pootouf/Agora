<?php

namespace ContainerXK2F6Iq;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getGameManagerServiceService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'App\Service\Game\GameManagerService' shared autowired service.
     *
     * @return \App\Service\Game\GameManagerService
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'Game'.\DIRECTORY_SEPARATOR.'GameManagerService.php';
        include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'Game'.\DIRECTORY_SEPARATOR.'AbstractGameManagerService.php';
        include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'Game'.\DIRECTORY_SEPARATOR.'SixQP'.\DIRECTORY_SEPARATOR.'SixQPGameManagerService.php';
        include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'Game'.\DIRECTORY_SEPARATOR.'Splendor'.\DIRECTORY_SEPARATOR.'SPLGameManagerService.php';

        $a = ($container->services['doctrine.orm.default_entity_manager'] ?? self::getDoctrine_Orm_DefaultEntityManagerService($container));
        $b = ($container->privates['App\\Service\\Game\\LogService'] ?? $container->load('getLogServiceService'));

        return $container->privates['App\\Service\\Game\\GameManagerService'] = new \App\Service\Game\GameManagerService(($container->privates['App\\Repository\\Game\\SixQP\\GameSixQPRepository'] ?? $container->load('getGameSixQPRepositoryService')), ($container->privates['App\\Repository\\Game\\Splendor\\GameSPLRepository'] ?? $container->load('getGameSPLRepositoryService')), new \App\Service\Game\SixQP\SixQPGameManagerService($a, ($container->privates['App\\Repository\\Game\\SixQP\\PlayerSixQPRepository'] ?? $container->load('getPlayerSixQPRepositoryService')), ($container->privates['App\\Service\\Game\\SixQP\\SixQPService'] ?? $container->load('getSixQPServiceService')), $b), new \App\Service\Game\Splendor\SPLGameManagerService($a, ($container->privates['App\\Service\\Game\\Splendor\\SPLService'] ?? $container->load('getSPLServiceService')), ($container->privates['App\\Repository\\Game\\Splendor\\PlayerSPLRepository'] ?? $container->load('getPlayerSPLRepositoryService')), $b));
    }
}