<?php

namespace Container6kZEf9m;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_IkIzvbyService extends App_KernelTestDebugContainer
{
    /**
     * Gets the private '.service_locator.ikIzvby' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.ikIzvby'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'card' => ['privates', '.errored..service_locator.ikIzvby.App\\Entity\\Game\\SixQP\\CardSixQP', NULL, 'Cannot autowire service ".service_locator.ikIzvby": it needs an instance of "App\\Entity\\Game\\SixQP\\CardSixQP" but this type has been excluded in "config/services.yaml".'],
            'game' => ['privates', '.errored..service_locator.ikIzvby.App\\Entity\\Game\\SixQP\\GameSixQP', NULL, 'Cannot autowire service ".service_locator.ikIzvby": it needs an instance of "App\\Entity\\Game\\SixQP\\GameSixQP" but this type has been excluded in "config/services.yaml".'],
        ], [
            'card' => 'App\\Entity\\Game\\SixQP\\CardSixQP',
            'game' => 'App\\Entity\\Game\\SixQP\\GameSixQP',
        ]);
    }
}