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
 * Value Object representing a Callable type.
 *
 * @psalm-immutable
 */
final class Callable_ implements Type
{
<<<<<<< HEAD
    private ?Type $returnType;
    /** @var CallableParameter[] */
    private array $parameters;
=======
    /** @var Type|null */
    private $returnType;
    /** @var CallableParameter[] */
    private $parameters;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939

    /**
     * @param CallableParameter[] $parameters
     */
    public function __construct(array $parameters = [], ?Type $returnType = null)
    {
        $this->parameters = $parameters;
        $this->returnType = $returnType;
    }

    /** @return CallableParameter[] */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getReturnType(): ?Type
    {
        return $this->returnType;
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString(): string
    {
        return 'callable';
    }
}
