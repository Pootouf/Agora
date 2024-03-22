<?php
/*
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @link      http://phpdoc.org
 *
 */

declare(strict_types=1);

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\String_;

use function sprintf;

/** @psalm-immutable */
class StringValue implements PseudoType
{
<<<<<<< HEAD
    private string $value;
=======
    /** @var string */
    private $value;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function underlyingType(): Type
    {
        return new String_();
    }

    public function __toString(): string
    {
        return sprintf('"%s"', $this->value);
    }
}
