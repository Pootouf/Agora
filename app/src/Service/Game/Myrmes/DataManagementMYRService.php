<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\DTO\Glenmore\BoardBoxGLM;
use App\Entity\Game\DTO\Glenmore\PersonalBoardBoxGLM;
use App\Entity\Game\DTO\Glenmore\PlayerResourcesDataGLM;
use App\Entity\Game\DTO\Myrmes\BoardBoxMYR;
use App\Entity\Game\DTO\Tile;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Repository\Game\Myrmes\GardenWorkerMYRRepository;
use App\Repository\Game\Myrmes\PheromonTileMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Service\Game\Glenmore\TileGLMService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;


/**
 * Enables to manipulate data and convert it into representable content
 */
class DataManagementMYRService
{

    public function __construct(private readonly TileMYRRepository $tileMYRRepository,
                                private readonly GardenWorkerMYRRepository $gardenWorkerMYRRepository,
                                private readonly PheromonTileMYRRepository $pheromonTileMYRRepository)
    {}


    /**
     * organizeMainBoardRows : return a collection of rows, a row is a collection of tiles or null.
     *  It represents each row of the main board from top to bottom.
     * @param GameMYR $game
     * @return Collection
     * @throws \Exception
     */
    public function organizeMainBoardRows(GameMYR $game) : Collection
    {
        $result = new ArrayCollection();

        // Change min and max y & x for adding extra empty tile (one empty line at the start of the board, one empty
        //  tile at each side of the line)
        $miny = $this->tileMYRRepository->
            findOneBy(['coord_Y' => 'ASC'])->getCoordY() - 1;
        $maxy = $this->tileMYRRepository->
            findOneBy(['coord_Y' => 'DESC'])->getCoordY() + 1;
        $minx = $this->tileMYRRepository->
            findOneBy(['coord_X' => 'ASC'])->getCoordX() - 1;
        $maxx = $this->tileMYRRepository->
            findOneBy(['coord_X' => 'DESC'])->getCoordX() + 1;

        //Sorting by x coord and then by y coord
        $tiles = $game->getMainBoardMYR()->getTiles()->toArray();
        usort($tiles, function (TileMYR $tile1, TileMYR $tile2){
            $value = $tile1->getCoordX() - $tile2->getCoordX();
            return $value == 0 ? $tile1->getCoordY() - $tile2->getCoordY() : $value;
        });

        $previousX = $minx;
        $currentLine = new ArrayCollection();
        $y = $miny;
        $x = $minx;
        foreach ($tiles as $tile) {
            // Move to the next line
            if ($previousX < $tile->getCoordX()) {
                // Fill the gaps up to y max coord
                while ($y <= $maxy) {
                    $currentLine->add($this->createBoardBox($game, null, $x, $y));
                }
                $result->add($currentLine);
                $x++;
                $currentLine = new ArrayCollection();

            }
            // Fill the gaps up to the next y coord
            while ($y < $tile->getCoordY()) {
                $currentLine->add($this->createBoardBox($game, null, $x, $y));
                $y++;
            }
            $currentLine->add($this->createBoardBox($game, $tile, $x, $y));
            $previousX = $tile->getCoordX();
            $y++;
        }
        // Complete the last added line until max y
        while ($y <= $maxy) {
            $currentLine->add($this->createBoardBox($game, null, $x, $y));
            $y++;
        }
        $result->add($currentLine);
        // Add an empty tile row at the end of the board
        while($x < $maxx) {
            $currentLine = new ArrayCollection();
            $y = $miny;
            $x++;
            while($y <= $maxy) {
                $currentLine->add($this->createBoardBox($game, null, $x, $y));
                $y++;
            }
            $result->add($currentLine);
        }
        return $result;
    }

    /**
     * createBoardBox : create a board box tile with tile, ant and pheromone
     * @throws \Exception
     */
    private function createBoardBox(GameMYR $game, ?TileMYR $tile, int $x, int $y) : BoardBoxMYR
    {
        $ant = null;
        $pheromoneTile = null;
        if ($tile != null) {
            $ant = $this->gardenWorkerMYRRepository->findOneBy(
                [
                    'mainBoardMYR' => $game->getMainBoardMYR(),
                    'tile' => $tile->getId()
                ]
            );
            $pheromoneTile = $this->pheromonTileMYRRepository->findOneBy(
                [
                    'mainBoard' => $game->getMainBoardMYR(),
                    'tile' => $tile->getId()
                ]
            );
            $ant = $ant ? $ant : null;
            $pheromoneTile = $pheromoneTile ? $pheromoneTile : null;
        }
        return new BoardBoxMYR($tile, $ant, $pheromoneTile, $x, $y);
    }
}