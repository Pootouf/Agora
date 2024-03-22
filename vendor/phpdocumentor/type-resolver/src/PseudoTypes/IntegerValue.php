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
use phpDocumentor\Reflection\Types\Integer;

/** @psalm-immutable */
final class IntegerValue implements PseudoType
{
<<<<<<< HEAD
    private int $value;
=======
    /** @var int */
    private $value;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function underlyingType(): Type
    {
        return new Integer();
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
