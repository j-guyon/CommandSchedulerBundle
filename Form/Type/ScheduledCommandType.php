<?php

namespace JMose\CommandSchedulerBundle\Form\Type;

use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
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
            'name',
            TextType::class,
            [
                'label' => 'detail.name',
                'required' => true,
            ]
        );

        $builder->add(
            'command',
            CommandChoiceType::class,
            [
                'label' => 'detail.command',
                'required' => true,
            ]
        );

        $builder->add(
            'arguments',
            TextType::class,
            [
                'label' => 'detail.arguments',
                'required' => false,
            ]
        );

        $builder->add(
            'cronExpression',
            TextType::class,
            [
                'label' => 'detail.cronExpression',
                'required' => false,
            ]
        );

        $builder->add(
            'logFile',
            TextType::class,
            [
                'label' => 'detail.logFile',
                'required' => false,
            ]
        );

        $builder->add(
            'priority',
            IntegerType::class,
            [
                'label' => 'detail.priority',
                'empty_data' => 0,
                'required' => false,
            ]
        );

        $builder->add(
            'executionMode', ChoiceType::class, array(
                'label' => 'detail.executionMode',
                'choices_as_values' => true, //This activates the "new" choice type API, which was introduced in Symfony 2.7 and it is the default in Symfony 3.x
                'choices' => [
                    'detail.executionMode.auto' => \JMose\CommandSchedulerBundle\Entity\ScheduledCommand::MODE_AUTO,
                    'detail.executionMode.ondemand' => \JMose\CommandSchedulerBundle\Entity\ScheduledCommand::MODE_ONDEMAND
                ]
            )
        );

        $builder->add(
            'executeImmediately',
            CheckboxType::class,
            [
                'label' => 'detail.executeImmediately',
                'required' => false,
            ]
        );

        $builder->add(
            'disabled',
            CheckboxType::class,
            [
                'label' => 'detail.disabled',
                'required' => false,
            ]
        );

        $builder->add(
            'save',
            SubmitType::class,
            [
                'label' => 'detail.save',
            ]
        );

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ScheduledCommand::class,
                'wrapper_attr' => 'default_wrapper',
                'translation_domain' => 'JMoseCommandScheduler',
            ]
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
