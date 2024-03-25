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
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\PheromonMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\PreyMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

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
     * canPlace : checks if a player can place a pheromone or a special tile on the selected tile
     *
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return bool
     * @throws Exception
     */
    public function canPlace(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : bool
    {
        $tileType = $pheromone->getType();
        switch ($tileType->getType()) {
            case MyrmesParameters::$PHEROMONE_TYPE_ZERO:
                return $this->canPlaceTypeZero($player, $tile, $pheromone);
            case MyrmesParameters::$PHEROMONE_TYPE_ONE:
                return $this->canPlaceTypeOne($player, $tile, $pheromone);
            case MyrmesParameters::$PHEROMONE_TYPE_TWO:
                return $this->canPlaceTypeTwo($player, $tile, $pheromone);
            case MyrmesParameters::$PHEROMONE_TYPE_THREE:
                return $this->canPlaceTypeThree($player, $tile, $pheromone);
            case MyrmesParameters::$PHEROMONE_TYPE_FOUR:
                return $this->canPlaceTypeFour($player, $tile, $pheromone);
            case MyrmesParameters::$PHEROMONE_TYPE_FIVE:
                return $this->canPlaceTypeFive($player, $tile, $pheromone);
            case MyrmesParameters::$PHEROMONE_TYPE_SIX:
                return $this->canPlaceTypeSix($player, $tile, $pheromone);
            case MyrmesParameters::$SPECIAL_TILE_TYPE_FARM:
                return $this->canPlaceTypeTwo($player, $tile, $pheromone)
                    && $this->canPlaceFarm($player);
            case MyrmesParameters::$SPECIAL_TILE_TYPE_QUARRY:
                return $this->canPlaceTypeTwo($player, $tile, $pheromone)
                 && $this->canPlaceQuarry($player);
            case MyrmesParameters::$SPECIAL_TILE_TYPE_SUBANTHILL:
                return $this->canPlaceTypeThree($player, $tile, $pheromone)
                && $this->canPlaceSubanthill($player);
            default:
                throw new Exception("unknown tile type");

        }

    }

    /**
     * isPositionAvailable : checks if any player can place something on the tile in parameters
     * @param GameMYR $gameMYR
     * @param TileMYR $tileMYR
     * @return bool
     */
    private function isPositionAvailable(GameMYR $gameMYR, TileMYR $tileMYR) : bool
    {
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
     * canPlaceTypeZero : checks if player can place a pheromone onto the tile
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return bool
     */
    private function canPlaceTypeZero(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : bool
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            return false;
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                return $this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile);
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                return $this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile);
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                return $this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile);
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                return $this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile);
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                return $this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile);
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                return $this->isPositionAvailable($game, $newTile) && !$this->containsPrey($game, $newTile);
            default:
                return false;
        }
    }

    /**
     * canPlaceTypeOne : checks if player can place a pheromone onto the tile
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return bool
     */
    private function canPlaceTypeOne(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : bool
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            return false;
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            default:
                return false;
        }
    }

    /**
     * canPlaceTypeTwo : checks if player can place a pheromone onto the tile
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return bool
     */
    private function canPlaceTypeTwo(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : bool
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            return false;
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            default:
                return false;
        }
    }

    /**
     * canPlaceTypeThree : checks if player can place a pheromone onto the tile
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return bool
     */
    private function canPlaceTypeThree(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : bool
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            return false;
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 3]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 3]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 3]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 3]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            default:
                return false;
        }
    }


    /**
     * canPlaceTypeFour : checks if player can place a pheromone onto the tile
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return bool
     */
    private function canPlaceTypeFour(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : bool
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            return false;
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY + 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY - 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 4]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY - 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY + 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 4]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 6:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY + 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 7:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY - 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 8:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 4]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 9:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY - 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 10:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY + 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 11:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 4]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            default:
                return false;
        }
    }

    /**
     * canPlaceTypeFive : checks if player can place a pheromone onto the tile
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return bool
     */
    private function canPlaceTypeFive(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : bool
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            return false;
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 3]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 3]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 3]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 3]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            default:
                return false;
        }
    }

    /**
     * canPlaceTypeSix : checks if player can place a pheromone onto the tile
     * @param PlayerMYR   $player
     * @param TileMYR     $tile
     * @param PheromonMYR $pheromone
     * @return bool
     */
    private function canPlaceTypeSix(PlayerMYR $player, TileMYR $tile, PheromonMYR $pheromone) : bool
    {
        $tileType = $pheromone->getType();
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $game = $player->getGameMyr();
        if (!$this->isPositionAvailable($game, $tile) || $this->containsPrey($game, $tile)) {
            return false;
        }
        switch ($tileType->getOrientation()) {
            case 0:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY + 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY - 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 1:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY - 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY - 3]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 4]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 2:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY - 4]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 3]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY - 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 3:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY - 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY + 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 4:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 2, "coord_Y" => $coordY + 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 4]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX - 1, "coord_Y" => $coordY + 3]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            case 5:
                $newTile = $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 2]
                );
                $result = $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX, "coord_Y" => $coordY + 4]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 3]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 2, "coord_Y" => $coordY + 2]
                );
                $result = $result && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
                $newTile =  $this->tileMYRRepository->findOneBy(
                    ["coord_X" => $coordX + 1, "coord_Y" => $coordY + 1]
                );
                return $result
                    && $this->isPositionAvailable($game, $newTile)
                    && !$this->containsPrey($game, $newTile);
            default:
                return false;
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
     * canPlaceFarm : checks if the player can place a farm
     * @param PlayerMYR   $player
     * @return bool
     */
    private function canPlaceFarm(PlayerMYR $player) : bool
    {
        $personalBoard = $player->getPersonalBoardMYR();
        $grass = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::$RESOURCE_TYPE_GRASS]);
        return $this->playerResourceMYRRepository->findOneBy(
            ["personalBoard" => $personalBoard, "resource" => $grass]
        ) != null;
    }

    /**
     * canPlaceQuarry : checks if the player can place a quarry
     * @param PlayerMYR   $player
     * @return bool
     */
    private function canPlaceQuarry(PlayerMYR $player) : bool
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
     * canPlaceSubanthill : checks if the player can place a subanthill
     * @param PlayerMYR   $player
     * @return bool
     */
    private function canPlaceSubanthill(PlayerMYR $player) : bool
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
}