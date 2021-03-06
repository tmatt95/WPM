<?php

/**
 * Update Part Type Form
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
 * Update Part Type Form
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
class PartTypeUpdate extends AbstractType
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
            'data_class' => 'AppBundle\Entity\PartType',
            'csrf_field_name' => '_token_part_type',
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
            ->add('name', 'text')
            ->add('description', 'textarea', array('required'=>false))
            ->add('save', 'submit', array('label' => 'Save'));
    }

    /**
     * Get the form name
     *
     * @inheritdoc
     * @return     nothing
     */
    public function getName()
    {
        return 'parttype_update';
    }
}
