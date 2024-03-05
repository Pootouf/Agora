<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\TileBuyBonusGLM;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use Doctrine\ORM\EntityManagerInterface;

class CardGLMService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
        private ResourceGLMRepository $resourceGLMRepository) {}

    /** applyCastle Of Mey : applies effect of card Castle Of Mey
     *
     * @param PersonalBoardGLM $personalBoard
     * @param int              $playerResource
     * @return int
     */
    public function applyCastleOfMey(PersonalBoardGLM $personalBoard, int $playerResource): int
    {
        foreach ($personalBoard->getPlayerCardGLM() as $playerCard) {
            if ($playerCard->getCard()->getName() == GlenmoreParameters::$CARD_CASTLE_OF_MEY) {
                $playerResource *= 2;
                break;
            }
        }
        return $playerResource;
    }

    /**
     * buyCardManagement : applies effect of the card associated to playerTile
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     */
    public function buyCardManagement(PlayerTileGLM $playerTileGLM) : void
    {
        $tile = $playerTileGLM->getTile();
        $personalBoard = $playerTileGLM->getPersonalBoard();
        $card = $tile->getCard();
        switch($card->getName()) {
            case GlenmoreParameters::$CARD_CASTLE_STALKER:
                $this->applyCastleStalker($playerTileGLM);
                break;
            case GlenmoreParameters::$CARD_LOCH_SHIEL:
                $this->applyLochShiel($personalBoard);
                break;
            case GlenmoreParameters::$CARD_DONAN_CASTLE:
                $this->applyDonanCastle($playerTileGLM);
                break;
            case GlenmoreParameters::$CARD_ARMADALE_CASTLE:
                $this->applyArmadaleCastle($personalBoard);
                break;
            case GlenmoreParameters::$CARD_LOCH_LOCHY:
                $this->applyLochLochy($playerTileGLM);
                break;
            case GlenmoreParameters::$CARD_LOCH_OICH:
                $this->applyLochOich($personalBoard);
                break;
            case GlenmoreParameters::$CARD_CASTLE_MOIL:
                $this->applyCastleMoil($playerTileGLM);
                break;
            default:
                break;
        }
    }

    /**
     * applyIonaAbbey: applies effect of card Iona Abbey
     *
     * @param GameGLM $gameGLM
     * @return void
     */
    public function applyIonaAbbey(GameGLM $gameGLM): void
    {
        $this->applyEndGameCard($gameGLM, GlenmoreParameters::$CARD_IONA_ABBEY,
            GlenmoreParameters::$TILE_TYPE_YELLOW, GlenmoreParameters::$IONA_ABBEY_POINTS);
    }

    /**
     * applyDuartCastle: applies effect of card Duart Castle
     *
     * @param GameGLM $gameGLM
     * @return void
     */
    public function applyDuartCastle(GameGLM $gameGLM): void
    {
        $this->applyEndGameCard($gameGLM, GlenmoreParameters::$CARD_DUART_CASTLE,
            GlenmoreParameters::$TILE_TYPE_VILLAGE, GlenmoreParameters::$DUART_CASTLE_POINTS);
    }

    /**
     * applyLochMorar: applies effect of card Loch Morar
     *
     * @param GameGLM $gameGLM
     * @return void
     */
    public function applyLochMorar(GameGLM $gameGLM): void
    {
        $this->applyEndGameCard($gameGLM, GlenmoreParameters::$CARD_LOCH_MORAR,
            GlenmoreParameters::$TILE_TYPE_GREEN, GlenmoreParameters::$LOCH_MORAR_POINTS);
    }


    /**
     * applyEndGameCard : applies effect of special cards for points count at the end of the game.
     *
     * @param GameGLM $gameGLM    the game
     * @param String  $cardName   the name of the special card
     * @param String  $tileColor  the color of the tiles impacted by the card
     * @param int     $cardPoints the amount of points given by the card
     * @return void
     */
    private function applyEndGameCard(GameGLM $gameGLM, string $cardName,
        string $tileColor, int $cardPoints): void
    {
        $players = $gameGLM->getPlayers();
        $owns = false;
        foreach ($players as $player) {
            foreach ($player->getPersonalBoard()->getPlayerCardGLM() as $playerCard) {
                if ($playerCard->getCard()->getName() === $cardName) {
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

    /**
     * applyCastleMoil : gives a whisky barrel to the player
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     */
    private function applyCastleMoil(PlayerTileGLM $playerTileGLM) : void
    {
        $resource = $this->resourceGLMRepository->findOneBy(["type" => GlenmoreParameters::$WHISKY_RESOURCE]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTileGLM);
        $this->entityManager->persist($playerTileResource);
        $playerTileGLM->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTileGLM);
        $this->entityManager->persist($playerTileGLM->getPersonalBoard());
        $this->entityManager->flush();
    }

    /**
     * applyArmadaleCastle : gives 3 money coins to the player
     * @param PersonalBoardGLM $personalBoardGLM
     * @return void
     */
    private function applyArmadaleCastle(PersonalBoardGLM $personalBoardGLM) : void
    {
        $personalBoardGLM->setMoney($personalBoardGLM->getMoney() + 3);
        $this->entityManager->persist($personalBoardGLM);
        $this->entityManager->flush();
    }

}