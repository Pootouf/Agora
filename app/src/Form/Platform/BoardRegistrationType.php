<?php

namespace App\Form\Platform;

use App\Entity\Platform\Board;
use App\Entity\Platform\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;

class BoardRegistrationType extends AbstractType
{
    private Security $security;
    private EntityManagerInterface $entityManager;


    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userId = $this->security->getUser()->getId();
        $allUsers = array_filter($this->entityManager->getRepository(User::class)->findAll(), function ($user) use ($userId) {
            return $user->getId() !== $userId && !$user->isAdmin();
        });
        $game = $options["game"];

        $choices = range($game->getMinPlayers(), $game->getMaxPlayers(), 1);
        //Dummy choices for nbInvitations
        $dummy = [];

        // Remplir le tableau avec les valeurs de 0 à 100
        for ($i = 0; $i <= $game->getMaxPlayers(); $i++) {
            $dummy[strval($i)] = $i;
        }
        $builder
            ->add('nbUserMax', ChoiceType::class, [
                'choices' => $choices,
                'choice_label' => function ($choice, $key, $value) {
                    return strval($value);
                }])
            ->add('nbInvitations', ChoiceType::class, [
                'choices' => $dummy
            ])
            ->add('invitedContacts', EntityType::class, [
                'placeholder' => "Liste de vos contacts",
                'class' => User::class,
                'choices' => $allUsers,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('inactivityHours', NumberType::class, [
                'attr' => array(
                    "min" => 1,
                    "max" => 7,
                    "step" => 1,
                    "placeholder" => "1",
                ),
                'html5' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Board::class,
            'game' => null
        ]);
    }
}
