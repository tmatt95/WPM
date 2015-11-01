<?php

namespace AppBundle\Form\Locations;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationNote extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\LocationNote',
            'csrf_field_name' => '_token_location_notes',
            )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('notes', 'textarea')
            ->add('save', 'submit', array('label' => 'Add'));
    }

    public function getName()
    {
        return 'location_notes';
    }
}
