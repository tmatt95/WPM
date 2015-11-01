<?php

namespace AppBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserUpdate extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_field_name' => '_token_user_update',
            )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('name_first', 'text', array('label' => 'First Name', 'attr' => array('placeholder' => 'First Name')))
            ->add('name_last', 'text', array('label' => 'Last Name', 'attr' => array('placeholder' => 'Last Name')))
            ->add('email', 'email')
            ->add('update', 'submit', array('label' => 'Update Info'));
    }

    public function getName()
    {
        return 'user';
    }
}
