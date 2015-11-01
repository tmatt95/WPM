<?php

/**
 * Change Part Qty Form
 * 
 * PHP version 5.6
 * 
 * @category WPM
 * @package  Part
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  GIT: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */

namespace AppBundle\Form\Parts;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Change Part Qty Form
 * 
 * PHP version 5.6
 * 
 * @category WPM
 * @package  Part
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  Release: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */
class PartChange extends AbstractType
{
    /**
     * Options for form
     * @param OptionsResolver $resolver form options
     * @return nothing
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\PartChange',
            'csrf_field_name' => '_token_part_change',
            )
        );
    }

    /**
     * This is where the form elements are contained
     * @param      FormBuilderInterface $builder form builder
     * @param      array                $options for the form
     * @return     nothing
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type', 'choice', array(
                'choices' => ['Add', 'Use'],
                'required' => false,
                'label' => 'Add or Use',
                )
            )
            ->add(
                'no_added',
                'integer',
                array(
                    'label' => 'No Added',
                    'attr' => array('placeholder' => 'No Added')
                )
            )
            ->add(
                'no_taken',
                'integer',
                array(
                    'label' => 'No Removed',
                    'attr' => array('placeholder' => 'No Removed')
                )
            )
            ->add(
                'comment',
                'textarea', array('label' => 'Comment')
            )
            ->add(
                'save',
                'submit',
                array(
                    'label' => 'Update',
                    'attr' => array('class' => 'btn btn-block btn-default')
                )
            );
    }

    /**
     * Get the form name
     *
     * @inheritdoc
     * @return     nothing
     */
    public function getName()
    {
        return 'parttype_changeqty';
    }
}
