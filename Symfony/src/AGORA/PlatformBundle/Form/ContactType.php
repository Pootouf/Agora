<?php
// src/Blogger/BlogBundle/Form/EnquiryType.php

namespace AGORA\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface; 

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('email');
        $builder->add('subject');
        $builder->add('body');
    }

    public function getName()
    {
        return 'contact';
    }
}
?>
