<?php

namespace App\Controller\Game;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\PlayerCardSPL;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Repository\Game\Glenmore\GameGLMRepository;
use App\Repository\Game\Myrmes\GameMYRRepository;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\GameManagerService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @codeCoverageIgnore
 */
#[IsGranted('ROLE_USER')]
class GameTestController extends AbstractController
{

    private GameManagerService $gameService;

    public function __construct(GameManagerService $gameService) {
        $this->gameService = $gameService;
    }

    #[Route('/game/sixqp/list', name: 'app_game_sixqp_list')]
    public function listSixQPGames(GameSixQPRepository $gameSixQPRepository): Response
    {
        $games = $gameSixQPRepository->findAll();

        return $this->render('Game/Six_qp/GameTest/list_games.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/game/sixqp/create', name: 'app_game_sixqp_create')]
    public function createSixQPGame(): Response
    {
        $this->gameService->createGame(AbstractGameManagerService::SIXQP_LABEL);
        return $this->redirectToRoute('app_game_sixqp_list');
    }

    #[Route('/game/sixqp/join/{id}', name: 'app_game_sixqp_join')]
    public function joinSixQPGame(GameSixQP $gameSixQP): Response
    {
        $user = $this->getUser();
        $this->gameService->joinGame($gameSixQP->getId(), $user);
        return $this->redirectToRoute('app_game_sixqp_list');
    }

    #[Route('/game/sixqp/leave/{id}', name: 'app_game_sixqp_quit')]
    public function quitSixQPGame(GameSixQP $gameSixQP): Response
    {
        $user = $this->getUser();
        $this->gameService->quitGame($gameSixQP->getId(), $user);
        return $this->redirectToRoute('app_game_sixqp_list');
    }

    #[Route('/game/sixqp/delete/{id}', name: 'app_game_sixqp_delete')]
    public function deleteSixQPGame(GameSixQP $gameSixQP): Response
    {
        $this->gameService->deleteGame($gameSixQP->getId());
        return $this->redirectToRoute('app_game_sixqp_list');
    }

    #[Route('/game/sixqp/launch/{id}', name: 'app_game_sixqp_launch')]
    public function launchSixQPGame(GameSixQP $gameSixQP): Response
    {
        $this->gameService->launchGame($gameSixQP->getId());
        return $this->redirectToRoute('app_game_sixqp_list');
    }







    #[Route('/game/splendor/list', name: 'app_game_splendor_list')]
    public function listSPLGames(GameSPLRepository $gameSPLRepository): Response
    {
        $games = $gameSPLRepository->findAll();

        return $this->render('Game/Splendor/GameTest/list_games.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/game/splendor/create', name: 'app_game_splendor_create')]
    public function createSplendorGame(): Response
    {
        $this->gameService->createGame(AbstractGameManagerService::SPL_LABEL);
        return $this->redirectToRoute('app_game_splendor_list');
    }

    #[Route('/game/splendor/join/{id}', name: 'app_game_splendor_join')]
    public function joinSplendorGame(GameSPL $gameSPL): Response
    {
        $user = $this->getUser();
        $this->gameService->joinGame($gameSPL->getId(), $user);
        return $this->redirectToRoute('app_game_splendor_list');
    }

    #[Route('/game/splendor/leave/{id}', name: 'app_game_splendor_quit')]
    public function quitSplendorGame(GameSPL $gameSPL): Response
    {
        $user = $this->getUser();
        $this->gameService->quitGame($gameSPL->getId(), $user);
        return $this->redirectToRoute('app_game_splendor_list');
    }

    #[Route('/game/splendor/delete/{id}', name: 'app_game_splendor_delete')]
    public function deleteSplendorGame(GameSPL $gameSPL): Response
    {
        $this->gameService->deleteGame($gameSPL->getId());
        return $this->redirectToRoute('app_game_splendor_list');
    }

    #[Route('/game/splendor/launch/{id}', name: 'app_game_splendor_launch')]
    public function launchSplendorGame(GameSPL $gameSPL): Response
    {
        $this->gameService->launchGame($gameSPL->getId());
        return $this->redirectToRoute('app_game_splendor_list');
    }

