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
                'label' => 'Add or Use'
            ))
            ->add('no_added', 'integer', array('label' => 'No Added', 'attr'=>array('placeholder'=>'No Added')))
            ->add('no_taken', 'integer', array('label' => 'No Removed', 'attr'=>array('placeholder'=>'No Removed')))
            ->add('comment', 'textarea', array('label' => 'Comment'))
            ->add('save', 'submit', array('label' => 'Update', 'attr'=>array('class'=>'btn btn-block btn-default')));
    }

    public function getName() {
        return 'parttype';
    }

}
