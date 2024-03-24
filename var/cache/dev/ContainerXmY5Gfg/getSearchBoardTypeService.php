<?php

namespace ContainerXmY5Gfg;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSearchBoardTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'App\Form\Platform\SearchBoardType' shared autowired service.
     *
     * @return \App\Form\Platform\SearchBoardType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/src/Form/Platform/SearchBoardType.php';

        return $container->privates['App\\Form\\Platform\\SearchBoardType'] = new \App\Form\Platform\SearchBoardType();
    }
}