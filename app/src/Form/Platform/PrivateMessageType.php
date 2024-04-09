<?php

namespace App\Form\Platform;

use App\Entity\Platform\PrivateMessage;
use App\Entity\Platform\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrivateMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextType::class)
            ->add('recepient', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Select Contact',
                'choices' => $options['contacts'], // Pass the contacts as choices
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrivateMessage::class,
            'contacts' => []
        ]);
    }
}
