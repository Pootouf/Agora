<?php

namespace App\Form\Platform;

use App\Data\SearchUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Joueur' => 'ROLE_USER',
                    'Modérateur' => 'ROLE_MODERATOR',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'required' => false,
                'placeholder' => 'Sélectionner le rôle',
            ])
            ->add('isbanned', ChoiceType::class, [
                'choices' => [
                    'Non banni' => false,
                    'Banni' => true
                ],
                'required' => false,
                'placeholder' => 'Selectionnez le bannissement',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchUser::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
