<?php

namespace JMose\CommandSchedulerBundle\Form\Type;

use JMose\CommandSchedulerBundle\Entity\UserHost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ScheduledCommandType
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @author  Daniel Fischer <dfischer000@gmail.com>
 * 
 * @package JMose\CommandSchedulerBundle\Form\Type
 */
class ScheduledCommandType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'Symfony\Component\Form\Extension\Core\Type\HiddenType');

        $builder->add(
            'name',
            'Symfony\Component\Form\Extension\Core\Type\TextType',
            array(
                'label' => 'detail.name',
                'required' => true
            )
        );

        $builder->add(
            'command',
            'JMose\CommandSchedulerBundle\Form\Type\CommandChoiceType',
            array(
                'label' => 'detail.command',
                'required' => true,
                'choices_as_values' => true
            )
        );

        $builder->add(
            'arguments',
            'Symfony\Component\Form\Extension\Core\Type\TextType',
            array(
                'label' => 'detail.arguments',
                'required' => false
            )
        );

        $builder->add(
            'cronExpression',
            'Symfony\Component\Form\Extension\Core\Type\TextType',
            array(
                'label' => 'detail.cronExpression',
                'required' => true
            )
        );

        $builder->add(
            'logFile',
            'Symfony\Component\Form\Extension\Core\Type\TextType',
            array(
                'label' => 'detail.logFile',
                'required' => false
            )
        );

        $builder->add(
            'rights',
            'JMose\CommandSchedulerBundle\Form\Type\UserHostChoiceType',
            array(
                // use object as value
                'choices_as_values' => true,
                // anonymous function to build labels from object
                'choice_label' => function (UserHost $right, $key, $index) {
                    /** @var UserHost $right */
                    $user = (($user = $right->getUser()) ? $user : '*');
                    $host = (($host = $right->getHost()) ? $host : '*');

                    $val = $right->getTitle();

                    // if user or host are set append to title
                    // output similar to mysql syntax
                    if(($user != '*') || ($host != '*')){
                        $val = sprintf("%s (%s@%s)",
                            $right->getTitle(),
                            $user,
                            $host
                        );
                    }

                    return $val;
                },
                'label' => 'detail.rights',
                'required' => false
            )
        );

        $builder->add(
            'priority',
            'Symfony\Component\Form\Extension\Core\Type\IntegerType',
            array(
                'label' => 'detail.priority',
                'empty_data' => 0,
                'required' => false
            )
        );

        $builder->add(
            'expectedRuntime',
            'Symfony\Component\Form\Extension\Core\Type\IntegerType',
            array(
                'label' => 'detail.expectedRuntime',
                'required' => false
            )
        );

        $builder->add(
            'executeImmediately',
            'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
            array(
                'label' => 'detail.executeImmediately',
                'required' => false
            )
        );

        $builder->add(
            'disabled',
            'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
            array(
                'label' => 'detail.disabled',
                'required' => false
            )
        );

        $builder->add(
            'logExecutions',
            'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
            array(
                'label' => 'detail.logExecutions',
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
     *
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
}
