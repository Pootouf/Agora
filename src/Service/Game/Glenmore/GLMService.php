<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use Doctrine\ORM\EntityManagerInterface;

class GLMService
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private TileGLMRepository $tileGLMRepository,
                                private PlayerGLMRepository $playerGLMRepository){}
    public function getActivePlayer(GameGLM $gameGLM): PlayerGLM
    {
        return $this->playerGLMRepository->findOneBy(["gameGLM" => $gameGLM->getId(),
            "turnOfPlayer" => true]);
    }

    public function endRoundOfPlayer(GameGLM $gameGLM, PlayerGLM $playerGLM, int $startPosition): void
    {
        $players = $gameGLM->getPlayers();
        foreach ($players as $player){
            $player->setTurnOfPlayer(false);
            $this->entityManager->persist($player);
        }
        $nextPlayer = null;
        $pointerPosition = $startPosition + 1;
        while ($nextPlayer == null && $startPosition != $pointerPosition){
            foreach ($players as $player){
                $playerPosition = $player->getPawn()->getPosition();
                if($playerPosition == $pointerPosition){
                    $nextPlayer = $player;
                }
            }
            $pointerPosition = ($pointerPosition +1) % GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;
        }
        if($startPosition == $pointerPosition){
            throw new \Exception("Next player unreachable");
        }
        $nextPlayer->setTurnOfPlayer(true);
        $this->entityManager->persist($nextPlayer);
        $this->entityManager->flush();
    }
}