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
     * @var array
     */
    private $serverList;

    /**
     * @param CommandChoiceList $choiceListService
     * @param array $serverList
     */
    public function __construct(CommandChoiceList $choiceListService, array $serverList)
    {
        $this->choiceListService    = $choiceListService;
        $this->serverList           = $serverList;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');

        $builder->add(
            'name', 'text', [
                'label'    => 'commandeScheduler.detail.name',
                'required' => true
            ]
        );

        $builder->add(
            'command', 'choice', [
                'choice_list' => $this->choiceListService,
                'label'       => 'commandeScheduler.detail.command',
                'required'    => true
            ]
        );

        $builder->add(
            'arguments', 'text', [
                'label'    => 'commandeScheduler.detail.arguments',
                'required' => false
            ]
        );

        $builder->add(
            'server', 'choice', [
            'choices'     => $this->serverList,
            'label'       => 'commandeScheduler.detail.server',
            'required'    => true
            ]
        );

        $builder->add(
            'cronExpression', 'text', [
                'label'    => 'commandeScheduler.detail.cronExpression',
                'required' => true
            ]
        );

        $builder->add(
            'logFile', 'text', [
                'label'    => 'commandeScheduler.detail.logFile',
                'required' => true
            ]
        );

        $builder->add(
            'priority', 'integer', [
                'label'      => 'commandeScheduler.detail.priority',
                'empty_data' => 0,
                'required'   => false
            ]
        );

        $builder->add(
            'executeImmediately', 'checkbox', [
                'label'    => 'commandeScheduler.detail.executeImmediately',
                'required' => false
            ]
        );

        $builder->add(
            'disabled', 'checkbox', [
                'label'    => 'commandeScheduler.detail.disabled',
                'required' => false
            ]
        );

        $builder->add(
            'save', 'submit', [
                'label' => 'commandeScheduler.detail.save',
            ]
        );

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'   => 'JMose\CommandSchedulerBundle\Entity\ScheduledCommand',
                'wrapper_attr' => 'default_wrapper'
            ]
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
