<?php

// src/AppBundle/Form/Parts/PartType.php


namespace AppBundle\Form\Parts;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartTypeUpdate extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\PartType',
            'csrf_field_name' => '_token_part_type',
            )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('description', 'textarea')
            ->add('save', 'submit', array('label' => 'Save'));
    }

    public function getName()
    {
        return 'parttype';
    }
}
