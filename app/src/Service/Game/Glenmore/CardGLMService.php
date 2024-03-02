<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Repository\Game\Glenmore\DrawTilesGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use Doctrine\ORM\EntityManagerInterface;

class CardGLMService
{
    public function __construct(private EntityManagerInterface $entityManager,
        private TileGLMRepository $tileGLMRepository,
        private DrawTilesGLMRepository $drawTilesGLMRepository,
        private ResourceGLMRepository $resourceGLMRepository,
        private PlayerGLMRepository $playerGLMRepository)
    {}

    /** applyCastle Of Mey : applies effect of card Castle Of Mey
     * @param PersonalBoardGLM $personalBoard
     * @param int              $playerResource
     * @return int
     */
    public function applyCastleOfMey(PersonalBoardGLM $personalBoard, int $playerResource) : int
    {
        foreach($personalBoard->getCards() as $card) {
            if ($card->getName() == GlenmoreParameters::$CARD_CASTLE_OF_MEY) {
                $playerResource *= 2;
                break;
            }
        }
        return $playerResource;
    }

    /**
     * applyIonaAbbey: applies effect of card Iona Abbey
     * @param GameGLM $gameGLM
     * @return void
     */
    public function applyIonaAbbey(GameGLM $gameGLM) : void
    {
        // TODO
    }

    /**
     * applyDuartCastle: applies effect of card Duart Castle
     * @param GameGLM $gameGLM
     * @return void
     */
    public function applyDuartCastle(GameGLM $gameGLM) : void
    {
        // TODO
    }

    /**
     * applyLochMorar: applies effect of card Loch Morar
     * @param GameGLM $gameGLM
     * @return void
     */
    public function applyLochMorar(GameGLM $gameGLM) : void
    {
        // TODO
    }
}