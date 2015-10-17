<?php

  // src/AppBundle/Form/Parts/PartType.php
  namespace AppBundle\Form\Parts;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartType extends AbstractType
{

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\PartType',
    ));
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('name', 'text')
      ->add('description', 'textarea')
      ->add('save', 'submit', array('label' => 'Add Part Type'));
  }

  public function getName()
  {
    return 'parttype';
  }
}
