<?php

/**
 * Update User Form.
 *
 * PHP version 5.6
 *
 * @category WPM
 * @package  User
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  GIT: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */

namespace AppBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Update User Form.
 *
 * PHP version 5.6
 *
 * @category WPM
 * @package  User
 * @author   Matthew Turner <tmatt95@gmail.com>
 * @license  http://opensource.org/licenses/GPL-3.0 GPL3
 * @version  Release: <1.0.0>
 * @link     https://github.com/tmatt95/WPM/
 */
class UserUpdate extends AbstractType
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
            'data_class' => 'AppBundle\Entity\User',
            'csrf_field_name' => '_token_user_update'
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
                'username',
                'text'
            )
            ->add(
                'name_first',
                'text',
                array(
                    'label' => 'First Name',
                    'attr' => array('placeholder' => 'First Name')
                )
            )
            ->add(
                'name_last',
                'text',
                array(
                    'label' => 'Last Name',
                    'attr' => array('placeholder' => 'Last Name')
                )
            )
            ->add('email', 'email')
            ->add('update', 'submit', array('label' => 'Update Info'));
    }

    /**
     * Get the form name
     *
     * @inheritdoc
     * @return     nothing
     */
    public function getName()
    {
        return 'user_update';
    }
}
