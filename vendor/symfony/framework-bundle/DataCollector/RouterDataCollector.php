<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\DataCollector;

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\DataCollector\RouterDataCollector as BaseRouterDataCollector;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class RouterDataCollector extends BaseRouterDataCollector
{
    public function guessRoute(Request $request, mixed $controller): string
    {
        if (\is_array($controller)) {
            $controller = $controller[0];
        }

<<<<<<< HEAD
        if ($controller instanceof RedirectController) {
=======
        if ($controller instanceof RedirectController && $request->attributes->has('_route')) {
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
            return $request->attributes->get('_route');
        }

        return parent::guessRoute($request, $controller);
    }
}
