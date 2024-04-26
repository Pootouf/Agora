<?php

namespace App\Entity\Game\DTO;

interface GameParameters
{
    // NOTIFICATION TYPE
    const string ALERT_NOTIFICATION_TYPE = "alert";
    const string INFO_NOTIFICATION_TYPE = "info";
    const string RINGING_NOTIFICATION_TYPE = "ringing";
    const string VALIDATION_NOTIFICATION_TYPE = "validation";

    // NOTIFICATION COLOR
    const string NOTIFICATION_COLOR_RED = "red";
    const string NOTIFICATION_COLOR_GREEN = "green";
    const string NOTIFICATION_COLOR_BLUE = "blue";
    const string NOTIFICATION_COLOR_YELLOW = "yellow";
}
