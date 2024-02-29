<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use Doctrine\ORM\EntityManagerInterface;

class TileGLMService
{
    public function __construct(private EntityManagerInterface $entityManager,
        private PlayerGLMRepository $playerGLMRepository){}

    public function isChainBroken(MainBoardGLM $mainBoardGLM)
    {

    }


}