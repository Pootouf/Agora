<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Repository\Game\MessageRepository;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use App\Repository\Game\Myrmes\PheromonMYRRepository;
use App\Repository\Game\Myrmes\PheromonTileMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\PreyMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;


class WorkshopMYRService
{

    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly MYRService $MYRService,
                                private readonly PheromonTileMYRRepository $pheromoneTileMYRRepository,
                                private readonly PreyMYRRepository $preyMYRRepository,
                                private readonly TileMYRRepository $tileMYRRepository,
                                private readonly PheromonMYRRepository $pheromonMYRRepository,
                                private readonly ResourceMYRRepository $resourceMYRRepository,
                                private readonly PlayerResourceMYRRepository $playerResourceMYRRepository,
                                private readonly NurseMYRRepository $nurseMYRRepository,
                                private readonly AnthillHoleMYRRepository $anthillHoleMYRRepository)
    {}

    /**
     * getAvailableAnthillHolesPositions : returns a list of possible new anthill holes positions
     * @param PlayerMYR $player
     * @return ArrayCollection<Int, TileMYR>
     */
    public function getAvailableAnthillHolesPositions(PlayerMYR $player) : ArrayCollection
    {
        $pheromones = $player->getPheromonMYRs();
        $result = new ArrayCollection();
        foreach ($pheromones as $pheromone) {
            $pheromoneTiles = $pheromone->getPheromonTiles();
            foreach ($pheromoneTiles as $pheromoneTile) {
                $tile = $pheromoneTile->getTile();
                $adjacentTiles = $this->getAdjacentTiles($tile);
                foreach ($adjacentTiles as $adjacentTile) {
                    if ($adjacentTile != null && $this->isValidPosition($player, $adjacentTile)) {
                        $result->add($adjacentTile);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * canSetPhaseToWorkshop : before entering workshop phase, check if the player has nurses in workshop area
     * @param PlayerMYR $playerMYR
     * @return bool
     */
    public function canSetPhaseToWorkshop(PlayerMYR $playerMYR): bool
    {
        $personalBoard = $playerMYR->getPersonalBoardMYR();
        foreach($personalBoard->getNurses() as $nurse) {
            if($nurse->getArea() == MyrmesParameters::WORKSHOP_AREA) {
                return true;
            }
        }
        return false;
    }

    /**
     * Manage resources and purchase about position of nurse
     *
     * @param PlayerMYR    $player
     * @param int          $workshop
     * @param TileMYR|null $tile
     * @return void
     * @throws Exception
     */
    public function manageWorkshop(PlayerMYR $player, int $workshop, TileMYR $tile = null): void
    {
        if (!$this->canChooseThisBonus($player, $workshop)) {
            throw new Exception("player can not choose this bonus");
        }
        $nurses = $this->MYRService->getNursesAtPosition($player, $workshop);
        $nursesCount = $nurses->count()
        ;
        if ($nursesCount != 1) {
            throw new Exception("player can not choose this bonus");
        }

        switch ($workshop) {
            case MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA:
                $this->manageAnthillHole($nursesCount, $player, $tile);
                break;
            case MyrmesParameters::WORKSHOP_LEVEL_AREA:
                $this->manageLevel($nursesCount, $player);
                break;
            case MyrmesParameters::WORKSHOP_NURSE_AREA:
                if ($this->canBuyNurse($player)) {
                    $this->manageNurse($nursesCount, $player);
                }
                break;
            case MyrmesParameters::WORKSHOP_GOAL_AREA:
                break;
        }
        $this->entityManager->flush();
    }

    /**
     * manageEndOfWorkshop : end the workshop round
     * @param GameMYR $gameMYR
     * @return void
     * @throws Exception
     */
    public function manageEndOfWorkshop(GameMYR $gameMYR): void
    {
        if(!$this->MYRService->canManageEndOfPhase($gameMYR, MyrmesParameters::PHASE_WORKSHOP)) {
            throw new Exception("All members have not played yet");
        }
        $this->MYRService->manageEndOfRound($gameMYR);
    }

    /**
     * canChooseThisBonus : checks if the player can choose the selected bonus in the workshopArea
     * @param PlayerMYR $player
     * @param int       $workshopArea
     * @return bool
     */
    private function canChooseThisBonus(PlayerMYR $player, int $workshopArea) : bool
    {
        if ( $workshopArea < MyrmesParameters::WORKSHOP_GOAL_AREA
            || $workshopArea > MyrmesParameters::WORKSHOP_NURSE_AREA)
        {
            return false;
        }

        if ($player->getPhase() != MyrmesParameters::PHASE_WORKSHOP) {
            return false;
        }
        return $this->MYRService->getNursesAtPosition($player, $workshopArea)->count() > 0;
    }

    /**
     * playerReachedAnthillHoleLimit : checks if the player reached his limit of anthill holes
     * @param PlayerMYR $player
     * @return bool
     */
    private function playerReachedAnthillHoleLimit(PlayerMYR $player) : bool
    {
        return $player->getAnthillHoleMYRs()->count() >= MyrmesParameters::MAX_ANTHILL_HOLE_NB;
    }

    /**
     * isValidPosition : checks if the tile chosen is valid for the player to place a new anthill hole
     * @param PlayerMYR $player
     * @param TileMYR   $tile
     * @return bool
     */
    private function isValidPosition(PlayerMYR $player, TileMYR $tile) : bool
    {
        if ($tile->getType() === MyrmesParameters::WATER_TILE_TYPE) {
            return false;
        }
        $mainBoard = $player->getGameMyr()->getMainBoardMYR();
        $pheromoneTile = $this->pheromoneTileMYRRepository->findOneBy(["mainBoard" => $mainBoard, "tile" => $tile]);
        if ($pheromoneTile != null) {
            return false;
        }
        $anthillHole = $this->anthillHoleMYRRepository->findOneBy(["mainBoardMYR" => $mainBoard, "tile" => $tile]);
        if ($anthillHole != null) {
            return false;
        }
        $prey = $this->preyMYRRepository->findOneBy(["mainBoardMYR" => $mainBoard, "tile" => $tile]);
        if ($prey != null) {
            return false;
        }
        /** @var ArrayCollection<Int, TileMYR> $adjacentTiles */
        $adjacentTiles = $this->getAdjacentTiles($tile);
        /** @var Array<PheromonMYR> $playerPheromones */
        $playerPheromones = $this->pheromonMYRRepository->findBy(["player" => $player]);
        foreach ($adjacentTiles as $adjacentTile) {
            foreach ($playerPheromones as $playerPheromone) {
                foreach ($playerPheromone->getPheromonTiles() as $pheromoneTile) {
                    if ($pheromoneTile->getTile() === $adjacentTile) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * getAdjacentTiles : return all adjacent tiles of a tile
     * @param TileMYR $tile
     * @return ArrayCollection<Int, TileMYR>
     */
    private function getAdjacentTiles(TileMYR $tile) : ArrayCollection
    {
        $coordX = $tile->getCoordX();
        $coordY = $tile->getCoordY();
        $result = new ArrayCollection();
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX, "coord_Y" => $coordY - 2]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX, "coord_Y" => $coordY + 2]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX - 1, "coord_Y" => $coordY - 1]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX - 1, "coord_Y" => $coordY + 1]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX + 1, "coord_Y" => $coordY - 1]);
        $result->add($newTile);
        $newTile = $this->tileMYRRepository->findOneBy(["coord_X" => $coordX + 1, "coord_Y" => $coordY + 2]);
        $result->add($newTile);
        return $result;
    }

    /**
     * Manage all change driven by add anthill hole
     *
     * @param int          $nursesCount
     * @param PlayerMYR    $player
     * @param TileMYR $tile
     * @return void
     * @throws Exception
     */
    private function manageAnthillHole(int $nursesCount, PlayerMYR $player, TileMYR $tile) : void
    {
        if ($this->playerReachedAnthillHoleLimit($player)) {
            throw new Exception("can't place more anthill holes");
        }
        if (!$this->isValidPosition($player, $tile)) {
            throw new Exception("can't place an anthill hole there");
        }
        if ($nursesCount == 1) {
            $anthillHole = new AnthillHoleMYR();
            $anthillHole->setTile($tile);
            $anthillHole->setPlayer($player);
            $anthillHole->setMainBoardMYR($player->getGameMyr()->getMainBoardMYR());
            $this->entityManager->persist($anthillHole);
            $player->addAnthillHoleMYR($anthillHole);
            $this->giveDirtToPlayer($player);
            $this->entityManager->persist($player);
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::WORKSHOP_ANTHILL_HOLE_AREA
            );
        }
    }

    /**
     * Check if player can increase anthill level
     * @param PlayerMYR $player
     * @param array $requestResources
     * @return bool
     */
    private function canIncreaseLevel(PlayerMYR $player, array $requestResources) : bool
    {
        $pBoard = $player->getPersonalBoardMYR();

        $haveResource = array_fill_keys(array_keys($requestResources), 0);

        foreach ($pBoard->getPlayerResourceMYRs() as $playerResource)
        {
            $resource = $playerResource->getResource();
            if (array_key_exists($resource->getDescription(), $requestResources))
            {
                $haveResource[$resource->getDescription()]
                    = $playerResource->getQuantity();
            }
        }

        foreach (array_keys($haveResource) as $resource)
        {
            if ($haveResource[$resource] < $requestResources[$resource]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get buy about start anthill level
     * @param int $level
     * @return array
     * @throws Exception
     */
    private function getBuyForLevel(int $level) : array
    {
        return match ($level) {
            0 => MyrmesParameters::BUY_RESOURCE_FOR_LEVEL_ONE,
            1 => MyrmesParameters::BUY_RESOURCE_FOR_LEVEL_TWO,
            2 => MyrmesParameters::BUY_RESOURCE_FOR_LEVEL_THREE,
            default => throw new Exception("Don't buy"),
        };
    }

    /**
     * Player spend resource necessary for add anthill level
     * @param PlayerMYR $player
     * @param string $resourceStr
     * @param int $count
     * @return void
     */
    private function spendResource(PlayerMYR $player, string $resourceStr, int $count) : void
    {
        $personalBoard = $player->getPersonalBoardMYR();

        foreach ($personalBoard->getPlayerResourceMYRs() as $playerResource)
        {
            $resource = $playerResource->getResource();
            if ($resource->getDescription() === $resourceStr)
            {
                $oldQuantity = $playerResource->getQuantity();
                $playerResource->setQuantity($oldQuantity - $count);
                return;
            }
        }
    }

    /**
     * Manage all changes driven by level increase
     * @param int $nursesCount
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    private function manageLevel(int $nursesCount, PlayerMYR $player) : void
    {
        if ($nursesCount == 1)
        {
            $personalBoard = $player->getPersonalBoardMYR();
            $buys = $this->getBuyForLevel($personalBoard->getAnthillLevel());

            if (!$this->canIncreaseLevel($player, $buys)) {
                throw new Exception("Can't increase anthill level");
            }

            foreach (array_keys($buys) as $resource)
            {
                $this->spendResource($player, $resource, $buys[$resource]);
            }

            $level = $personalBoard->getAnthillLevel();
            $personalBoard->setAnthillLevel($level + 1);
            $this->MYRService->manageNursesAfterBonusGive(
                $player, 1, MyrmesParameters::WORKSHOP_LEVEL_AREA
            );
        }
    }

    /**
     * Check if player can buy nurse
     * @param PlayerMYR $player
     * @return bool
     */
    private function canBuyNurse(PlayerMYR $player) : bool
    {
        $pBoard = $player->getPersonalBoardMYR();
        $resource = $this->getFoodResource($player);

        return $pBoard->getLarvaCount() >= 2
            && $resource != null
            && $resource->getQuantity() >= 2
            && $this->nurseMYRRepository->findOneBy(
                [
                    'available' => false,
                    'player' => $player
                ]
            ) != null;
    }

    private function getFoodResource(PlayerMYR $playerMYR) : ?PlayerResourceMYR
    {
        $pBoard = $playerMYR->getPersonalBoardMYR();

        $grass = $this->resourceMYRRepository->findOneBy(
            ["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]
        );
        return $this->playerResourceMYRRepository->findOneBy(
            [
                "resource" => $grass,
                "personalBoard" => $pBoard
            ]
        );
    }

    /**
     * Manage all change driven by add nurse
     * @param int $nursesCount
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    private function manageNurse(int $nursesCount, PlayerMYR $player) : void
    {
        $pBoard = $player->getPersonalBoardMYR();
        $pBoard->setLarvaCount($pBoard->getLarvaCount() - 2);

        $playerResource = $this->getFoodResource($player);
        $playerResource->setQuantity($playerResource->getQuantity() - 2);
        $this->entityManager->persist($playerResource);

        $nurse = $this->nurseMYRRepository->findOneBy(['available' => false]);
        $nurse->setAvailable(true);
        $nurse->setArea(MyrmesParameters::BASE_AREA);

        $this->entityManager->persist($nurse);
        $this->entityManager->persist($pBoard);

        $this->MYRService->manageNursesAfterBonusGive(
            $player, $nursesCount, MyrmesParameters::WORKSHOP_NURSE_AREA
        );
    }

    /**
     * giveDirtToPlayer : gives a resource of dirt to the player
     * @param PlayerMYR $player
     * @return void
     */
    private function giveDirtToPlayer(PlayerMYR $player) : void
    {
        $dirt = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_DIRT]);
        $playerDirt = $this->playerResourceMYRRepository->findOneBy(["resource" => $dirt]);
        if ($playerDirt != null) {
            $playerDirt->setQuantity($playerDirt->getQuantity() + 1);
            $this->entityManager->persist($playerDirt);
        } else {
            $playerDirt = new PlayerResourceMYR();
            $playerDirt->setResource($dirt);
            $playerDirt->setQuantity(1);
            $playerDirt->setPersonalBoard($player->getPersonalBoardMYR());
            $this->entityManager->persist($playerDirt);
            $player->getPersonalBoardMYR()->addPlayerResourceMYR($playerDirt);
            $this->entityManager->persist($player->getPersonalBoardMYR());
        }
        $this->entityManager->flush();
    }

}