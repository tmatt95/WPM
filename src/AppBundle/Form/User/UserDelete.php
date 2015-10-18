<?php

namespace AppBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDelete extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_field_name' => '_token_user_delete'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');
    }

    public function getName() {
        return 'user_delete';
    }

}
