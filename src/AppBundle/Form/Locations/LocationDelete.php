<?php

namespace AppBundle\Form\Locations;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationDelete extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Location',
            'csrf_field_name' => '_token_location_delete'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('idDelete', 'hidden');
    }

    public function getName() {
        return 'location_delete';
    }

}
