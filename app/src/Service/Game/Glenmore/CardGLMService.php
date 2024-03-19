<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\Glenmore\CreatedResourceGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\SelectedResourceGLM;
use App\Entity\Game\Glenmore\TileBuyBonusGLM;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

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
     * applyLochNess : if player owns Loch Ness and Loch Ness was not used yet during its round,
     *  all non activated tiles can be activated but only of them
     * @param PersonalBoardGLM $personalBoard
     * @return Collection<Int, PlayerTileGLM>
     */
    public function applyLochNess(PersonalBoardGLM $personalBoard) : Collection
    {
        $activableTiles = new ArrayCollection();
        $owns = false;
        $mustActivate = false;
        // checks if player owns Loch Ness
        $cards = $personalBoard->getPlayerCardGLM();
        foreach ($cards as $card) {
            if ($card->getCard()->getName() == GlenmoreParameters::$CARD_LOCH_NESS) {
                $owns = true;
                break;
            }
        }
        if ($owns) {
            // gets Loch Ness tile
            foreach ($personalBoard->getPlayerTiles() as $playerTile) {
                if ($playerTile->getTile()->getName() == GlenmoreParameters::$CARD_LOCH_NESS) {
                    // if Loch Ness power was not used
                    if(!$playerTile->isActivated()) {
                        $mustActivate = true;
                        break;
                    }
                }
            }
            if ($mustActivate) {
                foreach ($personalBoard->getPlayerTiles() as $playerTile) {
                    if(!$playerTile->isActivated() && !$playerTile->getTile()->getActivationBonus()->isEmpty()) {
                        $activableTiles->add($playerTile);
                    }
                }
            }
        }
        return $activableTiles;
    }


    /**
     * buyCardManagement : applies effect of the card associated to playerTile
     * @param PlayerTileGLM $playerTileGLM
     * @return int -1 if Loch Lochy was bought, 0 else
     */
    public function buyCardManagement(PlayerTileGLM $playerTileGLM) : int
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
                return $this->applyLochLochy($playerTileGLM);
            case GlenmoreParameters::$CARD_CASTLE_MOIL:
                $this->applyCastleMoil($playerTileGLM);
                break;
            default:
                break;
        }
        return 0;
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
     * selectResourceForLochLochy : for Loch Lochy, player picks resources
     * @param PlayerGLM   $playerGLM
     * @param ResourceGLM $resourceGLM
     * @return void
     * @throws Exception
     */
    public function selectResourceForLochLochy(PlayerGLM $playerGLM, ResourceGLM $resourceGLM) : void
    {
        $createdResources = $playerGLM->getPersonalBoard()->getCreatedResources();
        if ($createdResources->count() >= 2) {
            throw new Exception("can't pick more resources");
        }
        if ($createdResources->count() == 1) {
            $createdResource = $createdResources->first();
            if ($createdResource->getResource()->getColor() === $resourceGLM->getColor()) {
                $createdResource->setQuantity($createdResource->getQuantity() + 1);
                $this->entityManager->persist($createdResource);
            }
        } else {
            $createdResource = new CreatedResourceGLM();
            $createdResource->setResource($resourceGLM);
            $createdResource->setQuantity(1);
            $createdResource->setPersonalBoardGLM($playerGLM->getPersonalBoard());
            $this->entityManager->persist($createdResource);
            $playerGLM->getPersonalBoard()->addCreatedResource($createdResource);
        }
        $this->entityManager->persist($playerGLM->getPersonalBoard());
        $this->entityManager->flush();
    }

    /**
     * clearCreatedResources : clear all created resources by the player
     * @param PlayerGLM $playerGLM
     * @return void
     */
    public function clearCreatedResources(PlayerGLM $playerGLM) : void
    {
        $playerGLM->getPersonalBoard()->getCreatedResources()->clear();
        $this->entityManager->persist($playerGLM->getPersonalBoard());
        $this->entityManager->flush();
    }

    /**
     * validateTakingOfResourcesForLochLochy : for each resource selected, place it on Loch Lochy tile,
     *  then clears his collection of resources
     *
     * @param PlayerGLM $playerGLM
     * @return void
     */
    public function validateTakingOfResourcesForLochLochy(PlayerGLM $playerGLM) : void
    {
        $createdResources = $playerGLM->getPersonalBoard()->getCreatedResources();
        $tile = null;
        foreach ($playerGLM->getPersonalBoard()->getPlayerTiles() as $playerTile) {
            if ($playerTile->getTile()->getName() === GlenmoreParameters::$CARD_LOCH_LOCHY) {
                $tile = $playerTile;
            }
        }
        foreach ($createdResources as $createdResource) {
            $playerResource = new PlayerTileResourceGLM();
            $playerResource->setResource($createdResource->getResource());
            $playerResource->setQuantity($createdResource->getQuantity());
            $playerResource->setPlayer($playerGLM);
            $playerResource->setPlayerTileGLM($tile);
            $this->entityManager->persist($playerResource);
            $tile->addPlayerTileResource($playerResource);
            $this->entityManager->persist($tile);
        }
        $this->clearCreatedResources($playerGLM);
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
        $this->giveWhisky($playerTileGLM, 1);
    }

    /**
     * applyDonanCastle : gives 2 whisky barrels to the player
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     */
    private function applyDonanCastle(PlayerTileGLM $playerTileGLM) : void
    {
       $this->giveWhisky($playerTileGLM, 2);
    }

    /**
     * applyCastleStalker : gives another villager onto the tile
     * @param PlayerTileGLM $playerTileGLM
     * @return void
     */
    private function applyCastleStalker(PlayerTileGLM $playerTileGLM) : void
    {
        foreach ($playerTileGLM->getPlayerTileResource() as $playerTileResource) {
            if ($playerTileResource->getResource()->getType() == GlenmoreParameters::$VILLAGER_RESOURCE) {
                $playerTileResource->setQuantity($playerTileResource->getQuantity() + 1);
                $this->entityManager->persist($playerTileResource);
            }
        }
        $this->entityManager->persist($playerTileGLM);
        $this->entityManager->persist($playerTileGLM->getPersonalBoard());
        $this->entityManager->flush();
    }

    /**
     * giveWhisky : gives $amount whisky barrels to the player
     * @param PlayerTileGLM $playerTileGLM
     * @param int           $amount
     * @return void
     */
    private function giveWhisky(PlayerTileGLM $playerTileGLM, int $amount) : void
    {
        $resource = $this->resourceGLMRepository->findOneBy(["type" => GlenmoreParameters::$WHISKY_RESOURCE]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setPlayer($playerTileGLM->getPersonalBoard()->getPlayerGLM());
        $playerTileResource->setQuantity($amount);
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

    /**
     * applyLochShiel : for each production tile empty, gives its activation bonus
     * @param PersonalBoardGLM $personalBoard
     * @return void
     */
    private function applyLochShiel(PersonalBoardGLM $personalBoard) : void
    {
        $tiles = $personalBoard->getPlayerTiles();
        foreach ($tiles as $tile) {
            foreach ($tile->getPlayerTileResource() as $playerTileResource) {
                if ($playerTileResource->getResource()->getType()
                    == GlenmoreParameters::$PRODUCTION_RESOURCE) {
                    if($playerTileResource->getQuantity() == 0) {
                        $playerTileResource->setQuantity(1);
                        $this->entityManager->persist($playerTileResource);
                    }
                }
            }
        }
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }

    /**
     * applyLochLochy : returns an integer to indicate to the controller to publish a Mercure notif
     * @param PlayerTileGLM $playerTileGLM
     * @return int
     */
    private function applyLochLochy(PlayerTileGLM $playerTileGLM) : int
    {
        return -1;
    }

}