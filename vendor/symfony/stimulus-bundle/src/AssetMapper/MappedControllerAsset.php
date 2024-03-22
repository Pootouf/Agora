<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\UX\StimulusBundle\AssetMapper;

use Symfony\Component\AssetMapper\MappedAsset;

/**
<<<<<<< HEAD
 * @experimental
 *
 * @author Ryan Weaver <ryan@symfonycasts.com>
=======
 * @author Ryan Weaver <ryan@symfonycasts.com>
 *
 * @internal
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
 */
class MappedControllerAsset
{
    public function __construct(
        public MappedAsset $asset,
        public bool $isLazy,
        /**
         * @var MappedControllerAutoImport[]
         */
        public array $autoImports = [],
    ) {
    }
}
