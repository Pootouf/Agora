<?php

namespace App\Service\Game;


use App\Entity\Game\DTO\Game;
use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Repository\Game\SixQP\CardSixQPRepository;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use function PHPUnit\Framework\isNull;

class SixQPService extends AbstractGameService
{
    public static int $MAX_POINTS = 66;

    private EntityManagerInterface $entityManager;
    private CardSixQPRepository $cardSixQPRepository;
    private ChosenCardSixQPRepository $chosenCardSixQPRepository;

    private PlayerSixQPRepository $playerSixQPRepository;

    public function __construct(EntityManagerInterface $entityManager,
        CardSixQPRepository $cardSixQPRepository,
        ChosenCardSixQPRepository $chosenCardSixQPRepository,
        PlayerSixQPRepository $playerSixQPRepository)
    {
        $this->entityManager = $entityManager;
        $this->cardSixQPRepository = $cardSixQPRepository;
        $this->chosenCardSixQPRepository = $chosenCardSixQPRepository;
        $this->playerSixQPRepository = $playerSixQPRepository;
    }

    /**
     * createGame : create a six q p game
     * @return int the id of the game
     */
    public function createGame(): int
    {

        $game = new GameSixQP();
        $game->setGameName('6QP');
        for($i = 0; $i < RowSixQP::$NUMBER_OF_ROWS_BY_GAME; $i++) {
            $row = new RowSixQP();
            $row->setPosition($i);
            $game->addRowSixQP($row);
            $this->entityManager->persist($row);
        }

        $this->entityManager->persist($game);
        $this->entityManager->flush();
        return $game->getId();
    }

    /**
     * createPlayer : create a sixqp player and save him in the database
     * @param string $playerName the name of the player to create
     * @param Game $game the game of the player
     * @return int -4 if too many player, 1 success, -7 already in the party
     */
    public function createPlayer(string $playerName, Game $game): int
    {
        $game = $this->getGameSixQPFromGame($game);
        if ($game == null) {
            return -6;
        }
        if (count($game->getPlayerSixQPs()) >= 10) {
            return -4;
        }
        if ($this->playerSixQPRepository->findOneBy(['username' => $playerName]) != null) {
            return -7;
        }
        $player = new PlayerSixQP($playerName, $game);
        $discard = new DiscardSixQP($player, $game);
        $player->setDiscardSixQP($discard);
        $game->addPlayerSixQP($player);
        $this->entityManager->persist($player);
        $this->entityManager->persist($discard);
        $this->entityManager->flush();
        return 1;
    }

    /**
     * deletePlayer : delete a sixqp player
     * @param string $playerName the name of the player to delete
     * @param GameSixQP $game the game of the player
     * @return int 1 success, -1 no player
     */
    public function deletePlayer(string $playerName, Game $game): int
    {
        $game = $this->getGameSixQPFromGame($game);
        if ($game == null) {
            return -6;
        }
        $player = $this->playerSixQPRepository->findOneBy(['game' => $game->getId(), 'username' => $playerName]);
        if ($player == null) {
            return -1;
        }
        $this->entityManager->remove($player);
        $this->entityManager->flush();
        return 1;
    }

    /**
     * deleteGame : delete a game
     * @param GameSixQP $game the game to delete
     * @return int
     */
    public function deleteGame(Game $game): int
    {
        $game = $this->getGameSixQPFromGame($game);
        if ($game == null) {
            return -6;
        }
        foreach ($game->getPlayerSixQPs() as $playerSixQP) {
            $this->entityManager->remove($playerSixQP);
        }
        foreach ($game->getRowSixQPs() as $rowSixQP) {
            $this->entityManager->remove($rowSixQP);
        }
        $this->entityManager->remove($game);
        $this->entityManager->flush();
        return 1;
    }

    /**
     * launchGame : launch a game
     * @param GameSixQP $game the game to launch
     * @return int -4 not a valid number of player, 1 success
     */
    public function launchGame(Game $game): int
    {
        $game = $this->getGameSixQPFromGame($game);
        if ($game == null) {
            return -6;
        }
        $numberOfPlayers = count($game->getPlayerSixQPs());
        if ($numberOfPlayers > 10 || $numberOfPlayers < 2) {
            return -4;
        }
        $game->setLaunched(true);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        return 1;
    }




