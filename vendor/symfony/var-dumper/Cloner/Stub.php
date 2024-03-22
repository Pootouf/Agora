<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Cloner;

<<<<<<< HEAD
=======
use Symfony\Component\VarDumper\Cloner\Internal\NoDefault;

>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
/**
 * Represents the main properties of a PHP variable.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class Stub
{
    public const TYPE_REF = 1;
    public const TYPE_STRING = 2;
    public const TYPE_ARRAY = 3;
    public const TYPE_OBJECT = 4;
    public const TYPE_RESOURCE = 5;
    public const TYPE_SCALAR = 6;

    public const STRING_BINARY = 1;
    public const STRING_UTF8 = 2;

    public const ARRAY_ASSOC = 1;
    public const ARRAY_INDEXED = 2;

    public $type = self::TYPE_REF;
    public $class = '';
    public $value;
    public $cut = 0;
    public $handle = 0;
    public $refCount = 0;
    public $position = 0;
    public $attr = [];

    private static array $defaultProperties = [];

    /**
     * @internal
     */
    public function __sleep(): array
    {
        $properties = [];

        if (!isset(self::$defaultProperties[$c = static::class])) {
<<<<<<< HEAD
            self::$defaultProperties[$c] = get_class_vars($c);

            foreach ((new \ReflectionClass($c))->getStaticProperties() as $k => $v) {
                unset(self::$defaultProperties[$c][$k]);
=======
            $reflection = new \ReflectionClass($c);
            self::$defaultProperties[$c] = [];

            foreach ($reflection->getProperties() as $p) {
                if ($p->isStatic()) {
                    continue;
                }

                self::$defaultProperties[$c][$p->name] = $p->hasDefaultValue() ? $p->getDefaultValue() : ($p->hasType() ? NoDefault::NoDefault : null);
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
            }
        }

        foreach (self::$defaultProperties[$c] as $k => $v) {
<<<<<<< HEAD
            if ($this->$k !== $v) {
=======
            if (NoDefault::NoDefault === $v || $this->$k !== $v) {
>>>>>>> 2b5a5be8c33b93a2ea2500b9c6aa226dbc5bc939
                $properties[] = $k;
            }
        }

        return $properties;
    }
}
