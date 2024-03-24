<?php

namespace ContainerZwxhy43;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_Nq3U0sbService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.nq3U0sb' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.nq3U0sb'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService ??= $container->getService(...), [
            'App\\Controller\\Game\\GameTestController::deleteSixQPGame' => ['privates', '.service_locator.ENzRjNN', 'get_ServiceLocator_ENzRjNNService', true],
            'App\\Controller\\Game\\GameTestController::joinSixQPGame' => ['privates', '.service_locator.ENzRjNN', 'get_ServiceLocator_ENzRjNNService', true],
            'App\\Controller\\Game\\GameTestController::launchSixQPGame' => ['privates', '.service_locator.ENzRjNN', 'get_ServiceLocator_ENzRjNNService', true],
            'App\\Controller\\Game\\GameTestController::listSixQPGames' => ['privates', '.service_locator.VffpW4I', 'get_ServiceLocator_VffpW4IService', true],
            'App\\Controller\\Game\\GameTestController::quitSixQPGame' => ['privates', '.service_locator.ENzRjNN', 'get_ServiceLocator_ENzRjNNService', true],
            'App\\Controller\\Game\\RegistrationController::register' => ['privates', '.service_locator.ywU5Sl_', 'get_ServiceLocator_YwU5SlService', true],
            'App\\Controller\\Game\\SecurityController::login' => ['privates', '.service_locator.rSTd.nA', 'get_ServiceLocator_RSTd_NAService', true],
            'App\\Controller\\Game\\SixQPController::placeCardOnRow' => ['privates', '.service_locator.0flv6qU', 'get_ServiceLocator_0flv6qUService', true],
            'App\\Controller\\Game\\SixQPController::selectCard' => ['privates', '.service_locator.ikIzvby', 'get_ServiceLocator_IkIzvbyService', true],
            'App\\Controller\\Game\\SixQPController::showGame' => ['privates', '.service_locator.jZSENec', 'get_ServiceLocator_JZSENecService', true],
            'App\\Controller\\Platform\\RegistrationController::register' => ['privates', '.service_locator.e_4zbH4', 'get_ServiceLocator_E4zbH4Service', true],
            'App\\Controller\\Platform\\RegistrationController::verifyUserEmail' => ['privates', '.service_locator.1Z9fEX7', 'get_ServiceLocator_1Z9fEX7Service', true],
            'App\\Controller\\Platform\\SecurityController::login' => ['privates', '.service_locator.rSTd.nA', 'get_ServiceLocator_RSTd_NAService', true],
            'App\\Kernel::loadRoutes' => ['privates', '.service_locator.y4_Zrx.', 'get_ServiceLocator_Y4Zrx_Service', true],
            'App\\Kernel::registerContainerConfiguration' => ['privates', '.service_locator.y4_Zrx.', 'get_ServiceLocator_Y4Zrx_Service', true],
            'kernel::loadRoutes' => ['privates', '.service_locator.y4_Zrx.', 'get_ServiceLocator_Y4Zrx_Service', true],
            'kernel::registerContainerConfiguration' => ['privates', '.service_locator.y4_Zrx.', 'get_ServiceLocator_Y4Zrx_Service', true],
            'App\\Controller\\Game\\GameTestController:deleteSixQPGame' => ['privates', '.service_locator.ENzRjNN', 'get_ServiceLocator_ENzRjNNService', true],
            'App\\Controller\\Game\\GameTestController:joinSixQPGame' => ['privates', '.service_locator.ENzRjNN', 'get_ServiceLocator_ENzRjNNService', true],
            'App\\Controller\\Game\\GameTestController:launchSixQPGame' => ['privates', '.service_locator.ENzRjNN', 'get_ServiceLocator_ENzRjNNService', true],
            'App\\Controller\\Game\\GameTestController:listSixQPGames' => ['privates', '.service_locator.VffpW4I', 'get_ServiceLocator_VffpW4IService', true],
            'App\\Controller\\Game\\GameTestController:quitSixQPGame' => ['privates', '.service_locator.ENzRjNN', 'get_ServiceLocator_ENzRjNNService', true],
            'App\\Controller\\Game\\RegistrationController:register' => ['privates', '.service_locator.ywU5Sl_', 'get_ServiceLocator_YwU5SlService', true],
            'App\\Controller\\Game\\SecurityController:login' => ['privates', '.service_locator.rSTd.nA', 'get_ServiceLocator_RSTd_NAService', true],
            'App\\Controller\\Game\\SixQPController:placeCardOnRow' => ['privates', '.service_locator.0flv6qU', 'get_ServiceLocator_0flv6qUService', true],
            'App\\Controller\\Game\\SixQPController:selectCard' => ['privates', '.service_locator.ikIzvby', 'get_ServiceLocator_IkIzvbyService', true],
            'App\\Controller\\Game\\SixQPController:showGame' => ['privates', '.service_locator.jZSENec', 'get_ServiceLocator_JZSENecService', true],
            'App\\Controller\\Platform\\RegistrationController:register' => ['privates', '.service_locator.e_4zbH4', 'get_ServiceLocator_E4zbH4Service', true],
            'App\\Controller\\Platform\\RegistrationController:verifyUserEmail' => ['privates', '.service_locator.1Z9fEX7', 'get_ServiceLocator_1Z9fEX7Service', true],
            'App\\Controller\\Platform\\SecurityController:login' => ['privates', '.service_locator.rSTd.nA', 'get_ServiceLocator_RSTd_NAService', true],
            'kernel:loadRoutes' => ['privates', '.service_locator.y4_Zrx.', 'get_ServiceLocator_Y4Zrx_Service', true],
            'kernel:registerContainerConfiguration' => ['privates', '.service_locator.y4_Zrx.', 'get_ServiceLocator_Y4Zrx_Service', true],
        ], [
            'App\\Controller\\Game\\GameTestController::deleteSixQPGame' => '?',
            'App\\Controller\\Game\\GameTestController::joinSixQPGame' => '?',
            'App\\Controller\\Game\\GameTestController::launchSixQPGame' => '?',
            'App\\Controller\\Game\\GameTestController::listSixQPGames' => '?',
            'App\\Controller\\Game\\GameTestController::quitSixQPGame' => '?',
            'App\\Controller\\Game\\RegistrationController::register' => '?',
            'App\\Controller\\Game\\SecurityController::login' => '?',
            'App\\Controller\\Game\\SixQPController::placeCardOnRow' => '?',
            'App\\Controller\\Game\\SixQPController::selectCard' => '?',
            'App\\Controller\\Game\\SixQPController::showGame' => '?',
            'App\\Controller\\Platform\\RegistrationController::register' => '?',
            'App\\Controller\\Platform\\RegistrationController::verifyUserEmail' => '?',
            'App\\Controller\\Platform\\SecurityController::login' => '?',
            'App\\Kernel::loadRoutes' => '?',
            'App\\Kernel::registerContainerConfiguration' => '?',
            'kernel::loadRoutes' => '?',
            'kernel::registerContainerConfiguration' => '?',
            'App\\Controller\\Game\\GameTestController:deleteSixQPGame' => '?',
            'App\\Controller\\Game\\GameTestController:joinSixQPGame' => '?',
            'App\\Controller\\Game\\GameTestController:launchSixQPGame' => '?',
            'App\\Controller\\Game\\GameTestController:listSixQPGames' => '?',
            'App\\Controller\\Game\\GameTestController:quitSixQPGame' => '?',
            'App\\Controller\\Game\\RegistrationController:register' => '?',
            'App\\Controller\\Game\\SecurityController:login' => '?',
            'App\\Controller\\Game\\SixQPController:placeCardOnRow' => '?',
            'App\\Controller\\Game\\SixQPController:selectCard' => '?',
            'App\\Controller\\Game\\SixQPController:showGame' => '?',
            'App\\Controller\\Platform\\RegistrationController:register' => '?',
            'App\\Controller\\Platform\\RegistrationController:verifyUserEmail' => '?',
            'App\\Controller\\Platform\\SecurityController:login' => '?',
            'kernel:loadRoutes' => '?',
            'kernel:registerContainerConfiguration' => '?',
        ]);
    }
}