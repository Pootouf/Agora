<?php

declare(strict_types=1);

namespace Doctrine\ORM\Mapping;

<<<<<<< HEAD
=======
use BackedEnum;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use ReflectionEnum;
use ReflectionNamedType;
use ReflectionProperty;

use function array_merge;
use function assert;
use function enum_exists;
<<<<<<< HEAD
=======
use function is_a;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939

/** @psalm-type ScalarName = 'array'|'bool'|'float'|'int'|'string' */
final class DefaultTypedFieldMapper implements TypedFieldMapper
{
    /** @var array<class-string|ScalarName, class-string<Type>|string> $typedFieldMappings */
    private array $typedFieldMappings;

    private const DEFAULT_TYPED_FIELD_MAPPINGS = [
        DateInterval::class => Types::DATEINTERVAL,
        DateTime::class => Types::DATETIME_MUTABLE,
        DateTimeImmutable::class => Types::DATETIME_IMMUTABLE,
        'array' => Types::JSON,
        'bool' => Types::BOOLEAN,
        'float' => Types::FLOAT,
        'int' => Types::INTEGER,
        'string' => Types::STRING,
    ];

    /** @param array<class-string|ScalarName, class-string<Type>|string> $typedFieldMappings */
    public function __construct(array $typedFieldMappings = [])
    {
        $this->typedFieldMappings = array_merge(self::DEFAULT_TYPED_FIELD_MAPPINGS, $typedFieldMappings);
    }

    /**
     * {@inheritDoc}
     */
    public function validateAndComplete(array $mapping, ReflectionProperty $field): array
    {
        $type = $field->getType();

        if (
            ! isset($mapping['type'])
            && ($type instanceof ReflectionNamedType)
        ) {
            if (! $type->isBuiltin() && enum_exists($type->getName())) {
<<<<<<< HEAD
                $mapping['enumType'] = $type->getName();

=======
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
                $reflection = new ReflectionEnum($type->getName());
                if (! $reflection->isBacked()) {
                    throw MappingException::backedEnumTypeRequired(
                        $field->class,
                        $mapping['fieldName'],
<<<<<<< HEAD
                        $mapping['enumType'],
                    );
                }

                $type = $reflection->getBackingType();
=======
                        $type->getName(),
                    );
                }

                assert(is_a($type->getName(), BackedEnum::class, true));
                $mapping['enumType'] = $type->getName();
                $type                = $reflection->getBackingType();
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939

                assert($type instanceof ReflectionNamedType);
            }

            if (isset($this->typedFieldMappings[$type->getName()])) {
                $mapping['type'] = $this->typedFieldMappings[$type->getName()];
            }
        }

        return $mapping;
    }
}
