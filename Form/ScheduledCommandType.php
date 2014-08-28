<?php

namespace JMose\CommandSchedulerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ScheduledCommandType
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Form
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
        $builder->add('lastReturnCode', 'hidden');
        $builder->add('locked', 'hidden');

        $builder->add(
            'name', 'text', array(
                'label'    => 'commandeScheduler.detail.name',
                'required' => true
            )
        );

        $builder->add(
            'command', 'choice', array(
                'choice_list' => $this->choiceListService,
                'label'       => 'commandeScheduler.detail.command',
                'required'    => true
            )
        );

        $builder->add(
            'arguments', 'text', array(
                'label'    => 'commandeScheduler.detail.arguments',
                'required' => false
            )
        );

        $builder->add(
            'cronExpression', 'text', array(
                'label'    => 'commandeScheduler.detail.cronExpression',
                'required' => true
            )
        );

        $builder->add(
            'logFile', 'text', array(
                'label'    => 'commandeScheduler.detail.logFile',
                'required' => true
            )
        );

        $builder->add(
            'priority', 'integer', array(
                'label'      => 'commandeScheduler.detail.priority',
                'empty_data' => 0,
                'required'   => false
            )
        );

        $builder->add(
            'executeImmediately', 'checkbox', array(
                'label'    => 'commandeScheduler.detail.executeImmediately',
                'required' => false
            )
        );

        $builder->add(
            'disabled', 'checkbox', array(
                'label'    => 'commandeScheduler.detail.disabled',
                'required' => false
            )
        );

        $builder->add(
            'save', 'submit', array(
                'label' => 'commandeScheduler.detail.save',
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
                'data_class'   => 'JMose\CommandSchedulerBundle\Entity\ScheduledCommand',
                'wrapper_attr' => 'default_wrapper'
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
