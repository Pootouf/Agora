<?php

namespace Container6kZEf9m;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getEmailVerifierService extends App_KernelTestDebugContainer
{
    /**
     * Gets the public 'App\Security\Platform\EmailVerifier' shared autowired service.
     *
     * @return \App\Security\Platform\EmailVerifier
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/src/Security/Platform/EmailVerifier.php';

        return $container->services['App\\Security\\Platform\\EmailVerifier'] = new \App\Security\Platform\EmailVerifier(($container->privates['symfonycasts.verify_email.helper'] ?? $container->load('getSymfonycasts_VerifyEmail_HelperService')), ($container->privates['mailer.mailer'] ?? $container->load('getMailer_MailerService')), ($container->services['doctrine.orm.default_entity_manager'] ?? self::getDoctrine_Orm_DefaultEntityManagerService($container)));
    }
}