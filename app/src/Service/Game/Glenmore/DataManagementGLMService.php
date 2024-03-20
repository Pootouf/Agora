<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\DTO\Glenmore\BoardBoxGLM;
use App\Entity\Game\DTO\Glenmore\PersonalBoardBoxGLM;
use App\Entity\Game\DTO\Glenmore\PlayerResourcesDataGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;


/**
 * Enables to manipulate data and convert it into representable content
 */
class DataManagementGLMService
{

    public function __construct(private PlayerTileGLMRepository $playerTileGLMRepository,
                                private TileGLMService $tileGLMService)
    {}

    /**
     * getWhiskyCount : return an integer of the number of whisky possessed by a player
     * @param PlayerGLM $playerGLM
     * @return int
     */
    public function getWhiskyCount(PlayerGLM $playerGLM) : int
    {
        $whiskyCount = 0;
        $tiles = $playerGLM->getPersonalBoard()->getPlayerTiles();
        foreach ($tiles as $tile) {
            $resources = $tile->getPlayerTileResource();
            foreach($resources as $resource) {
                if($resource->getResource()->getType() == GlenmoreParameters::$WHISKY_RESOURCE) {
                    $whiskyCount += $resource->getQuantity();
                }
            }
        }
        return $whiskyCount;
    }


    /**
     * getPlayersResourcesData : return a collection containing the players data regarding their resources
     * @param GameGLM $gameGLM
     * @return Collection
     */
    public function getPlayersResourcesData(GameGLM $gameGLM) : Collection
    {
        $playersData = new ArrayCollection();
        foreach($gameGLM->getPlayers() as $player) {
            $playerData = new PlayerResourcesDataGLM($player,
                                                    $this->tileGLMService->getPlayerProductionResources($player),
                                                    $this->tileGLMService->getMovementPoints($player),
                                                    $this->getWhiskyCount($player));
            $playersData->add($playerData);

        }
        return $playersData;
    }

    /**
     * createBoardBoxes : return a collection of BoardBoxGLM.
     * It transforms the pawns and tiles used in back-end into BoardBoxGLM for the front-end.
     * @param GameGLM $game
     * @return Collection<BoardBoxGLM>
     */
    public function createBoardBoxes(GameGLM $game) : Collection
    {
        $tiles = $game->getMainBoard()->getBoardTiles();
        $pawns = $game->getMainBoard()->getPawns();
        $boardBoxes = new ArrayCollection();

        for($i = 0; $i < GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD; $i++) {
            $isEmptyBox = true;
            foreach($tiles as $tile) {
                if($tile->getPosition() == $i) {
                    try {
                        $boardBoxes->add(new BoardBoxGLM($tile, null));
                    } catch (\Exception $e) {
                        //Can't append here with a null argument
                    }
                    $isEmptyBox = false;
                    break;
                }
            }
            foreach($pawns as $pawn) {
                if($pawn->getPosition() == $i) {
                    try {
                        $boardBoxes->add(new BoardBoxGLM(null, $pawn));
                    } catch (\Exception $e) {
                        //Can't append here with a null argument
                    }
                    $isEmptyBox = false;
                    break;
                }
            }
            if($isEmptyBox) {
                $boardBoxes->add(new BoardBoxGLM(null, null));
            }
        }
        return $boardBoxes;
    }

    /**
     * organizeMainBoardRows : return a collection of rows, a row is a collection of BoardBoxGLM.
     * It represents each row of the board from top to bottom.
     * @param Collection<BoardBoxGLM> $boardBoxes
     * @return Collection<Collection<BoardBoxGLM>>
     */
    public function organizeMainBoardRows(Collection $boardBoxes) : Collection
    {
        $rows = new ArrayCollection();

        $row1 = new ArrayCollection();
        $row1->add($boardBoxes->get(0));
        $row1->add($boardBoxes->get(1));
        $row1->add($boardBoxes->get(2));
        $row1->add($boardBoxes->get(3));
        $row1->add($boardBoxes->get(4));

        $rows->add($row1);

        $row2 = new ArrayCollection();
        $row2->add($boardBoxes->get(13));
        $row2->add($boardBoxes->get(5));

        $rows->add($row2);

        $row3 = new ArrayCollection();
        $row3->add($boardBoxes->get(12));
        $row3->add($boardBoxes->get(6));

        $rows->add($row3);

        $row4 = new ArrayCollection();
        $row4->add($boardBoxes->get(11));
        $row4->add($boardBoxes->get(10));
        $row4->add($boardBoxes->get(9));
        $row4->add($boardBoxes->get(8));
        $row4->add($boardBoxes->get(7));

        $rows->add($row4);
        return $rows;
    }

    /**
     * organizePersonalBoardRows : return a collection of rows, a row is a collection of tiles or null.
     *  It represents each row of the personal board from top to bottom.
     * @param PlayerGLM $playerGLM
     * @return Collection
     */
    public function organizePersonalBoardRows(PlayerGLM $playerGLM, array $possiblePlacement) : Collection
    {
        $result = new ArrayCollection();

        // Change min and max y & x for adding extra empty tile (one empty line at the start of the board, one empty
        //  tile at each side of the line)
        $miny = $this->playerTileGLMRepository->
        findOneBy(['personalBoard' => $playerGLM->getPersonalBoard()], ['coord_Y' => 'ASC'])->getCoordY() - 1;
        $maxy = $this->playerTileGLMRepository->
        findOneBy(['personalBoard' => $playerGLM->getPersonalBoard()], ['coord_Y' => 'DESC'])->getCoordY() + 1;
        $minx = $this->playerTileGLMRepository->
        findOneBy(['personalBoard' => $playerGLM->getPersonalBoard()], ['coord_X' => 'ASC'])->getCoordX() - 1;
        $maxx = $this->playerTileGLMRepository->
            findOneBy(['personalBoard' => $playerGLM->getPersonalBoard()], ['coord_X' => 'DESC'])->getCoordX() + 1;

        //Sorting by x coord and then by y coord
        $tiles = $playerGLM->getPersonalBoard()->getPlayerTiles()->toArray();
        usort($tiles, function ($tile1, $tile2){
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
                    $currentLine->add(new PersonalBoardBoxGLM(
                        null,
                        $x,
                        $y,
                        in_array([$x, $y], $possiblePlacement)
                    ));
                    $y++;
                }
                $y = $miny;
                $result->add($currentLine);
                $x++;
                $currentLine = new ArrayCollection();

            }
            // Fill the gaps up to the next y coord
            while ($y < $tile->getCoordY()) {
                $currentLine->add(new PersonalBoardBoxGLM(
                    null,
                    $x,
                    $y,
                    in_array([$x, $y], $possiblePlacement)
                ));
                $y++;
            }
            $currentLine->add(new PersonalBoardBoxGLM($tile, $x, $y, in_array([$x, $y], $possiblePlacement)));
            $previousX = $tile->getCoordX();
            $y++;
        }
        // Complete the last added line until max y
        while ($y <= $maxy) {
            $currentLine->add(new PersonalBoardBoxGLM(null, $x, $y, in_array([$x, $y], $possiblePlacement)));
            $y++;
        }
        $result->add($currentLine);
        // Add an empty tile row at the end of the board
        while($x < $maxx) {
            $currentLine = new ArrayCollection();
            $y = $miny;
            $x++;
            while($y <= $maxy) {
                $currentLine->add(new PersonalBoardBoxGLM(null, $x, $y, in_array([$x, $y], $possiblePlacement)));
                $y++;
            }
            $result->add($currentLine);
        }
        return $result;
    }
}