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
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\User'
    ));
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('username', 'text')
      ->add('name_first', 'text')
      ->add('name_last', 'text')
      ->add('email', 'email')
      ->add('password', 'password')
      ->add('save', 'submit', array('label' => 'Add User'));
  }

  public function getName()
  {
    return 'user';
  }
}
