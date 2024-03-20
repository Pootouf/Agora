<?php

namespace App\Service\Game\Splendor;

use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\TokenSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Exception;

class TokenSPLService
{
    public function __construct(private EntityManagerInterface $entityManager,
        private TokenSPLRepository $tokenSPLRepository) {}

    public function getTokenOnMainBoardFromColor(MainBoardSPL $mainBoardSPL, string $color) : ?TokenSPL
    {
        return $mainBoardSPL->getTokens()->filter(function (TokenSPL $tokenSPL) use ($color) {
            return strcmp($tokenSPL->getColor(), $color) == 0;
        })->first();
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the red tokens
     */
    public function getRedTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == SplendorParameters::$COLOR_RED;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the blue tokens
     */
    public function getBlueTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == SplendorParameters::$COLOR_BLUE;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the green tokens
     */
    public function getGreenTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == SplendorParameters::$COLOR_GREEN;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the white tokens
     */
    public function getWhiteTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == SplendorParameters::$COLOR_WHITE;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the black tokens
     */
    public function getBlackTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == SplendorParameters::$COLOR_BLACK;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the yellow tokens
     */
    public function getYellowTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == SplendorParameters::$COLOR_YELLOW;
        });
    }

    /**
     * takeToken : player takes a token from the mainBoard
     *
     * @param PlayerSPL $playerSPL
     * @param TokenSPL  $tokenSPL
     * @return void
     * @throws Exception
     */
    public function takeToken(PlayerSPL $playerSPL, TokenSPL $tokenSPL): void
    {
        if ($playerSPL->getPersonalBoard()->getTokens()->count()
            + $playerSPL->getPersonalBoard()->getSelectedTokens()->count() >= 10) {
            throw new Exception("Can't pick up more tokens");
        }
        $tokensPickable = $this->canChooseTwoTokens($playerSPL, $tokenSPL);
        if ($tokensPickable == -1) {
            throw new Exception("An error as occurred");
        }
        $personalBoard = $playerSPL->getPersonalBoard();
        $selectedToken = new SelectedTokenSPL();
        $selectedToken->setToken($tokenSPL);
        $selectedToken->setPersonalBoardSPL($personalBoard);
        $personalBoard->addSelectedToken($selectedToken);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->persist($selectedToken);
        $mainBoard = $playerSPL->getGameSPL()->getMainBoard();
        $mainBoard->removeToken($tokenSPL);
        $this->entityManager->persist($mainBoard);
        $this->entityManager->flush();
    }

    /**
     * mustEndPLayerRoundBecauseOfTokens: returns if a player's round must end because of
     *      his selected tokens
     *
     * @param PlayerSPL $playerSPL
     * @return bool
     */
    public function mustEndPlayerRoundBecauseOfTokens(PLayerSPL $playerSPL): bool
    {
        $personalBoard = $playerSPL->getPersonalBoard();
        $tokens = $personalBoard->getTokens();
        $tokensNb = $tokens->count();
        $selectedTokens = $personalBoard->getSelectedTokens();
        $selectedTokensNb = $selectedTokens->count();
        $firstToken = $selectedTokens->first()->getToken();
        $lastToken = $selectedTokens->last()->getToken();
        if ($tokensNb + $selectedTokensNb == SplendorParameters::$PLAYER_MAX_TOKEN
                || !$this->canSelectTokenOfOtherColors($playerSPL->getGameSPL(), $selectedTokens)
            || $selectedTokensNb == 3
            || $selectedTokensNb == 2 && $firstToken->getColor() == $lastToken->getColor()
        ) {
            return true;
        }
        return false;
    }


    /**
     * validateTakingOfTokens: transfer all selected tokens to player's owned
     *
     * @param PlayerSPL $playerSPL
     * @return void
     */
    public function validateTakingOfTokens(PlayerSPL $playerSPL): void
    {
        $selectedTokens = $playerSPL->getPersonalBoard()->getSelectedTokens();
        foreach ($selectedTokens as $selectedToken) {
            $playerSPL->getPersonalBoard()->addToken($selectedToken->getToken());
            $this->entityManager->persist($playerSPL->getPersonalBoard());
            $this->entityManager->persist($playerSPL);
        }
        $selectedTokens->clear();
        $this->entityManager->persist($playerSPL->getPersonalBoard());
        $this->entityManager->persist($playerSPL);
        $this->entityManager->flush();
    }

    /**
     * clearSelectedTokens : removes selected tokens from player
     *
     * @param PlayerSPL $playerSPL
     * @return void
     */
    public function clearSelectedTokens(PlayerSPL $playerSPL): void
    {
        $mainBoard = $playerSPL->getGameSPL()->getMainBoard();
        $selectedTokens = $playerSPL->getPersonalBoard()->getSelectedTokens();
        foreach($selectedTokens as $selectedToken) {
            $mainBoard->addToken($selectedToken->getToken());
        }
        $selectedTokens->clear();
        $this->entityManager->persist($mainBoard);
        $this->entityManager->persist($playerSPL->getPersonalBoard());
        $this->entityManager->persist($playerSPL);
        $this->entityManager->flush();
    }

    /**
     * canChooseTwoTokens : checks if $playerSPL can pick $tokenSPL (in 2 tokens context)
     *
     * @param PlayerSPL $playerSPL
     * @param TokenSPL  $tokenSPL
     * @return int
     *          0 If it's his first token
     *          1 If player takes a token of same color that the one we picked previously
     *          -1 If player can't pick token
     */
    private function canChooseTwoTokens(PlayerSPL $playerSPL, TokenSPL $tokenSPL): int
    {
        $selectedTokens = $playerSPL->getPersonalBoard()->getSelectedTokens();
        if ($selectedTokens->count() == 0) {
            return 0;
        }
        if ($selectedTokens->count() == 1 && $selectedTokens->first()->getToken()->getColor() == $tokenSPL->getColor()) {
            if ($this->selectTokensWithColor($playerSPL, $tokenSPL) < SplendorParameters::$MIN_AVAILABLE_TOKENS) {
                return -1;
            }
            return 1;
        }
        return $this->canChooseThreeTokens($playerSPL, $tokenSPL);
    }

    /**
     * selectTokensWithColor : returns number of remaining tokens of $tokenSPL's color
     *      from $playerSPL's game
     *
     * @param PlayerSPL $playerSPL
     * @param TokenSPL  $tokenSPL
     * @return int
     */
    private function selectTokensWithColor(PlayerSPL $playerSPL, TokenSPL $tokenSPL): int
    {
        $game = $playerSPL->getGameSPL();
        $color = $tokenSPL->getColor();
        $tokens = $game->getMainBoard()->getTokens();
        $result = 0;
        foreach ($tokens as $token) {
            if ($token->getColor() == $color) {
                $result++;
            }
        }
        return $result;
    }

    /**
     * initializeGameToken : add the number of token dependently of the number of players
     * @param GameSPL $gameSPL
     * @return void
     */
    public function initializeGameToken(GameSPL $gameSPL) : void
    {
        $tokens = new ArrayCollection($this->tokenSPLRepository->findAll());
        $blackTokens = array_values($this->getBlackTokensFromCollection($tokens)->toArray());
        $redTokens = array_values($this->getRedTokensFromCollection($tokens)->toArray());
        $whiteTokens = array_values($this->getWhiteTokensFromCollection($tokens)->toArray());
        $blueTokens = array_values($this->getBlueTokensFromCollection($tokens)->toArray());
        $greenTokens = array_values($this->getGreenTokensFromCollection($tokens)->toArray());
        $yellowTokens = array_values($this->getYellowTokensFromCollection($tokens)->toArray());

        switch($gameSPL->getPlayers()->count()) {
            case 2 :
                for ($i = 0; $i < SplendorParameters::$TOKENS_NUMBER_2_PLAYERS; $i++) {
                    $this->addATokenOfEachColor($gameSPL, $blackTokens, $blueTokens,$redTokens,
                                          $greenTokens, $whiteTokens, $i);
                }
                break;
            case 3 :
                for ($i = 1; $i < SplendorParameters::$TOKENS_NUMBER_3_PLAYERS; $i++) {
                    $this->addATokenOfEachColor($gameSPL, $blackTokens, $blueTokens,$redTokens,
                        $greenTokens, $whiteTokens, $i);
                }
                break;

            case 4 :
                for ($i = 1; $i < SplendorParameters::$TOKENS_NUMBER_4_PLAYERS; $i++) {
                    $this->addATokenOfEachColor($gameSPL, $blackTokens, $blueTokens,$redTokens,
                        $greenTokens, $whiteTokens, $i);
                }
                break;
        }

        foreach ($yellowTokens as $token) {
            $gameSPL->getMainBoard()->addToken($token);
        }


    }

    /**
     * canSelectTokenOfOtherColors: return true if there is at least one color of token
     *              available except the selected tokens
     * @param GameSPL $game
     * @param Collection $tokens
     * @return bool
     */
    private function canSelectTokenOfOtherColors(GameSPL $game, Collection $tokens): bool
    {
        $selectedColors = $tokens->map(function (SelectedTokenSPL $token) {
            return $token->getToken()->getColor();
        });

        $colors = $game->getMainBoard()->getTokens()->map(
            function (TokenSPL $tokenSPL) {
                return $tokenSPL->getColor();
            }
            )->filter(function (String $color) use ($selectedColors) {
                return !$selectedColors->contains($color);
        });
        return $colors->count() > 0;
    }

    /**
     * canChooseThreeTokens : checks if $playerSPL can choose $tokenSPL (in 3 tokens context)
     *
     * @param PlayerSPL $playerSPL
     * @param TokenSPL  $tokenSPL
     * @return int
     *              0 If it's player's first token
     *              or player has already picked another token from another context
     *              1 If player picked 2 tokens and the new one has a different color from the others
     *              -1 If player can't pick token
     */
    private function canChooseThreeTokens(PlayerSPL $playerSPL, TokenSPL $tokenSPL): int
    {
        $selectedTokens = $playerSPL->getPersonalBoard()->getSelectedTokens();
        if ($selectedTokens->count() == 0) {
            return 0;
        }
        if ($selectedTokens->count() == 1 && $selectedTokens->first()->getToken()->getColor() != $tokenSPL->getColor()) {
            return 0;
        }
        if ($selectedTokens->count() == 2 && $selectedTokens->first()->getToken()->getColor() != $tokenSPL->getColor() && $selectedTokens[1]->getToken()->getColor() != $tokenSPL->getColor() && $selectedTokens->first()->getToken()->getColor() != $selectedTokens[1]->getToken()->getColor()) {
            return 1;
        }
        return -1;
    }

    /**
     * addATokenOfEachColor : add a token of each color of index $i from the collections in parameter
     * @param GameSPL $gameSPL
     * @param array $blackTokens
     * @param array $blueTokens
     * @param array $redTokens
     * @param array $greenTokens
     * @param array $whiteTokens
     * @param int $i
     * @return void
     */
    private function addATokenOfEachColor(GameSPL $gameSPL, array $blackTokens,
                                          array $blueTokens, array $redTokens,
                                          array $greenTokens, array $whiteTokens, int $i) : void
    {
        $gameSPL->getMainBoard()->addToken($blackTokens[$i]);
        $gameSPL->getMainBoard()->addToken($blueTokens[$i]);
        $gameSPL->getMainBoard()->addToken($redTokens[$i]);
        $gameSPL->getMainBoard()->addToken($greenTokens[$i]);
        $gameSPL->getMainBoard()->addToken($redTokens[$i]);
        $gameSPL->getMainBoard()->addToken($whiteTokens[$i]);

    }
}