    #[Route('/game/splendor/launch/fake/{id}', name: 'app_game_splendor_launchFakeParty')]
    public function launchFakeSplendorGame(GameSPL $gameSPL,
        EntityManagerInterface $entityManager): Response
    {
        $this->gameService->launchGame($gameSPL->getId());
        $players = $gameSPL->getPlayers();

        $tokens = $gameSPL->getMainBoard()->getTokens()->toArray();

        $nobleTiles = $gameSPL->getMainBoard()->getNobleTiles()->toArray();
        $cardsLevel1 = $gameSPL->getMainBoard()->getDrawCards()
            ->get(SplendorParameters::DRAW_CARD_LEVEL_ONE)->getDevelopmentCards();
        $cardsLevel2 = $gameSPL->getMainBoard()->getDrawCards()
            ->get(SplendorParameters::DRAW_CARD_LEVEL_TWO)->getDevelopmentCards();
        $cardsLevel3 = $gameSPL->getMainBoard()->getDrawCards()
            ->get(SplendorParameters::DRAW_CARD_LEVEL_THREE)->getDevelopmentCards();
        $cards = array_merge($cardsLevel1->toArray(), $cardsLevel2->toArray(), $cardsLevel3->toArray());
        shuffle($cards);
        shuffle($nobleTiles);

        $tokenInd = 0;
        $cardInd = 0;
        $nobleTileInd = 0;
        foreach ($players as $player) {
            for ($i = 0; $i < 4; $i++) {
                $this->doRandomly(function () use ($gameSPL, $entityManager, $tokenInd, $tokens, $player) {
                    $player->getPersonalBoard()->addToken($tokens[$tokenInd]);
                    $gameSPL->getMainBoard()->removeToken($tokens[$tokenInd]);
                    $entityManager->persist($player->getPersonalBoard());
                    $entityManager->persist($gameSPL);
                    $entityManager->flush();
                });
                $tokenInd++;
            }

            for ($i = 0; $i < 3; $i++) {
                $this->doRandomly(function () use ($gameSPL, $entityManager, $cardInd, $cards, $player) {
                    $playerCard = new PlayerCardSPL($player, $cards[$cardInd], false);
                    $player->getPersonalBoard()->addPlayerCard($playerCard);
                    $gameSPL->getMainBoard()->getDrawCards()
                        ->get($cards[$cardInd]->getLevel() - 1)->removeDevelopmentCard($cards[$cardInd]);
                    $entityManager->persist($playerCard);
                    $entityManager->persist($player->getPersonalBoard());
                    $entityManager->persist($gameSPL->getMainBoard());
                    $entityManager->flush();
                });
                $cardInd++;
            }

            $this->doRandomly(function () use ($gameSPL, $entityManager, $cardInd, $cards, $player) {
                $playerCard = new PlayerCardSPL($player, $cards[$cardInd], true);
                $player->getPersonalBoard()->addPlayerCard($playerCard);
                $gameSPL->getMainBoard()->getDrawCards()
                    ->get($cards[$cardInd]->getLevel() - 1)->removeDevelopmentCard($cards[$cardInd]);
                $entityManager->persist($playerCard);
                $entityManager->persist($player->getPersonalBoard());
                $entityManager->persist($gameSPL->getMainBoard());
                $entityManager->flush();
            });
            $cardInd++;

            for ($i = 0; $i < 1; $i++) {
                $this->doRandomly(function () use ($gameSPL, $nobleTiles, $nobleTileInd, $entityManager, $player) {
                    $player->getPersonalBoard()->addNobleTile($nobleTiles[$nobleTileInd]);
                    $gameSPL->getMainBoard()->removeNobleTile($nobleTiles[$nobleTileInd]);
                    $entityManager->persist($player->getPersonalBoard());
                    $entityManager->persist($gameSPL->getMainBoard());
                    $entityManager->flush();
                });
                $nobleTileInd++;
            }

        }


        return $this->redirectToRoute('app_game_splendor_list');
    }




    #[Route('/game/glenmore/list', name: 'app_game_glenmore_list')]
    public function listGLMGames(GameGLMRepository $gameGLMRepository): Response
    {
        $games = $gameGLMRepository->findAll();

        return $this->render('Game/Glenmore/GameTest/list_games.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/game/glenmore/create', name: 'app_game_glenmore_create')]
    public function createGlenmoreGame(): Response
    {
        $this->gameService->createGame(AbstractGameManagerService::GLM_LABEL);
        return $this->redirectToRoute('app_game_glenmore_list');
    }

