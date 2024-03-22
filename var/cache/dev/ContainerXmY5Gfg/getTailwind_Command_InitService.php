<?php

namespace ContainerXmY5Gfg;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getTailwind_Command_InitService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'tailwind.command.init' shared service.
     *
     * @return \Symfonycasts\TailwindBundle\Command\TailwindInitCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/Command.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfonycasts/tailwind-bundle/src/Command/TailwindInitCommand.php';

        $container->privates['tailwind.command.init'] = $instance = new \Symfonycasts\TailwindBundle\Command\TailwindInitCommand(($container->privates['tailwind.builder'] ?? $container->load('getTailwind_BuilderService')));

        $instance->setName('tailwind:init');
        $instance->setDescription('Initializes Tailwind CSS for your project');

        return $instance;
    }
}
