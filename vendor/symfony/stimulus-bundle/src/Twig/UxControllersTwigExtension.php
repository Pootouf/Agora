<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\UX\StimulusBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
<<<<<<< HEAD
 * @experimental
=======
 * @internal
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
 *
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
final class UxControllersTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('ux_controller_link_tags', [UxControllersTwigRuntime::class, 'renderLinkTags'], ['is_safe' => ['html']]),
        ];
    }
}
