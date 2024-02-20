<?php

namespace App\Form\Platform;

use App\Entity\Game\DTO\Game;
use App\Entity\Platform\Board;
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
                    'Teerminé' => 'FINISH'
                ]
            ])
            ->add('availability', ChoiceType::class, [
                'choices' => [
                    'Disponible' => 'OPEN',
                    'Indisponible' => 'CLOSE'
                ]
            ])
            ->add('creationdate', DateType::class, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Board::class,
        ]);
    }
}