    #[Route('/game/glenmore/join/{id}', name: 'app_game_glenmore_join')]
    public function joinGlenmoreGame(GameGLM $game): Response
    {
        $user = $this->getUser();
        $value = $this->gameService->joinGame($game->getId(), $user);
        if ($value != AbstractGameManagerService::SUCCESS) {
            throw new Exception($value);
        }
        return $this->redirectToRoute('app_game_glenmore_list');
    }

    #[Route('/game/glenmore/leave/{id}', name: 'app_game_glenmore_quit')]
    public function quitGlenmoreGame(GameGLM $game): Response
    {
        $user = $this->getUser();
        $value = $this->gameService->quitGame($game->getId(), $user);
        if ($value != AbstractGameManagerService::SUCCESS) {
            throw new Exception($value);
        }
        return $this->redirectToRoute('app_game_glenmore_list');
    }

    #[Route('/game/glenmore/delete/{id}', name: 'app_game_glenmore_delete')]
    public function deleteGlenmoreGame(GameGLM $game): Response
    {
        $value = $this->gameService->deleteGame($game->getId());
        if ($value != AbstractGameManagerService::SUCCESS) {
            throw new Exception($value);
        }
        return $this->redirectToRoute('app_game_glenmore_list');
    }

    #[Route('/game/glenmore/launch/{id}', name: 'app_game_glenmore_launch')]
    public function launchGlenmoreGame(GameGLM $game): Response
    {
        $value = $this->gameService->launchGame($game->getId());
        if ($value != AbstractGameManagerService::SUCCESS) {
            throw new Exception($value);
        }
        return $this->redirectToRoute('app_game_glenmore_list');
    }



    #[Route('/game/myrmes/list', name: 'app_game_myrmes_list')]
    public function listMYRGames(GameMYRRepository $gameMYRRepository): Response
    {
        $games = $gameMYRRepository->findAll();

        return $this->render('Game/Myrmes/GameTest/list_games.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/game/myrmes/create', name: 'app_game_myrmes_create')]
    public function createMyrmesGame(): Response
    {
        $this->gameService->createGame(AbstractGameManagerService::MYR_LABEL);
        return $this->redirectToRoute('app_game_myrmes_list');
    }

    #[Route('/game/myrmes/join/{id}', name: 'app_game_myrmes_join')]
    public function joinMyrmesGame(GameMYR $game): Response
    {
        $user = $this->getUser();
        $value = $this->gameService->joinGame($game->getId(), $user);
        if ($value != AbstractGameManagerService::SUCCESS) {
            throw new Exception($value);
        }
        return $this->redirectToRoute('app_game_myrmes_list');
    }

    #[Route('/game/myrmes/leave/{id}', name: 'app_game_myrmes_quit')]
    public function quitMyrmesGame(GameMYR $game): Response
    {
        $user = $this->getUser();
        $value = $this->gameService->quitGame($game->getId(), $user);
        if ($value != AbstractGameManagerService::SUCCESS) {
            throw new Exception($value);
        }
        return $this->redirectToRoute('app_game_myrmes_list');
    }

    #[Route('/game/myrmes/delete/{id}', name: 'app_game_myrmes_delete')]
    public function deleteMyrmesGame(GameMYR $game): Response
    {
        $value = $this->gameService->deleteGame($game->getId());
        if ($value != AbstractGameManagerService::SUCCESS) {
            throw new Exception($value);
        }
        return $this->redirectToRoute('app_game_myrmes_list');
    }

    #[Route('/game/myrmes/launch/{id}', name: 'app_game_myrmes_launch')]
    public function launchMyrmesGame(GameMYR $game): Response
    {
        $value = $this->gameService->launchGame($game->getId());
        if ($value != AbstractGameManagerService::SUCCESS) {
            throw new Exception($value);
        }
        return $this->redirectToRoute('app_game_myrmes_list');
    }



    private function doRandomly(callable $call): void
    {
        $random = rand(0, 1);
        if ($random == 1) {
            call_user_func($call);
        }
    }
}
