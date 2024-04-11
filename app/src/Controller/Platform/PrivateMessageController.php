<?php


namespace App\Controller\Platform;

use App\Entity\Game\Message;
use App\Entity\Platform\PrivateMessage;
use App\Entity\Platform\User;
use App\Form\Platform\PrivateMessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PrivateMessageController extends AbstractController
{
    #[Route('/platform/message/send', name: 'app_platform_private_message_send')]
    public function send(Request $request, EntityManagerInterface $em): Response
    {
        $message = new PrivateMessage();
        $contacts = $this->getUser()->getContacts();

        $form = $this->createForm(PrivateMessageType::class, $message, [
            'contacts' => $contacts
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            $title = $form->get('title')->getData();
            $reciever = $form->get('recepient')->getData();
            $sender = $this->getUser();
            $content = $form->get('content')->getData();
            $messageDate = new \DateTime('now');
            //udating Message Private Objet

            $message->setTitle($title);
            $reciever->addRecieved($message);
            $sender->addSent($message);
            $message->setContent($content);
            $message->setMessageTime($messageDate);

            $em->persist($message);
            $em->persist($reciever);
            $em->persist($sender);

            $em->flush();

            return $this->redirectToRoute('app_platform_private_message_send');
        }

        return $this->render('platform/private_message/send.html.twig', [
            'controller_name' => 'PrivateMessageController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/platform/message/sends', name: 'app_platform_private_message_sents')]
    public function sends(Request $request): Response
    {
        //get the message by sents;
        $messages = $this->getUser()->getSents();

        return $this->render('platform/private_message/sends.html.twig', [
            'controller_name' => 'PrivateMessageController',
            'messages' => $messages
        ]);
    }

    #[Route('/platform/message/received', name: 'app_platform_private_message_display')]
    public function received(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $messages = $user->getRecieveds();

        return $this->render('platform/private_message/received.html.twig', [
            'controller_name' => 'PrivateMessageController',
            'messages' => $messages
        ]);
    }

    #[Route('/platform/message/{id}/displayOne', name: 'app_platform_private_message_dOne')]
    public function displayMessage(Request $request, int $id, EntityManagerInterface $em): Response
    {
        $message = $em->getRepository(PrivateMessage::class)->find($id);


        return $this->render('platform/private_message/messageContent.html.twig', [
            'controller_name' => 'PrivateMessageController',
            'message' => $message
        ]);
    }
}