    /**
     * initializeNewRound : initialize a new round with random cards for central board and players
     * @param GameSixQP $gameSixQP
     * @return void
     * @throws Exception if not valid number of players or rows
     */
    public function initializeNewRound(GameSixQP $gameSixQP): void
    {
        if (count($gameSixQP->getRowSixQPs()) != RowSixQP::$NUMBER_OF_ROWS_BY_GAME) {
            throw new Exception('Invalid number of rows');
        }
        $numberOfPlayers = count($gameSixQP->getPlayerSixQPs());
        if ($numberOfPlayers > 10 || $numberOfPlayers < 2) {
            throw new Exception('Invalid number of players');
        }

        $cards = $this->cardSixQPRepository->findAll();
        shuffle($cards);
        $players = $gameSixQP->getPlayerSixQPs();
        $cardIndex = 0;
        foreach ($gameSixQP->getRowSixQPs() as $row) {
            $row->clearCards();
            $row->addCard($cards[$cardIndex++]);
            $this->entityManager->persist($row);
        }
        foreach ($players as $player) {
            $player->clearCards();
            for ($i = 0; $i < PlayerSixQP::$NUMBER_OF_CARDS_BY_PLAYER; $i++) {
                $player->addCard($cards[$cardIndex++]);
            }
            $this->entityManager->persist($player);
        }
        $this->entityManager->flush();
    }

