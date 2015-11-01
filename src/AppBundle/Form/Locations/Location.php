<?php

namespace AppBundle\Form\Locations;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Location extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\Location',
            'csrf_field_name' => '_token_location',
            )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text')
            ->add('description', 'textarea')
            ->add('save', 'submit', array('label' => 'Save'));
    }

    public function getName()
    {
        return 'location';
    }
}
