<?php

namespace App\Generator\Game;

use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

class PlayerIdGenerator extends AbstractIdGenerator
{

    public function __construct(private readonly PlayerSixQPRepository $playerSixQPRepository,
                                private readonly PlayerSPLRepository $playerSPLRepository,
                                private readonly PlayerGLMRepository $playerGLMRepository,
                                private readonly PlayerMYRRepository $playerMYRRepository)
    {}

    public function generateId(EntityManagerInterface $em, $entity) : mixed
    {
        $player = $this->playerSixQPRepository->findOneBy([], ['id' => 'DESC']);
        $id = $player == null ? 0 : $player->getId();
        $lastId = $id;

        $player = $this->playerSPLRepository->findOneBy([], ['id' => 'DESC']);
        $id = $player == null ? 0 : $player->getId();
        $lastId = max($id, $lastId);

        $player = $this->playerGLMRepository->findOneBy([], ['id' => 'DESC']);
        $id = $player == null ? 0 : $player->getId();
        $lastId = max($id, $lastId);

        $player = $this->playerMYRRepository->findOneBy([], ['id' => 'DESC']);
        $id = $player == null ? 0 : $player->getId();
        $lastId = max($id, $lastId);

        return $lastId + 1;
    }

}
