<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\PheromonMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\PreyMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;


/**
 * @codeCoverageIgnore
 */
class WorkerMYRService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly MYRService $MYRService,
                                private readonly AnthillHoleMYRRepository $anthillHoleMYRRepository,
                                private readonly PheromonMYRRepository $pheromonMYRRepository,
                                private readonly PreyMYRRepository $preyMYRRepository,
                                private readonly TileMYRRepository $tileMYRRepository,
                                private readonly PlayerResourceMYRRepository $playerResourceMYRRepository,
                                private readonly ResourceMYRRepository $resourceMYRRepository
    )
    {}

    /**
     * placeAnthillHole : if player can place an anthill hole, it place it on the tile
     * @param PlayerMYR $playerMYR
     * @param TileMYR $tileMYR
     * @return void
     * @throws Exception
     */
    public function placeAnthillHole(PlayerMYR $playerMYR, TileMYR $tileMYR) : void
    {
        if(!$this->isPositionAvailable($playerMYR->getGameMyr(), $tileMYR)) {
            throw new Exception("Can't place anthill hole here");
        }
        $anthillHole = new AnthillHoleMYR();
        $anthillHole->setPlayer($playerMYR);
        $anthillHole->setTile($tileMYR);
        $anthillHole->setMainBoardMYR($playerMYR->getGameMyr()->getMainBoardMYR());
        $playerMYR->addAnthillHoleMYR($anthillHole);
        $this->entityManager->persist($anthillHole);
        $this->entityManager->persist($playerMYR);
        $this->entityManager->flush();
    }

    /**
     * placePheromone : player tries to place a pheromone or a special tile on the selected tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return void
     * @throws Exception
     */
    public function placePheromone(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : void
    {
        $tileType = $pheromone->getType();
        if (!$this->isWorkerOnTile($player, $tile)) {
            throw new Exception("no garden worker on this tile");
        }
        $pheromoneCount = $this->getPheromoneCountOfType($player, $pheromone);
        if (!$this->canChoosePheromone($player, $pheromone, $pheromoneCount)) {
            throw new Exception("player can't place more pheromones of this type");
        }
        switch ($tileType->getType()) {
            case MyrmesParameters::$PHEROMONE_TYPE_ZERO:
                $this->placePheromoneTypeZero($player, $tile, $pheromone);
                break;
            case MyrmesParameters::$PHEROMONE_TYPE_ONE:
                $this->placePheromoneTypeOne($player, $tile, $pheromone);
                break;
            case MyrmesParameters::$PHEROMONE_TYPE_TWO:
                $this->placePheromoneTypeTwo($player, $tile, $pheromone);
                break;
            case MyrmesParameters::$PHEROMONE_TYPE_THREE:
                $this->placePheromoneTypeThree($player, $tile, $pheromone);
                break;
            case MyrmesParameters::$PHEROMONE_TYPE_FOUR:
                $this->placePheromoneTypeFour($player, $tile, $pheromone);
                break;
            case MyrmesParameters::$PHEROMONE_TYPE_FIVE:
                $this->placePheromoneTypeFive($player, $tile, $pheromone);
                break;
            case MyrmesParameters::$PHEROMONE_TYPE_SIX:
                $this->placePheromoneTypeSix($player, $tile, $pheromone);
                break;
            case MyrmesParameters::$SPECIAL_TILE_TYPE_FARM:
                if ($this->placePheromoneFarm($player)) {
                    $this->placePheromoneTypeTwo($player, $tile, $pheromone);
                    break;
                } else {
                    throw new Exception("cant place this tile");
                }
            case MyrmesParameters::$SPECIAL_TILE_TYPE_QUARRY:
                if ($this->placePheromoneQuarry($player)) {
                    $this->placePheromoneTypeTwo($player, $tile, $pheromone);
                    break;
                } else {
                    throw new Exception("cant place this tile");
                }
            case MyrmesParameters::$SPECIAL_TILE_TYPE_SUBANTHILL:
                if ($this->placePheromoneSubanthill($player)) {
                    $this->placePheromoneTypeThree($player, $tile, $pheromone);
                    break;
                } else {
                    throw new Exception("cant place this tile");
                }
            default:
                throw new Exception("unknown tile type");
        }
    }

    /**
     * isPositionAvailable : checks if any player can place something on the tile in parameters
     * @param GameMYR $gameMYR
     * @param ?TileMYR $tileMYR
     * @return bool
     */
    private function isPositionAvailable(GameMYR $gameMYR, ?TileMYR $tileMYR) : bool
    {
        if ($tileMYR == null) {
            return false;
        }
        if($tileMYR->getType() == MyrmesParameters::$WATER_TILE_TYPE) {
            return false;
        }
        $players = $gameMYR->getPlayers();
        foreach ($players as $player) {
            $anthill = $this->anthillHoleMYRRepository->findOneBy(["tile" => $tileMYR,
                "player" => $player]);
            if($anthill != null) {
                return false;
            }
            $playerPheromones = $this->pheromonMYRRepository->findBy(["player" => $player]);
            foreach ($playerPheromones as $playerPheromone) {
                $pheromoneTiles = $playerPheromone->getPheromonTiles();
                foreach ($pheromoneTiles as $pheromoneTile) {
                    if($pheromoneTile->getTile() === $tileMYR) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * placePheromoneTypeZero : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeZero(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : void
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        $tiles = new ArrayCollection();
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                if ($this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                if ($this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                if ($this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                if ($this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            default:
                throw new Exception("impossible case");
        }
    }

    /**
     * placePheromoneTypeOne : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeOne(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : void
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        $tiles = new ArrayCollection();
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $tiles->add($newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $tiles->add($newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * placePheromoneTypeTwo : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeTwo(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : void
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        $tiles = new ArrayCollection();
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * placePheromoneTypeThree : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeThree(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : void
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        $tiles = new ArrayCollection();
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 3]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 3]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 3]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 3]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            default:
                throw new Exception("can't place this tile");
        }
    }


    /**
     * placePheromoneTypeFour : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeFour(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : void
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        $tiles = new ArrayCollection();
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 4]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 4]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 6:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 7:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 8:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 4]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 9:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 10:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 11:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 4]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * placePheromoneTypeFive : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeFive(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : void
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        $tiles = new ArrayCollection();
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 3]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 3]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 3]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 3]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * placePheromoneTypeSix : checks if player can place a pheromone onto the tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return void
     * @throws Exception
     */
    private function placePheromoneTypeSix(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : void
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            throw new Exception("can't place this tile");
        }
        $tiles = new ArrayCollection();
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 3]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 4]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 4]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 3]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY - 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 4]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 3]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 4]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 3]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY + 2]
                );
                $tiles->add($newTile);
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $tiles->add($newTile);
                if ($result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile)) {
                    $this->createAndPlacePheromone($tiles, $player, $tileType);
                    break;
                } else {
                    throw new Exception("can't place this tile");
                }
            default:
                throw new Exception("can't place this tile");
        }
    }

    /**
     * containsPrey : checks if the tile contains a prey
     * @param GameMYR|null $game
     * @param TileMYR      $tile
     * @return bool
     */
    private function containsPrey(?GameMYR $game, TileMYR $tile) : bool
    {
        $mainBoard = $game->getMainBoardMYR();
        return $this->preyMYRRepository->findOneBy(
            ["mainBoardMYR" => $mainBoard->getId(), "tile" => $tile]
        ) != null;
    }

    /**
     * placePheromoneFarm : checks if the player can place a farm
     * @param PlayerMYR   $player
     * @return bool
     */
    private function placePheromoneFarm(PlayerMYR $player) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();
        $grass = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::$RESOURCE_TYPE_GRASS]);
        return $this->playerResourceMYRRepository->findOneBy(
            ["personalBoard" => $personalBoard, "resource" => $grass]
        ) != null;
    }

    /**
     * placePheromoneQuarry : checks if the player can place a quarry
     * @param PlayerMYR   $player
     * @return bool
     */
    private function placePheromoneQuarry(PlayerMYR $player) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();
        $grass = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::$RESOURCE_TYPE_GRASS]);
        $stone = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::$RESOURCE_TYPE_STONE]);
        return
            $this->playerResourceMYRRepository->findOneBy(
                ["personalBoard" => $personalBoard, "resource" => $grass]
            ) != null
            &&
            $this->playerResourceMYRRepository->findOneBy(
                ["personalBoard" => $personalBoard, "resource" => $stone]
            )!= null;
    }

    /**
     * placePheromoneSubanthill : checks if the player can place a subanthill
     * @param PlayerMYR   $player
     * @return bool
     */
    private function placePheromoneSubanthill(PlayerMYR $player) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();
        $grass = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::$RESOURCE_TYPE_GRASS]);
        $stone = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::$RESOURCE_TYPE_STONE]);
        $dirt = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::$RESOURCE_TYPE_DIRT]);
        return
            $this->playerResourceMYRRepository->findOneBy(
                ["personalBoard" => $personalBoard, "resource" => $grass]
            ) != null
            &&
            $this->playerResourceMYRRepository->findOneBy(
                ["personalBoard" => $personalBoard, "resource" => $stone]
            )!= null
            &&
            $this->playerResourceMYRRepository->findOneBy(
                ["personalBoard" => $personalBoard, "resource" => $dirt]
            )!= null;
    }

    /**
     * isWorkerOnTile : checks if the player owns a garden worker on the selected tile
     * @param PlayerMYR $player
     * @param TileMYR   $tile
     * @return bool
     */
    private function isWorkerOnTile(PlayerMYR $player, TileMYR $tile) : bool
    {
        $mainBoard = $player->getGameMyr()->getMainBoardMYR();
        $gardenWorkers = $mainBoard->getGardenWorkers();
        foreach ($gardenWorkers as $gardenWorker) {
            if ($gardenWorker->getTile() === $tile && $gardenWorker->getPlayer() === $player) {
                return true;
            }
        }
        return false;
    }

    /**
     * getPheromoneCountOfType : returns the amount of pheromones from a type a player already has placed
     * @param PlayerMYR   $player
     * @param PheromonMYR $pheromone
     * @return int
     */
    private function getPheromoneCountOfType(PlayerMYR $player, PheromonMYR $pheromone) : int
    {
        $pheromones = $player->getPheromonMYRs();
        $result = 0;
        $type = $pheromone->getType()->getType();
        foreach ($pheromones as $pheromone) {
            if ($pheromone->getType()->getType() == $type) {
                ++$result;
            }
        }
        return $result;
    }

    private function canChoosePheromone(PlayerMYR $player, PheromonMYR $pheromone, int $pheromoneCount) : bool
    {
        $anthillLevel = $player->getPersonalBoardMYR()->getAnthillLevel();
        $pheromoneSize = $pheromone->getPheromonTiles()->count();
        $allowedSize = $anthillLevel + 2;
        if ($player->getPersonalBoardMYR()->getBonus() === MyrmesParameters::$BONUS_PHEROMONE) {
            ++$allowedSize;
        }
        if ($pheromoneSize > $allowedSize) {
            return false;
        }
        if ($pheromone->getType()->getType() === MyrmesParameters::$SPECIAL_TILE_TYPE_SUBANTHILL) {
            return $anthillLevel == 3;
        }
        return MyrmesParameters::$PHEROMONE_TYPE_AMOUNT[$pheromone->getType()->getType()] >= $pheromoneCount;
    }

    /**
     * placeResourceOnTile : places the correct resource on the tile
     * @param PheromonTileMYR $tile
     * @return void
     */
    private function placeResourceOnTile(PheromonTileMYR $tile) : void
    {
        $grass = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::$RESOURCE_TYPE_GRASS]);
        $stone = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::$RESOURCE_TYPE_STONE]);
        $dirt = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::$RESOURCE_TYPE_DIRT]);
        switch ($tile->getTile()->getType()) {
            case MyrmesParameters::$DIRT_TILE_TYPE:
                $tile->setResource($dirt);
                break;
            case MyrmesParameters::$GRASS_TILE_TYPE:
                $tile->setResource($grass);
                break;
            case MyrmesParameters::$STONE_TILE_TYPE:
                $tile->setResource($stone);
                break;
        }
    }

    /**
     * createAndPlacePheromone : creates and places a pheromone on selected tile, owned by the player
     * @param ArrayCollection<Int, TileMYR>  $tiles
     * @param PlayerMYR                      $playerMYR
     * @param TileTypeMYR                    $tileTypeMYR
     * @return void
     */
    private function createAndPlacePheromone(ArrayCollection $tiles,
            PlayerMYR $playerMYR,
            TileTypeMYR $tileTypeMYR) : void
    {
        $pheromone = new PheromonMYR();
        $pheromone->setPlayer($playerMYR);
        $pheromone->setType($tileTypeMYR);
        $pheromone->setHarvested(false);
        foreach ($tiles as $tile) {
            $pheromoneTile = new PheromonTileMYR();
            $pheromoneTile->setTile($tile);
            $pheromoneTile->setPheromonMYR($pheromone);
            $this->placeResourceOnTile($pheromoneTile);
            $this->entityManager->persist($pheromoneTile);
            $pheromone->addPheromonTile($pheromoneTile);
        }
        $this->entityManager->persist($pheromone);
        $playerMYR->addPheromonMYR($pheromone);
        $type = $tileTypeMYR->getType();
        if ($type <= MyrmesParameters::$PHEROMONE_TYPE_SIX) {
            $points = MyrmesParameters::$PHEROMONE_TYPE_LEVEL[$tileTypeMYR->getType()];
        } else {
            $points = MyrmesParameters::$SPECIAL_TILES_TYPE_LEVEL[$tileTypeMYR->getType()];
        }
        if ($playerMYR->getPersonalBoardMYR()->getBonus() === MyrmesParameters::$BONUS_POINT) {
            ++$points;
        }
        $playerMYR->setScore($playerMYR->getScore() + $points);
        $this->entityManager->persist($playerMYR);
        $this->entityManager->flush();
    }
}