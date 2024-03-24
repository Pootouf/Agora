<?php

namespace Container6kZEf9m;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSymfonycasts_VerifyEmail_UriSignerFactoryService extends App_KernelTestDebugContainer
{
    /**
     * Gets the private 'symfonycasts.verify_email.uri_signer_factory' shared service.
     *
     * @return \SymfonyCasts\Bundle\VerifyEmail\Factory\UriSignerFactory
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfonycasts/verify-email-bundle/src/Factory/UriSignerFactory.php';

        return $container->privates['symfonycasts.verify_email.uri_signer_factory'] = new \SymfonyCasts\Bundle\VerifyEmail\Factory\UriSignerFactory($container->getEnv('APP_SECRET'), 'signature');
    }
}