<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/xdebug' => [[['_route' => '_profiler_xdebug', '_controller' => 'web_profiler.controller.profiler::xdebugAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/game/sixqp/list' => [[['_route' => 'app_game_sixqp_list', '_controller' => 'App\\Controller\\Game\\GameTestController::listSixQPGames'], null, null, null, false, false, null]],
        '/game/sixqp/create' => [[['_route' => 'app_game_sixqp_create', '_controller' => 'App\\Controller\\Game\\GameTestController::createSixQPGame'], null, null, null, false, false, null]],
        '/game/register' => [[['_route' => 'app_game_register', '_controller' => 'App\\Controller\\Game\\RegistrationController::register'], null, null, null, false, false, null]],
        '/game/login' => [[['_route' => 'app_game_login', '_controller' => 'App\\Controller\\Game\\SecurityController::login'], null, null, null, false, false, null]],
        '/game/logout' => [[['_route' => 'app_game_logout', '_controller' => 'App\\Controller\\Game\\SecurityController::logout'], null, null, null, false, false, null]],
        '/description' => [[['_route' => 'app_desc', '_controller' => 'App\\Controller\\Platform\\DescriptionController::index'], null, null, null, false, false, null]],
        '/game' => [[['_route' => 'app_game', '_controller' => 'App\\Controller\\Platform\\GameController::index'], null, null, null, false, false, null]],
        '/' => [[['_route' => 'app_home', '_controller' => 'App\\Controller\\Platform\\HomeController::index'], null, null, null, false, false, null]],
        '/register' => [[['_route' => 'app_register', '_controller' => 'App\\Controller\\Platform\\RegistrationController::register'], null, null, null, false, false, null]],
        '/verify/email' => [[['_route' => 'app_verify_email', '_controller' => 'App\\Controller\\Platform\\RegistrationController::verifyUserEmail'], null, null, null, false, false, null]],
        '/login' => [[['_route' => 'app_login', '_controller' => 'App\\Controller\\Platform\\SecurityController::login'], null, null, null, false, false, null]],
        '/logout' => [[['_route' => 'app_logout', '_controller' => 'App\\Controller\\Platform\\SecurityController::logout'], null, null, null, false, false, null]],
        '/settings' => [[['_route' => 'app_settings', '_controller' => 'App\\Controller\\Platform\\SettingsController::index'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:38)'
                    .'|wdt/([^/]++)(*:57)'
                    .'|profiler/(?'
                        .'|font/([^/\\.]++)\\.woff2(*:98)'
                        .'|([^/]++)(?'
                            .'|/(?'
                                .'|search/results(*:134)'
                                .'|router(*:148)'
                                .'|exception(?'
                                    .'|(*:168)'
                                    .'|\\.css(*:181)'
                                .')'
                            .')'
                            .'|(*:191)'
                        .')'
                    .')'
                .')'
                .'|/game/(?'
                    .'|sixqp/(?'
                        .'|join/([^/]++)(*:233)'
                        .'|l(?'
                            .'|eave/([^/]++)(*:258)'
                            .'|aunch/([^/]++)(*:280)'
                        .')'
                        .'|delete/([^/]++)(*:304)'
                        .'|([^/]++)(*:320)'
                    .')'
                    .'|([^/]++)/sixqp/(?'
                        .'|select/([^/]++)(*:362)'
                        .'|place/row/([^/]++)(*:388)'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        38 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        57 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        98 => [[['_route' => '_profiler_font', '_controller' => 'web_profiler.controller.profiler::fontAction'], ['fontName'], null, null, false, false, null]],
        134 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        148 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        168 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        181 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        191 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        233 => [[['_route' => 'app_game_sixqp_join', '_controller' => 'App\\Controller\\Game\\GameTestController::joinSixQPGame'], ['id'], null, null, false, true, null]],
        258 => [[['_route' => 'app_game_sixqp_quit', '_controller' => 'App\\Controller\\Game\\GameTestController::quitSixQPGame'], ['id'], null, null, false, true, null]],
        280 => [[['_route' => 'app_game_sixqp_launch', '_controller' => 'App\\Controller\\Game\\GameTestController::launchSixQPGame'], ['id'], null, null, false, true, null]],
        304 => [[['_route' => 'app_game_sixqp_delete', '_controller' => 'App\\Controller\\Game\\GameTestController::deleteSixQPGame'], ['id'], null, null, false, true, null]],
        320 => [[['_route' => 'app_game_show_sixqp', '_controller' => 'App\\Controller\\Game\\SixQPController::showGame'], ['id'], null, null, false, true, null]],
        362 => [[['_route' => 'app_game_sixqp_select', '_controller' => 'App\\Controller\\Game\\SixQPController::selectCard'], ['idGame', 'idCard'], null, null, false, true, null]],
        388 => [
            [['_route' => 'app_game_sixqp_placecardonrow', '_controller' => 'App\\Controller\\Game\\SixQPController::placeCardOnRow'], ['idGame', 'idRow'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
