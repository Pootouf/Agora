<?php

namespace App\Controller\Platform;



use App\Entity\Platform\Game;
use App\Entity\Platform\Board;
use App\Entity\Platform\User;
use App\Form\Platform\BoardRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BoardController extends AbstractController
{
    #[Route('/boardCreation/{game_id}', name: 'app_board_create', requirements: ['game_id' => '\d+'], methods: ['GET','POST', 'HEAD'])]
    public function create(Request $request, $game_id, EntityManagerInterface $manager, Security $security): Response
    {
        //Find the game with the id passed in parameters
        $game = $manager->getRepository(Game::class)->find($game_id);
        //create a board
        $board = new Board();
        $form = $this->createForm(BoardRegistrationType::class, $board, [
            'game' => $game]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->isValid());

            $board->setCreationDate(new \DateTime());
            $board->setInvitationTimer(new \DateTime());
            $board->setInactivityTimer(new \DateTime());
            $userId = $security->getUser()->getId();
            $user = $manager->getRepository(User::class)->find($userId);
            $user->addBoard($board);

            $manager->persist($board);
            $manager->flush();

            $this->addFlash(
                'success',
                'La table à bien été créé ! Lien d\'invitation : '
            );

            return $this->redirectToRoute('app_home');
        }
        /*
        dump($form->isSubmitted());
        dump($form->getData());
        dump($form->getErrors(true));     // Main errors
        */

        return $this->render('platform/game/boardRegister.html.twig', [
            'game' => $game,
            'form' => $form->createView()
        ]);
    }
}
