<?php

namespace App\Controller\Game;

use App\Entity\Game\DTO\Glenmore\BoardBoxGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\MessageService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GlenmoreController extends AbstractController
{

    public function __construct(private GLMService $service, private MessageService $messageService)
    {}

    #[Route('/game/glenmore/{id}', name: 'app_game_show_glm')]
    public function showGame(GameGLM $game): Response
    {
        $player = $this->service->getPlayerFromNameAndGame($game, $this->getUser()->getUsername());
        $isSpectator = false;
        $needToPlay = false;
        if ($player == null) {
            $player = $game->getPlayers()->get(0);
            $isSpectator = true;
        } else {
            //$needToPlay = $player->isTurnOfPlayer();
        }
        $messages = $this->messageService->receiveMessage($game->getId());
        return $this->render('/Game/Glenmore/index.html.twig', [
            'game' => $game,
            'player' => $player,
            'isSpectator' => $isSpectator,
            'needToPlay' => $needToPlay,
            'isGameFinished' => $this->service->isGameEnded($game),
            'selectedTile' => null,
            'adjacentTiles' => null,
            'potentialNeighbours' => null,
            'boardTiles' => $this->organizeMainBoardRows($this->createBoardBoxes($game)),
            'messages' => $messages,
        ]);
    }

    /**
     * createBoardBoxes : return a collection of BoardBoxGLM.
     * It transforms the pawns and tiles used in back-end into BoardBoxGLM for the front-end.
     * @param GameGLM $game
     * @return Collection<BoardBoxGLM>
     */
    private function createBoardBoxes(GameGLM $game) : Collection
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
    private function organizeMainBoardRows(Collection $boardBoxes) : Collection
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
}