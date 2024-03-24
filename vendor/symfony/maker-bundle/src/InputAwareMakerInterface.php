<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\MakerBundle;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Lets the configureDependencies method access to the command's input.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface InputAwareMakerInterface extends MakerInterface
{
<<<<<<< HEAD
    public function configureDependencies(DependencyBuilder $dependencies, InputInterface $input = null);
=======
    public function configureDependencies(DependencyBuilder $dependencies, ?InputInterface $input = null);
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
}
