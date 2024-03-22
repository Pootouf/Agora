<?php

declare(strict_types=1);

namespace Doctrine\Persistence\Reflection;

use BackedEnum;
<<<<<<< HEAD
use ReflectionProperty;
=======
use ReflectionClass;
use ReflectionProperty;
use ReflectionType;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
use ReturnTypeWillChange;

use function array_map;
use function is_array;
<<<<<<< HEAD
=======
use function reset;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939

/**
 * PHP Enum Reflection Property - special override for backed enums.
 */
class EnumReflectionProperty extends ReflectionProperty
{
    /** @var ReflectionProperty */
    private $originalReflectionProperty;

    /** @var class-string<BackedEnum> */
    private $enumType;

    /** @param class-string<BackedEnum> $enumType */
    public function __construct(ReflectionProperty $originalReflectionProperty, string $enumType)
    {
        $this->originalReflectionProperty = $originalReflectionProperty;
        $this->enumType                   = $enumType;
    }

    /**
     * {@inheritDoc}
     *
<<<<<<< HEAD
=======
     * @psalm-external-mutation-free
     */
    public function getDeclaringClass(): ReflectionClass
    {
        return $this->originalReflectionProperty->getDeclaringClass();
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-external-mutation-free
     */
    public function getName(): string
    {
        return $this->originalReflectionProperty->getName();
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-external-mutation-free
     */
    public function getType(): ?ReflectionType
    {
        return $this->originalReflectionProperty->getType();
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes(?string $name = null, int $flags = 0): array
    {
        return $this->originalReflectionProperty->getAttributes($name, $flags);
    }

    /**
     * {@inheritDoc}
     *
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
     * Converts enum instance to its value.
     *
     * @param object|null $object
     *
     * @return int|string|int[]|string[]|null
     */
    #[ReturnTypeWillChange]
    public function getValue($object = null)
    {
        if ($object === null) {
            return null;
        }

        $enum = $this->originalReflectionProperty->getValue($object);

        if ($enum === null) {
            return null;
        }

        return $this->fromEnum($enum);
    }

    /**
     * Converts enum value to enum instance.
     *
     * @param object $object
     * @param mixed  $value
     */
    public function setValue($object, $value = null): void
    {
        if ($value !== null) {
            $value = $this->toEnum($value);
        }

        $this->originalReflectionProperty->setValue($object, $value);
    }

    /**
     * @param BackedEnum|BackedEnum[] $enum
     *
     * @return ($enum is BackedEnum ? (string|int) : (string[]|int[]))
     */
    private function fromEnum($enum)
    {
        if (is_array($enum)) {
            return array_map(static function (BackedEnum $enum) {
                return $enum->value;
            }, $enum);
        }

        return $enum->value;
    }

    /**
<<<<<<< HEAD
     * @param int|string|int[]|string[] $value
     *
     * @return ($value is int|string ? BackedEnum : BackedEnum[])
     */
    private function toEnum($value)
    {
        if (is_array($value)) {
=======
     * @param int|string|int[]|string[]|BackedEnum|BackedEnum[] $value
     *
     * @return ($value is int|string|BackedEnum ? BackedEnum : BackedEnum[])
     */
    private function toEnum($value)
    {
        if ($value instanceof BackedEnum) {
            return $value;
        }

        if (is_array($value)) {
            $v = reset($value);
            if ($v instanceof BackedEnum) {
                return $value;
            }

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
            return array_map([$this->enumType, 'from'], $value);
        }

        return $this->enumType::from($value);
    }
<<<<<<< HEAD
=======

    /**
     * {@inheritDoc}
     *
     * @psalm-external-mutation-free
     */
    public function getModifiers(): int
    {
        return $this->originalReflectionProperty->getModifiers();
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-external-mutation-free
     */
    public function getDocComment(): string|false
    {
        return $this->originalReflectionProperty->getDocComment();
    }
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
}
