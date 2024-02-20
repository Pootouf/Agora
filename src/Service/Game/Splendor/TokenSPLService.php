<?php

namespace App\Service\Game\Splendor;

use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\TokenSPLRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;

class TokenSPLService
{
    public function __construct(private EntityManagerInterface $entityManager,
        private TokenSPLRepository $tokenSPLRepository,
        private SPLService $SPLService) {}

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the red tokens
     */
    public function getRedTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == TokenSPL::$COLOR_RED;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the blue tokens
     */
    public function getBlueTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == TokenSPL::$COLOR_BLUE;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the green tokens
     */
    public function getGreenTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == TokenSPL::$COLOR_GREEN;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the white tokens
     */
    public function getWhiteTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == TokenSPL::$COLOR_WHITE;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the black tokens
     */
    public function getBlackTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == TokenSPL::$COLOR_BLACK;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the yellow tokens
     */
    public function getYellowTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function($token) {
            return $token->getColor() == TokenSPL::$COLOR_YELLOW;
        });
    }

    /**
     * takeToken : player takes a token from the mainBoard
     *
     * @param PlayerSPL $playerSPL
     * @param TokenSPL  $tokenSPL
     * @return void
     */
    public function takeToken(PlayerSPL $playerSPL, TokenSPL $tokenSPL): void
    {
        if ($playerSPL->getPersonalBoard()->getTokens()->count() >= 10) {
            throw new Exception("Can't pick up more tokens");
        }
        $tokensPickable = $this->canChooseTwoTokens($playerSPL, $tokenSPL);
        if ($tokensPickable == -1) {
            throw new Exception("An error as occurred");
        }
        $selectedToken = new SelectedTokenSPL();
        $selectedToken->setToken($tokenSPL);
        $playerSPL->getPersonalBoard()->addSelectedToken($selectedToken);
        $this->entityManager->persist($selectedToken);
        $this->entityManager->persist($playerSPL);
        $this->entityManager->flush();
    }

    /**
     * mustEndPLayerRoundBecauseOfTokens: returns if a player's round must end because of
     *      his selected tokens
     *
     * @param PlayerSPL $playerSPL
     * @return bool
     */
    public function mustEndPlayerRoundBecauseOfTokens(PLayerSPL $playerSPL): Boolean
    {
        $personalBoard = $playerSPL->getPersonalBoard();
        $tokens = $personalBoard->getTokens();
        $tokensNb = $tokens->count();
        $selectedTokens = $personalBoard->getSelectedTokens();
        $selectedTokensNb = $selectedTokens->count();
        $firstToken = $selectedTokens->first()->getToken();
        $lastToken = $selectedTokens->last()->getToken();
        if ($tokensNb + $selectedTokensNb == 10
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
        }
    }

    /**
     * clearSelectedTokens : removes selected tokens from player
     *
     * @param PlayerSPL $playerSPL
     * @return void
     */
    public function clearSelectedTokens(PlayerSPL $playerSPL): void
    {
        $playerSPL->getPersonalBoard()->getSelectedTokens()->clear();
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
            if ($this->selectTokensWithColor($playerSPL, $tokenSPL) < SPLService::$MIN_AVAILABLE_TOKENS) {
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
}