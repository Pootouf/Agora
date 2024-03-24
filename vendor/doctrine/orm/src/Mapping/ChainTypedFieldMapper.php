<?php

declare(strict_types=1);

namespace Doctrine\ORM\Mapping;

<<<<<<< HEAD
=======
use Doctrine\ORM\Internal\NoUnknownNamedArguments;
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
use ReflectionProperty;

final class ChainTypedFieldMapper implements TypedFieldMapper
{
<<<<<<< HEAD
    /**
     * @readonly
     * @var TypedFieldMapper[] $typedFieldMappers
     */
=======
    use NoUnknownNamedArguments;

    /** @var list<TypedFieldMapper> $typedFieldMappers */
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
    private readonly array $typedFieldMappers;

    public function __construct(TypedFieldMapper ...$typedFieldMappers)
    {
<<<<<<< HEAD
=======
        self::validateVariadicParameter($typedFieldMappers);

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
        $this->typedFieldMappers = $typedFieldMappers;
    }

    /**
     * {@inheritDoc}
     */
    public function validateAndComplete(array $mapping, ReflectionProperty $field): array
    {
        foreach ($this->typedFieldMappers as $typedFieldMapper) {
            $mapping = $typedFieldMapper->validateAndComplete($mapping, $field);
        }

        return $mapping;
    }
}
