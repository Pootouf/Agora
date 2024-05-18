<?php

namespace App\Entity\Game\DTO;

interface GameTranslation
{
    public const string GAME_NOT_ACCESSIBLE_MESSAGE = "Game cannot be accessed";
    public const string INVALID_PLAYER_MESSAGE = "Invalid player";
    public const string NOT_PLAYER_TURN = "Not player's turn";
    public const string GAME_STRING = "La partie ";
}
