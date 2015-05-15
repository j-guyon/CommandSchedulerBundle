<?php

namespace JMose\CommandSchedulerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JMose\CommandSchedulerBundle\Form\CommandChoiceList;

/**
 * Class ScheduledCommandType
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Form\Type
 */
class ScheduledCommandType extends AbstractType
{

    /**
     * @var CommandChoiceList
     */
    private $choiceListService;

    /**
     * @param CommandChoiceList $choiceListService
     */
    public function __construct(CommandChoiceList $choiceListService)
    {
        $this->choiceListService = $choiceListService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');

        $builder->add(
            'name', 'text', array(
                'label'    => 'detail.name',
                'required' => true
            )
        );

        $builder->add(
            'command', 'choice', array(
                'choice_list' => $this->choiceListService,
                'label'       => 'detail.command',
                'required'    => true
            )
        );

        $builder->add(
            'arguments', 'text', array(
                'label'    => 'detail.arguments',
                'required' => false
            )
        );

        $builder->add(
            'cronExpression', 'text', array(
                'label'    => 'detail.cronExpression',
                'required' => true
            )
        );

        $builder->add(
            'logFile', 'text', array(
                'label'    => 'detail.logFile',
                'required' => true
            )
        );

        $builder->add(
            'priority', 'integer', array(
                'label'      => 'detail.priority',
                'empty_data' => 0,
                'required'   => false
            )
        );

        $builder->add(
            'executeImmediately', 'checkbox', array(
                'label'    => 'detail.executeImmediately',
                'required' => false
            )
        );

        $builder->add(
            'disabled', 'checkbox', array(
                'label'    => 'detail.disabled',
                'required' => false
            )
        );

        $builder->add(
            'save', 'submit', array(
                'label' => 'detail.save',
            )
        );

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => 'JMose\CommandSchedulerBundle\Entity\ScheduledCommand',
                'wrapper_attr'       => 'default_wrapper',
                'translation_domain' => 'JMoseCommandScheduler'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'command_scheduler_detail';
    }

}
