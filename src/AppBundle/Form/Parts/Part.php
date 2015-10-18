<?php

// src/AppBundle/Form/Parts/Part.php

namespace AppBundle\Form\Parts;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\PartTypeRepository;

class Part extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Part',
            'csrf_field_name' => '_token_part'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $partTypes = new PartTypeRepository('AppBundle:Product');

        $builder
                ->add('name', 'text')
                ->add('description', 'textarea')
                ->add('type', 'choice', array(
                    'choices' => $partTypes->getTypeList(),
                    'required' => true,
                ))
                ->add('location', 'text')
                ->add('qty', 'integer')
                ->add('save', 'submit', array('label' => 'Add Part', attr => ['item-class' => 'btn-primary']))
        ;
    }

    public function getName() {
        return 'part';
    }

}
