<?php

declare(strict_types=1);

namespace Doctrine\ORM\Mapping;

<<<<<<< HEAD
=======
use Doctrine\Deprecations\Deprecation;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
use InvalidArgumentException;

use function property_exists;

/** @internal */
trait ArrayAccessImplementation
{
    /** @param string $offset */
    public function offsetExists(mixed $offset): bool
    {
<<<<<<< HEAD
=======
        Deprecation::trigger(
            'doctrine/orm',
            'https://github.com/doctrine/orm/pull/11211',
            'Using ArrayAccess on %s is deprecated and will not be possible in Doctrine ORM 4.0. Use the corresponding property instead.',
            static::class,
        );

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        return isset($this->$offset);
    }

    /** @param string $offset */
    public function offsetGet(mixed $offset): mixed
    {
<<<<<<< HEAD
=======
        Deprecation::trigger(
            'doctrine/orm',
            'https://github.com/doctrine/orm/pull/11211',
            'Using ArrayAccess on %s is deprecated and will not be possible in Doctrine ORM 4.0. Use the corresponding property instead.',
            static::class,
        );

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        if (! property_exists($this, $offset)) {
            throw new InvalidArgumentException('Undefined property: ' . $offset);
        }

        return $this->$offset;
    }

    /** @param string $offset */
    public function offsetSet(mixed $offset, mixed $value): void
    {
<<<<<<< HEAD
=======
        Deprecation::trigger(
            'doctrine/orm',
            'https://github.com/doctrine/orm/pull/11211',
            'Using ArrayAccess on %s is deprecated and will not be possible in Doctrine ORM 4.0. Use the corresponding property instead.',
            static::class,
        );

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        $this->$offset = $value;
    }

    /** @param string $offset */
    public function offsetUnset(mixed $offset): void
    {
<<<<<<< HEAD
=======
        Deprecation::trigger(
            'doctrine/orm',
            'https://github.com/doctrine/orm/pull/11211',
            'Using ArrayAccess on %s is deprecated and will not be possible in Doctrine ORM 4.0. Use the corresponding property instead.',
            static::class,
        );

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        $this->$offset = null;
    }
}
