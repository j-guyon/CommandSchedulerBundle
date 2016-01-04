<?php

namespace JMose\CommandSchedulerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ScheduledCommandType
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Form\Type
 */
class ScheduledCommandType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class);

        $builder->add(
            'name', TextType::class, array(
                'label' => 'detail.name',
                'required' => true
            )
        );

        $builder->add(
            'command', CommandChoiceType::class, array(
                'label' => 'detail.command',
                'required' => true
            )
        );

        $builder->add(
            'arguments', TextType::class, array(
                'label' => 'detail.arguments',
                'required' => false
            )
        );

        $builder->add(
            'cronExpression', TextType::class, array(
                'label' => 'detail.cronExpression',
                'required' => true
            )
        );

        $builder->add(
            'logFile', TextType::class, array(
                'label' => 'detail.logFile',
                'required' => false
            )
        );

        $builder->add(
            'priority', IntegerType::class, array(
                'label' => 'detail.priority',
                'empty_data' => 0,
                'required' => false
            )
        );

        $builder->add(
            'executeImmediately', CheckboxType::class, array(
                'label' => 'detail.executeImmediately',
                'required' => false
            )
        );

        $builder->add(
            'disabled', CheckboxType::class, array(
                'label' => 'detail.disabled',
                'required' => false
            )
        );

        $builder->add(
            'save', SubmitType::class, array(
                'label' => 'detail.save',
            )
        );

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'JMose\CommandSchedulerBundle\Entity\ScheduledCommand',
                'wrapper_attr' => 'default_wrapper',
                'translation_domain' => 'JMoseCommandScheduler'
            )
        );
    }

    /**
     * Fields prefix
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'command_scheduler_detail';
    }
}
