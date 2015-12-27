<?php

namespace JMose\CommandSchedulerBundle\Form\Type;

use JMose\CommandSchedulerBundle\Service\CommandParser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
    public function configureOptions(OptionsResolver $resolver)
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
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }
}
