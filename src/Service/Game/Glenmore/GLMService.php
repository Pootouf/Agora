<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Repository\Game\Glenmore\DrawTilesGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class GLMService
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private TileGLMRepository $tileGLMRepository,
                                private DrawTilesGLMRepository $drawTilesGLMRepository,
                                private ResourceGLMRepository $resourceGLMRepository,
                                private PlayerGLMRepository $playerGLMRepository)
    {}

    public function getActivePlayer(GameGLM $gameGLM): PlayerGLM
    {
        return $this->playerGLMRepository->findOneBy(["gameGLM" => $gameGLM->getId(),
            "turnOfPlayer" => true]);
    }

    /**
     * getPlayerFromNameAndGame : return the player associated with a username and a game
     * @param GameGLM $game
     * @param string  $name
     * @return ?PlayerGLM
     */
    public function getPlayerFromNameAndGame(GameGLM $game, string $name): ?PlayerGLM
    {
        return $this->playerGLMRepository->findOneBy(['gameGLM' => $game->getId(), 'username' => $name]);
    }

    /**
     * getTilesFromGame : return the tiles from the board with the given game
     * @param GameGLM $game
     * @return Collection
     */
    public function getTilesFromGame(GameGLM $game): Collection
    {
        return $game->getMainBoard()->getBoardTiles();
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


    public function initializeNewGame(GameGLM $game) : void
    {
        $tilesLevelZero = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_ZERO]);
        $tilesLevelOne = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_ONE]);
        $tilesLevelTwo = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_TWO]);
        $tilesLevelThree = $this->tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_THREE]);

        $drawLevelZero = $this->drawTilesGLMRepository->findOneBy(
                ['mainBoardGLM' => $game->getMainBoard()->getId(),
                'level' => GlenmoreParameters::$TILE_LEVEL_ZERO]);
        $drawLevelOne = $this->drawTilesGLMRepository->findOneBy(
            ['mainBoardGLM' => $game->getMainBoard()->getId(),
                'level' => GlenmoreParameters::$TILE_LEVEL_ONE]);
        $drawLevelTwo = $this->drawTilesGLMRepository->findOneBy(
            ['mainBoardGLM' => $game->getMainBoard()->getId(),
                'level' => GlenmoreParameters::$TILE_LEVEL_TWO]);
        $drawLevelThree = $this->drawTilesGLMRepository->findOneBy(
            ['mainBoardGLM' => $game->getMainBoard()->getId(),
                'level' => GlenmoreParameters::$TILE_LEVEL_THREE]);

        foreach ($tilesLevelZero as $tile) $drawLevelZero->addTile($tile);
        foreach ($tilesLevelOne as $tile) $drawLevelOne->addTile($tile);
        foreach ($tilesLevelTwo as $tile) $drawLevelTwo->addTile($tile);
        foreach ($tilesLevelThree as $tile) $drawLevelThree->addTile($tile);
        $this->entityManager->persist($drawLevelZero);
        $this->entityManager->persist($drawLevelOne);
        $this->entityManager->persist($drawLevelTwo);
        $this->entityManager->persist($drawLevelThree);

        $startVillages = $this->tileGLMRepository->findBy(['name' => GlenmoreParameters::$TILE_NAME_START_VILLAGE]);
        $chiefs = $this->resourceGLMRepository->findBy(['type' => GlenmoreParameters::$VILLAGER_RESOURCE]);
        foreach ($game->getPlayers() as $player) {
            $tile = array_pop($startVillages);
            $chief = array_pop($chiefs);
            $playerTile = new PlayerTileGLM();
            $playerTile->setTile($tile);
            $playerTile->addResource($chief);
            $player->getPersonalBoard()->addPlayerTile($playerTile);
            $this->entityManager->persist($playerTile);

            $player->getPersonalBoard()->setMoney(GlenmoreParameters::$START_MONEY);
            $this->entityManager->persist($player->getPersonalBoard());
        }

        $this->entityManager->flush();
    }

}