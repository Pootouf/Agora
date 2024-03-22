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
 * Represents an expression type as described in the PSR-5, the PHPDoc Standard.
 *
 * @psalm-immutable
 */
final class Expression implements Type
{
<<<<<<< HEAD
    protected Type $valueType;
=======
    /** @var Type */
    protected $valueType;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939

    /**
     * Initializes this representation of an array with the given Type.
     */
    public function __construct(Type $valueType)
    {
        $this->valueType = $valueType;
    }

    /**
     * Returns the value for the keys of this array.
     */
    public function getValueType(): Type
    {
        return $this->valueType;
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        return '(' . $this->valueType . ')';
    }
}
