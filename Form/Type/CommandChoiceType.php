<?php

namespace JMose\CommandSchedulerBundle\Form\Type;

use JMose\CommandSchedulerBundle\Service\CommandParser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CommandChoiceType
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 */
class CommandChoiceType extends AbstractType
{

    /**
     * @var CommandParser
     */
    private $commandParser;

    /**
     * @param CommandParser $commandParser
     */
    public function __construct(CommandParser $commandParser)
    {
        $this->commandParser = $commandParser;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => $this->commandParser->getCommands()
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'command_choice';
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'choice';
    }
}
