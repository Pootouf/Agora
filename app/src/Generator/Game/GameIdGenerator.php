<?php

namespace App\Generator\Game;

use App\Repository\Game\Glenmore\GameGLMRepository;
use App\Repository\Game\Myrmes\GameMYRRepository;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

class GameIdGenerator extends AbstractIdGenerator
{

    public function __construct(private readonly GameSixQPRepository $gameSixQPRepository,
                                private readonly GameSPLRepository $gameSPLRepository,
                                private readonly GameGLMRepository $gameGLMRepository,
                                private readonly GameMYRRepository $gameMYRRepository)
    {

    }

    public function generateId(EntityManagerInterface $em, $entity) : mixed
    {
        $game = $this->gameSixQPRepository->findOneBy([], ['id' => 'DESC']);
        $id = $game == null ? 0 : $game->getId();
        $lastId = $id;

        $game = $this->gameSPLRepository->findOneBy([], ['id' => 'DESC']);
        $id = $game == null ? 0 : $game->getId();
        $lastId = max($id, $lastId);

        $game = $this->gameGLMRepository->findOneBy([], ['id' => 'DESC']);
        $id = $game == null ? 0 : $game->getId();
        $lastId = max($id, $lastId);

        $game = $this->gameMYRRepository->findOneBy([], ['id' => 'DESC']);
        $id = $game == null ? 0 : $game->getId();
        $lastId = max($id, $lastId);

        return $lastId + 1;
    }

}