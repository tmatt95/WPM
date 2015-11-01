<?php

  // src/AppBundle/Form/User/User.php


namespace AppBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class User extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_field_name' => '_token_user',
            )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array('attr' => array('placeholder' => 'Username')))
            ->add('name_first', 'text', array('label' => 'First Name', 'attr' => array('placeholder' => 'First Name')))
            ->add('name_last', 'text', array('label' => 'Last Name', 'attr' => array('placeholder' => 'Last Name')))
            ->add('email', 'email', array('attr' => array('placeholder' => 'Email')))
            ->add('password', 'password', array('attr' => array('placeholder' => 'Password')))
            ->add('save', 'submit', array('label' => 'Add User'));
    }

    public function getName()
    {
        return 'user';
    }
}
