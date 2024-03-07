<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\DTO\Glenmore\BoardBoxGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * Enables to manipulate data and convert it into representable content
 */
class DataManagementGLMService
{

    public function __construct(private PlayerTileGLMRepository $playerTileGLMRepository)
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
                    ++$whiskyCount;
                }
            }
        }
        return $whiskyCount;
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
                        $boardBoxes->add(new BoardBoxGLM($tile->getTile(), null));
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
    public function organizePersonalBoardRows(PlayerGLM $playerGLM) : Collection
    {
        $result = new ArrayCollection();

        $miny = $this->playerTileGLMRepository->
        findOneBy(['personalBoard' => $playerGLM->getPersonalBoard()], ['coord_Y' => 'ASC'])->getCoordY();

        $tiles = $playerGLM->getPersonalBoard()->getPlayerTiles()->toArray();
        usort($tiles, function ($tile1, $tile2){
            $value = $tile2->getCoordX() - $tile1->getCoordX();
            return $value == 0 ? $tile2->getCoordY() - $tile1->getCoordY() : $value;
        });

        $previousTile = $tiles[0];
        $currentLine = new ArrayCollection();
        foreach ($tiles as $tile) {
            $y = $previousTile->getCoordY();
            if ($previousTile->getCoordX() < $tile->getCoordX()) {
                $y = $miny;
                $result->add($currentLine);
                $currentLine = new ArrayCollection();
            }
            while ($y + 1 < $tile->getCoordY()) {
                $currentLine->add(null);
                $y++;
            }
            $currentLine->add($tile);
            $previousTile = $tile;
        }
        $result->add($currentLine);
        return $result;
    }
}