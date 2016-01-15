<?php

namespace JMose\CommandSchedulerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserHostType, used to edit/create UserHost Requirements
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 */
class UserHostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'Symfony\Component\Form\Extension\Core\Type\HiddenType');

        $builder->add(
            'title',
            'Symfony\Component\Form\Extension\Core\Type\TextType',
            array(
                'label'    => 'rights.title',
                'required' => true
            )
        );

        $builder->add(
            'user',
            'Symfony\Component\Form\Extension\Core\Type\TextType',
            array(
                'label'    => 'rights.user',
                'required' => false
            )
        );

        $builder->add(
            'host',
            'Symfony\Component\Form\Extension\Core\Type\TextType',
            array(
                'label'    => 'rights.host',
                'required' => false
            )
        );

        $builder->add(
            'user_excluded',
            'Symfony\Component\Form\Extension\Core\Type\TextType',
            array(
                'label'    => 'rights.userExcluded',
                'required' => false
            )
        );

        $builder->add(
            'host_excluded',
            'Symfony\Component\Form\Extension\Core\Type\TextType',
            array(
                'label'    => 'rights.hostExcluded',
                'required' => false
            )
        );

        $builder->add(
            'info',
            'Symfony\Component\Form\Extension\Core\Type\TextareaType',
            array(
                'label'    => 'rights.info',
                'required' => false
            )
        );

        $builder->add(
            'save',
            'Symfony\Component\Form\Extension\Core\Type\SubmitType',
            array(
                'label' => 'action.save',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => 'JMose\CommandSchedulerBundle\Entity\UserHost',
                'wrapper_attr'       => 'default_wrapper',
                'translation_domain' => 'JMoseCommandScheduler'
            )
        );
    }
}
