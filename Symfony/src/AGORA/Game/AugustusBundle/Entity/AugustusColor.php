<?php

namespace AGORA\Game\AugustusBundle\Entity;

abstract class AugustusColor
{
    const SENATOR  = "senator";
    const GREEN = "green";
    const PINK = "pink";
    const ORANGE  = "orange";
    const NOCOLOR  = "noColor";

    /** @var array user friendly named color */
    protected static $colorName = [
        self::SENATOR    => 'Senateur',
        self::GREEN => 'Vert',
        self::PINK => 'Rose',
        self::ORANGE  => 'Orange',
        self::NOCOLOR  => 'NoColor',
    ];

    /**
     * @param  string $colorShortName
     * @return string
     */
    public static function getColorName($colorShortName)
    {
        if (!isset(static::$colorName[$colorShortName])) {
            return "Unknown color ($colorShortName)";
        }

        return static::$colorName[$colorShortName];
    }

    /**
     * @return array<string>
     */
    public static function getAvailableColors()
    {
        return [
            self::SENATOR,
            self::GREEN,
            self::PINK,
            self::ORANGE,
            self::NOCOLOR
        ];
    }
}