<?php

namespace ContainerXmY5Gfg;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_P_V7DwoService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.P.v7Dwo' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.P.v7Dwo'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'boardRepository' => ['privates', 'App\\Repository\\Platform\\BoardRepository', 'getBoardRepositoryService', true],
        ], [
            'boardRepository' => 'App\\Repository\\Platform\\BoardRepository',
        ]);
    }
}