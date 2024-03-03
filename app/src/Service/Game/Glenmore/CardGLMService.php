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
    public function __construct(private readonly EntityManagerInterface $entityManager)
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
        $this->applyEndGameCard($gameGLM, GlenmoreParameters::$CARD_IONA_ABBEY,
            GlenmoreParameters::$TILE_TYPE_YELLOW, GlenmoreParameters::$IONA_ABBEY_POINTS);
    }

    /**
     * applyDuartCastle: applies effect of card Duart Castle
     * @param GameGLM $gameGLM
     * @return void
     */
    public function applyDuartCastle(GameGLM $gameGLM) : void
    {
        $this->applyEndGameCard($gameGLM, GlenmoreParameters::$CARD_DUART_CASTLE,
            GlenmoreParameters::$TILE_TYPE_VILLAGE, GlenmoreParameters::$DUART_CASTLE_POINTS);
    }

    /**
     * applyLochMorar: applies effect of card Loch Morar
     * @param GameGLM $gameGLM
     * @return void
     */
    public function applyLochMorar(GameGLM $gameGLM) : void
    {
        $this->applyEndGameCard($gameGLM, GlenmoreParameters::$CARD_LOCH_MORAR,
            GlenmoreParameters::$TILE_TYPE_GREEN, GlenmoreParameters::$LOCH_MORAR_POINTS);
    }


    /**
     * applyEndGameCard : applies effect of special cards for points count at the end of the game.
     * @param GameGLM $gameGLM the game
     * @param String  $cardName the name of the special card
     * @param String  $tileColor the color of the tiles impacted by the card
     * @param int     $cardPoints the amount of points given by the card
     * @return void
     */
    private function applyEndGameCard(GameGLM $gameGLM, String $cardName,
        String $tileColor, int $cardPoints) : void
    {
        $players = $gameGLM->getPlayers();
        $owns = false;
        foreach ($players as $player) {
            foreach ($player->getPersonalBoard()->getCards() as $card) {
                if ($card->getName() === $cardName) {
                    $owns = true;
                    break;
                }
            }
            if ($owns) {
                foreach ($player->getPersonalBoard()->getPlayerTiles() as $tile) {
                    if ($tile->getTile()->getType() == $tileColor) {
                        $player->setPoints($player->getPoints() + $cardPoints);
                    }
                }
                break;
            }
            $this->entityManager->persist($player);
        }
        $this->entityManager->flush();
    }
}