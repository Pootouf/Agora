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
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\ArrayKey;
use phpDocumentor\Reflection\Types\Mixed_;

use function implode;

/** @psalm-immutable */
class ArrayShape implements PseudoType
{
    /** @var ArrayShapeItem[] */
<<<<<<< HEAD
    private array $items;
=======
    private $items;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939

    public function __construct(ArrayShapeItem ...$items)
    {
        $this->items = $items;
    }

    /**
     * @return ArrayShapeItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function underlyingType(): Type
    {
        return new Array_(new Mixed_(), new ArrayKey());
    }

    public function __toString(): string
    {
        return 'array{' . implode(', ', $this->items) . '}';
    }
}
