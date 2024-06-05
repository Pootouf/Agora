<?php

namespace App\TwigExtension\Platform;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class BoardFilterExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('board_id', [$this, 'formatBoardId']),
        ];
    }

    public function getName()
    {
        return 'board_id';
    }

    /**
     * Used as a filter for a notification of invitation to board, which return the id of the board contained in the content
     * of the notification
     * @param string $message The content of the notification
     * @return int id of the board if the pattern matches the content, else 0 if not
     */
    public function formatBoardId(string $message): int
    {
        if (preg_match('/\((\d+)\)/', $message, $matches)) {
            return intval($matches[1]);
        } else {
            // Aucun nombre trouvé
            return false;
        }
    }
}
