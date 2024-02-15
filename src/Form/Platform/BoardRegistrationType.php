<?php

namespace App\Form\Platform;

use App\Entity\Platform\Board;
use App\Entity\Platform\User;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoardRegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $game = $options["game"];

        $nbOfPlayers = [];
        // Générer les choix de 1 à n
        for ($i = $game->getMinPlayers(); $i <= $game->getMaxPlayers(); $i++) {
            $nbOfPlayers[] = $i;
        }
        $builder
            ->add('nbUserMax', ChoiceType::class, [
                'choices' => $nbOfPlayers,
            ])
            ->add('nbInvitations', ChoiceType::class, [
                'choices' => $nbOfPlayers,
                ])
            ->add('invitationHash')
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
