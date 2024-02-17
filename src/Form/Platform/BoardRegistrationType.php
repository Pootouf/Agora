<?php

namespace App\Form\Platform;

use App\Entity\Platform\Board;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoardRegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
                'choice_label' => function($choice, $key, $value) {
                    return strval($value);
                }])
            ->add('nbInvitations', ChoiceType::class, [
                'choices' => $dummy
            ])
            ->add('invitationHash')
            ->add('inactivityHours', NumberType::class, [
                'attr' => array(
                    "min" => 24,
                    "max" => 168,
                    "step" => 1,
                    "placeholder" => "24",
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
