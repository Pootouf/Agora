<?php

namespace AGORA\Game\AugustusBundle\Entity;


abstract class AugustusPower
{
    const ONELEGION = "oneLegion";
    const TWOLEGION = "twoLegion";
    const DOUBLESWORDISSHIELD = "doubleSwordIsShield";
    const SHIELDISCHARIOT = "shieldIsChariot";
    const CHARIOTISCATAPULT = "chariotIsCatapult";
    const CATAPULTISTEACHES = "catapultIsTeaches";
    const TEACHESISKNIFE = "teachesIsKnife";
    const ONEPOINTBYSHIELD = "onePointByShield";
    const ONEPOINTBYDOUBLESWORD = "onePointByDoubleSword";
    const TWOPOINTBYCHARIOT = "twoPointByChariot";
    const THREEPOINTBYCATAPULT = "threePointByCatapult";
    const THREEPOINTBYTEACHES = "threePointByTeaches";
    const FOURPOINTBYKNIFE = "fourPointByKnife";
    const TWOPOINTBYGREENCARD = "twoPointByGreenCard";
    const TWOPOINTBYSENATORCARD = "twoPointBySenatorCard";
    const FOURPOINTBYPINKCARD = "fourPointByPinkCard";
    const FIVEPOINTBYREDCARD = "fivePointByRedCard";
    const SIXPOINTBYORANGECARD = "sixPointByOrangeCard";
    const REMOVEONELEGION = "removeOneLegion";
    const REMOVETWOLEGION = "removeTwoLegion";
    const REMOVEALLLEGION = "removeAllLegion";
    const REMOVEONECARD = "removeOneCard";
    const COMPLETECARD = "completeCard";
    const ONELEGIONONANYTHING = "oneLegionOnAnything";
    const TWOLEGIONONANYTHING = "twoLegionOnAnything";
    const TWOLEGIONONDOUBLESWORD = "twoLegionOnDoubleSword";
    const TWOLEGIONONCHARIOT = "twoLegionOnChariot";
    const TWOLEGIONONCATAPULT = "twoLegionOnCatapult";
    const TWOLEGIONONTEACHES = "twoLegionOnTeaches";
    const TWOLEGIONONSHIELD = "twoLegionOnShield";
    const TWOLEGIONONKNIFE = "twoLegionOnKnife";
    const ONECARD = "oneCard";
    const MOVELEGION = "moveLegion";
    const NOPOWER = "noPower";

    /** @var array user friendly named power */
    protected static $powerName = [
        self::ONELEGION => "oneLegion",
        self::TWOLEGION => "twoLegion",
        self::DOUBLESWORDISSHIELD => "doubleSwordIsShield",
        self::SHIELDISCHARIOT => "shieldIsChariot",
        self::CHARIOTISCATAPULT => "chariotIsCatapult",
        self::CATAPULTISTEACHES => "catapultIsTeaches",
        self::TEACHESISKNIFE => "teachesIsKnife",
        self::ONEPOINTBYSHIELD => "onePointByShield",
        self::ONEPOINTBYDOUBLESWORD => "onePointByDoubleSword",
        self::TWOPOINTBYCHARIOT => "twoPointByChariot",
        self::THREEPOINTBYCATAPULT => "threePointByCatapult",
        self::THREEPOINTBYTEACHES => "threePointByTeaches",
        self::FOURPOINTBYKNIFE => "fourPointByKnife",
        self::TWOPOINTBYGREENCARD => "twoPointByGreenCard",
        self::TWOPOINTBYSENATORCARD => "twoPointBySenatorCard",
        self::FOURPOINTBYPINKCARD => "fourPointByPinkCard",
        self::FIVEPOINTBYREDCARD => "fivePointByRedCard",
        self::SIXPOINTBYORANGECARD => "sixPointByOrangeCard",
        self::REMOVEONELEGION => "removeOneLegion",
        self::REMOVETWOLEGION => "removeTwoLegion",
        self::REMOVEALLLEGION => "removeAllLegion",
        self::REMOVEONECARD => "removeOneCard",
        self::COMPLETECARD => "completeCard",
        self::ONELEGIONONANYTHING => "oneLegionOnAnything",
        self::TWOLEGIONONANYTHING => "twoLegionOnAnything",
        self::TWOLEGIONONDOUBLESWORD => "twoLegionOnDoubleSword",
        self::TWOLEGIONONCHARIOT => "twoLegionOnChariot",
        self::TWOLEGIONONCATAPULT => "twoLegionOnCatapult",
        self::TWOLEGIONONTEACHES => "twoLegionOnTeaches",
        self::TWOLEGIONONSHIELD => "twoLegionOnShield",
        self::TWOLEGIONONKNIFE => "twoLegionOnKnife",
        self::ONECARD => "oneCard",
        self::MOVELEGION => "moveLegion",
        self::NOPOWER => "noPower"
        
    ];

    /**
     * @param  string $powerShortName
     * @return string
     */
    public static function getPowerName($powerShortName)
    {
        if (!isset(static::$powerName[$powerShortName])) {
            return "Unknown power ($powerShortName)";
        }

        return static::$powerName[$powerShortName];
    }

    /**
     * @return array<string>
     */
    public static function getAvailablePowers()
    {
        return [
            self::ONELEGION,
            self::TWOLEGION,
            self::ONECARD,
            self::MOVELEGION,
            self::DOUBLESWORDISSHIELD,
            self::SHIELDISCHARIOT,
            self::CHARIOTISCATAPULT,
            self::CATAPULTISTEACHES,
            self::TEACHESISKNIFE,
            self::ONEPOINTBYSHIELD,
            self::ONEPOINTBYDOUBLESWORD,
            self::TWOPOINTBYCHARIOT,
            self::THREEPOINTBYCATAPULT,
            self::THREEPOINTBYTEACHES,
            self::FOURPOINTBYKNIFE,
            self::TWOPOINTBYGREENCARD,
            self::TWOPOINTBYSENATORCARD,
            self::FOURPOINTBYPINKCARD,
            self::FIVEPOINTBYREDCARD,
            self::SIXPOINTBYORANGECARD,
            self::REMOVEONELEGION,
            self::REMOVETWOLEGION,
            self::REMOVEALLLEGION,
            self::REMOVEONECARD,
            self::COMPLETECARD,
            self::ONELEGIONONANYTHING,
            self::TWOLEGIONONANYTHING,
            self::TWOLEGIONONDOUBLESWORD,
            self::TWOLEGIONONCHARIOT,
            self::TWOLEGIONONCATAPULT,
            self::TWOLEGIONONTEACHES,
            self::TWOLEGIONONSHIELD,
            self::ONECARD,
            self::MOVELEGION,
            self::NOPOWER
        ];
    }
}