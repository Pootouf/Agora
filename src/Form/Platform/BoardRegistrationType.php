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

        $choices = range($game->getMinPlayers(), $game->getMaxPlayers(), 1);
        $choice_label = array_map('strval', $choices);
        $builder
            ->add('nbUserMax', ChoiceType::class, [
                'choices' => $choices,
                'choice_label' => function($choice, $key, $value) {
                    return strval($value);
                }])
            ->add('nbInvitations', ChoiceType::class, [
                'choices' => [0]
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