    /**
     * chooseCard : players chooses a card which is now in state "chosen"
     * @param PlayerSixQP $player
     * @param CardSixQP $cardSixQP
     * @return void
     * @throws Exception if player doesn't own the card
     * @throws Exception if player has already chosen another card
     */
    public function chooseCard(PlayerSixQP $player, CardSixQP $cardSixQP): void
    {
        if (!$this->doesPlayerOwnsCard($player, $cardSixQP)) {
            throw new Exception("Player doesn't own this card");
        }

        if ($player->getChosenCardSixQP() != null) {
            throw new Exception("Player has already chosen a card");
        }

        $chosenCardSixQP = new ChosenCardSixQP($player, $player->getGame(), $cardSixQP, false);
        $player->removeCard($cardSixQP);
        $player->setChosenCardSixQP($chosenCardSixQP);
        $this->entityManager->persist($chosenCardSixQP);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * placeCard : place the chosen card into the right row, and update player's discard if necessary
     * @param ChosenCardSixQP $chosenCardSixQP
     * @return int 0 if the card has been placed, -1 otherwise
     */
    public function placeCard(ChosenCardSixQP $chosenCardSixQP): int
    {
        $game = $chosenCardSixQP->getGame();
        $rows = $game->getRowSixQPs();
        $player = $chosenCardSixQP->getPlayer();

        $row = $this->getValidRowForCard($chosenCardSixQP, $rows);
        if (isNull($row)) {
            return -1;
        }

        $row->getCards()->add($chosenCardSixQP->getCard());
        if ($row->getCards()->count() == 6) {
            $this->addRowToDiscardOfPlayer($player, $row);
        }

        $this->entityManager->persist($row);
        $this->entityManager->remove($chosenCardSixQP);
        $this->entityManager->flush();
        return 0;
    }

    /**
     * getRanking : get the ranking by player points (ascending)
     * @param GameSixQP $gameSixQP
     * @return array
     * @throws Exception
     */
    public function getRanking(GameSixQP $gameSixQP): array
    {
        $numberOfPlayers = count($gameSixQP->getPlayerSixQPs());
        if ($numberOfPlayers > 10 || $numberOfPlayers < 2)
        {
            throw new Exception('Invalid number of players');
        }

        $ranking = array();

        foreach ($gameSixQP->getPlayerSixQPs() as $player)
        {
            $discard = $player->getDiscardSixQP();
            $ranking[$player->getId()] =
                is_null($discard) ?
                0 :$discard->getTotalPoints();
        }

        
        return $ranking;
    }

    /**
     * calculatePoints : update player points from his discard
     * @param DiscardSixQP $discardSixQP
     * @return void
     */
    public function calculatePoints(DiscardSixQP $discardSixQP): void
    {
        $cards = $discardSixQP->getCards();
        $totalPoints = 0;
        foreach ($cards as $card) {
            $totalPoints += $card->getPoints();
        }
        $discardSixQP->setTotalPoints($totalPoints);
        $this->entityManager->persist($discardSixQP);
        $this->entityManager->flush();
    }

    /**
     * revealCard : reveal the chosen card
     * @param ChosenCardSixQP $chosenCardSixQP the card to reveal
     * @return void
     */
    public function revealCard(ChosenCardSixQP $chosenCardSixQP): void
    {
        $chosenCardSixQP->setVisible(true);
        $this->entityManager->persist($chosenCardSixQP);
        $this->entityManager->flush();
    }

    public function doesAllPlayersHaveChosen(GameSixQP $game): bool {
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        return count($chosenCards) == count($game->getPlayerSixQPs());
    }

    public function doesPlayerAlreadyHasPlayed(PlayerSixQP $player): bool
    {
        $chosenCard = $this->chosenCardSixQPRepository->findOneBy(['player'=>$player->getId()]);
        return $chosenCard != null;
    }

    public function getNotPlacedCard(GameSixQP $game): array {
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        for ($i = 0; $i < count($chosenCards); $i++) {
            foreach ($game->getRowSixQPs() as $row) {
                if ($row->isCardInRow($chosenCards[$i]->getCard())) {
                    array_splice($chosenCards, $i, 1);
                }
            }
        }
        return $chosenCards;
    }

    /**
     * isGameEnded : checks if game has ended
     * @param GameSixQP $gameSixQP 
     * @return bool
     */
    public function isGameEnded(GameSixQP $gameSixQP): bool
    {
        $players = $gameSixQP->getPlayerSixQPs();
        if ($this->hasCardLeft($players)) {
            return false;
        }
        return $this->hasPlayerLost($players);
    }


    /**
     * getValidRowForCard : calculate the row with the nearest value to the chosen card,
     *                      with the chosen card value greater than the value of the row
     * @param ChosenCardSixQP $chosenCardSixQP the chosen card
     * @param Collection $rows the rows of the game
     * @return RowSixQP the valid row, null if no valid row in the game
     */
    private function getValidRowForCard(ChosenCardSixQP $chosenCardSixQP, Collection $rows): RowSixQP
    {
        $rowResult = null;
        $lastSmallestDistance = INF;
        foreach ($rows as $row) {
            $cards = $row->getCards();

            $chosenCardValue = $chosenCardSixQP->getCard()->getValue();
            $rowValue = $cards->get($cards->count() - 1)->getValue();
            if (
                $rowValue < $chosenCardValue
                && ($chosenCardValue - $rowValue) < $lastSmallestDistance
            ) {
                $rowResult = $row;
                $lastSmallestDistance = $chosenCardValue - $rowValue;
            }
        }
        return $rowResult;
    }

    /**
     * addRowToDiscardOfPlayer : add all the cards of the row to the discard of the player
     * @param PlayerSixQP $player the player who will get the cards
     * @param RowSixQP $row the row with the cards to add to the discard
     * @return void
     */
    private function addRowToDiscardOfPlayer(PlayerSixQP $player, RowSixQP $row): void
    {
        for ($i = 0; $i < 5; $i++) {
            $card = $row->getCards()->get(0);
            $row->getCards()->remove(0); //We delete the first 5 positions to delete all the cards
            $player->getDiscardSixQP()->addCard($card);
            $player->getDiscardSixQP()->addPoints($card->getPoints());
        }
        $this->entityManager->persist($player->getDiscardSixQP());
    }

    /**
     * doesPlayerOwnsCard : check if player really owns the asked card
     * @param PlayerSixQP $player
     * @param CardSixQP $card
     * @return bool
     */
    private function doesPlayerOwnsCard(PlayerSixQP $player, CardSixQP $card): bool
    {
        $cards = $player->getCards();
        return $cards->contains($card);
    }

    /**
     * hasCardLeft : checks if at least one player still has a card
     * @param Collection $players : a collection of 6QP players
     */
    private function hasCardLeft(Collection $players): bool
    {
        foreach ($players as $player) {
            if (count($player->getCards()) != 0) {
                return true;
            }
        }
        return false;
    }

     /**
     * hasPlayerLost : checks if at least one player has reached limit of points
     * @param Collection $players : a collection of 6QP players
     */
    private function hasPlayerLost(Collection $players): bool
    {
        foreach($players as $player) {
            if ($player -> getDiscardSixQP() -> getTotalPoints() >= SixQPService::$MAX_POINTS) {
                return true;
            }
        }
        return false;
    }

    private function getGameSixQPFromGame(Game $game): ?GameSixQP {
        /** @var GameSixQP $game */
        return $game->getGameName() == AbstractGameService::$SIXQP_LABEL ? $game : null;
    }
}
