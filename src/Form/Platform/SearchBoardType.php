<?php

namespace App\Form\Platform;

use App\Data\SearchData;
use App\Entity\Platform\Game;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchBoardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'IN_GAME',
                    'En attente' => 'WAITING',
                    'Terminé' => 'FINISH'
                ],
                'required' => false,
                'placeholder' => 'Sélectionner le statut',
            ])
            ->add('availability', ChoiceType::class, [
                'choices' => [
                    'Disponible' => 'OPEN',
                    'Indisponible' => 'CLOSE'
                ],
                'required' => false,
                'placeholder' => 'Sélectionner la disponibilité',
            ])
            ->add('datecreation', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'format' => 'yyyy-MM-dd',
            ])
            ->add('game', EntityType::class, [
                'class' => Game::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un jeu',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
