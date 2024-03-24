<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Type;

/**
 * Value Object representing a Float.
 *
 * @psalm-immutable
 */
<<<<<<< HEAD
final class Float_ implements Type
=======
class Float_ implements Type
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
{
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        return 'float';
    }
}
