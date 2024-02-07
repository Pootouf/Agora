<?php
namespace App\Entity\Platform\DTO;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\String\AbstractStringEnum;

class BoardStatus extends AbstractStringEnum
{
    public const WAITING = 'waiting';
    public const IN_GAME = 'IN_GAME';
}