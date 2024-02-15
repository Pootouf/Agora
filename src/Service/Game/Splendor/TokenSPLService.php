<?php

namespace App\Service\Game\Splendor;

use App\Entity\Game\Splendor\TokenSPL;
use Doctrine\Common\Collections\Collection;

class TokenSPLService
{

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the red tokens
     */
    public function getRedTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function ($token) {
            return $token->getColor() == TokenSPL::$COLOR_RED;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the blue tokens
     */
    public function getBlueTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function ($token) {
            return $token->getColor() == TokenSPL::$COLOR_BLUE;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the green tokens
     */
    public function getGreenTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function ($token) {
            return $token->getColor() == TokenSPL::$COLOR_GREEN;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the white tokens
     */
    public function getWhiteTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function ($token) {
            return $token->getColor() == TokenSPL::$COLOR_WHITE;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the black tokens
     */
    public function getBlackTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function ($token) {
            return $token->getColor() == TokenSPL::$COLOR_BLACK;
        });
    }

    /**
     * @param Collection<TokenSPL> $tokens
     * @return Collection<TokenSPL> the yellow tokens
     */
    public function getYellowTokensFromCollection(Collection $tokens): Collection
    {
        return $tokens->filter(function ($token) {
            return $token->getColor() == TokenSPL::$COLOR_YELLOW;
        });
    }

}