<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Displays the homepage
     * 
     * @return Response HTTP response: Homepage
     */
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $gameRepository = $entityManager->getRepository(Game::class);

        $games = $gameRepository->findAll();

        return $this->render('platform/home/index.html.twig', [
            'controller_name' => 'HomeController',
            'games' => $games,
        ]);
    }
}
