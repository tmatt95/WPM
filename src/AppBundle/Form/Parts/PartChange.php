<?php

// src/AppBundle/Form/Parts/PartType.php

namespace AppBundle\Form\Parts;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartChange extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PartChange',
            'csrf_field_name' => '_token_part_change'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('type', 'choice', array(
                'choices' => ['Add', 'Use'],
                'required' => false,
                'label' => 'Add / Use'
            ))
            ->add('no_added', 'text', array('label' => 'Added'))
            ->add('no_taken', 'text', array('label' => 'Used'))
            ->add('comment', 'textarea')
            ->add('save', 'submit', array('label' => 'Update'));
    }

    public function getName() {
        return 'parttype';
    }

}
