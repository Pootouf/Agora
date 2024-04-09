<?php

namespace App\Service\Platform;

use App\Entity\Platform\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private static int $ADD_HIMSELF_ERROR = -1;
    private static int $ALREADY_CONTACT_ERROR = -2;
    private static int $NOT_A_CONTACT_ERROR = -3;
    private static int $SUCCESS = 0;
    private EntityManagerInterface $entityManagerInterface;

    public function __construct(
        EntityManagerInterface $entityManagerInterface
    )
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    // Add the user $contactId to the contact list of the user $userId
    public function addContact(int $userId, int $contactId) : int
    {
        //User can't add himself in his contact list
        if($userId == $contactId){
            return UserService::$ADD_HIMSELF_ERROR;
        }
        $userRepository = $this->entityManagerInterface->getRepository(User::class);
        $user = $userRepository->find($userId);
        $newContact = $userRepository->find($contactId);
        if($user->getContacts()->contains($newContact)){
            return UserService::$ALREADY_CONTACT_ERROR;
        }
        $user->addContact($newContact);
        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->flush();
        return UserService::$SUCCESS;
    }

    public function removeContact(int $userId, int $contactId)
    {
        $userRepository = $this->entityManagerInterface->getRepository(User::class);
        $user = $userRepository->find($userId);
        $newContact = $userRepository->find($contactId);
        if(!$user->getContacts()->contains($newContact)){
            return UserService::$NOT_A_CONTACT_ERROR;
        }
        $user->removeContact($newContact);
        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->flush();
        return UserService::$SUCCESS;
    }





}