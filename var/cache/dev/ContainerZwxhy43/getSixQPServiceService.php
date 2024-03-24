<?php

namespace ContainerZwxhy43;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSixQPServiceService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'App\Service\Game\SixQP\SixQPService' shared autowired service.
     *
     * @return \App\Service\Game\SixQP\SixQPService
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'Game'.\DIRECTORY_SEPARATOR.'SixQP'.\DIRECTORY_SEPARATOR.'SixQPService.php';

        return $container->privates['App\\Service\\Game\\SixQP\\SixQPService'] = new \App\Service\Game\SixQP\SixQPService(($container->services['doctrine.orm.default_entity_manager'] ?? self::getDoctrine_Orm_DefaultEntityManagerService($container)), ($container->privates['App\\Repository\\Game\\SixQP\\CardSixQPRepository'] ?? $container->load('getCardSixQPRepositoryService')), ($container->privates['App\\Repository\\Game\\SixQP\\ChosenCardSixQPRepository'] ?? $container->load('getChosenCardSixQPRepositoryService')), ($container->privates['App\\Repository\\Game\\SixQP\\PlayerSixQPRepository'] ?? $container->load('getPlayerSixQPRepositoryService')));
    }
}