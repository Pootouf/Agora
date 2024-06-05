<?php

namespace App\Entity\Game\DTO;

interface GameParameters
{
    // NOTIFICATION TYPE
    public const string ALERT_NOTIFICATION_TYPE = "alert";
    public const string INFO_NOTIFICATION_TYPE = "info";
    public const string RINGING_NOTIFICATION_TYPE = "ringing";
    public const string VALIDATION_NOTIFICATION_TYPE = "validation";

    // NOTIFICATION COLOR
    public const string NOTIFICATION_COLOR_RED = "red";
    public const string NOTIFICATION_COLOR_GREEN = "green";
    public const string NOTIFICATION_COLOR_BLUE = "blue";
    public const string NOTIFICATION_COLOR_YELLOW = "yellow";
}
