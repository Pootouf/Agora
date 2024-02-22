<?php 
namespace App\Controller\Platform;

use App\Entity\Platform\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Asset\Packages;




    class ImageController extends AbstractController
    {
        public function __construct(Packages $assetPackages)
        {
            $this->assetPackages = $assetPackages; 
        }
  
    
        #[Route('/image', name: 'game_image')]
        public function gamesImages(EntityManagerInterface $entityManager): Response
        {
            // Récupérer tous les jeux depuis la base de données
            $games = $entityManager->getRepository(Game::class)->findAll();

            // Générer le HTML pour afficher les images de tous les jeux
            $html = '';
            foreach ($games as $game) {
                $imageUrl = $this->assetPackages->getUrl( $game->getImgURL()); // Utiliser asset() pour obtenir le chemin complet
                $html .= '<img src="' . $imageUrl . '" alt="Game Image"><br>';
            }
    
            // Renvoyer la réponse avec le HTML généré
            return new Response($html);
        }
    }
