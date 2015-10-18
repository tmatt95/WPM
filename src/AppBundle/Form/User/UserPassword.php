<?php

  // src/AppBundle/Form/User/User.php
  namespace AppBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPassword extends AbstractType
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
      ->add('password', 'password', array('label'=>'New Password','attr'=>array('placeholder'=>'New Password')))
      ->add('save', 'submit', array('label' => 'Update Password'));
  }

  public function getName()
  {
    return 'userpassword';
  }
}
