<?php

namespace App\Entity\Game\SixQP;

use App\Repository\Game\SixQP\CardSixQPRepository;
use App\Entity\Game\DTO\Card;

use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: CardSixQPRepository::class)]
class CardSixQP extends Card
{
    /**
     * @throws Exception if invalid value
     */
    public function setValue(int $value): static
    {
        if ($value > 104 || $value < 1) {
            throw new Exception('Invalid value for card');
        }
        return parent::setValue($value);
    }
}
