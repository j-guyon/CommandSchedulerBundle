<?php

namespace JMose\CommandSchedulerBundle\Event;

use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PreExecuteScheduledCommandEvent
 *
 * @author Mindaugas Mačiulaitis <napas@napas.lt>
 * @package JMose\CommandSchedulerBundle\Event
 */
class PreExecuteScheduledCommandEvent extends Event
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var ScheduledCommand
     */
    private $scheduledCommand;

    /**
     * @param InputInterface $input
     * @param ScheduledCommand $scheduledCommand
     */
    public function __construct(InputInterface $input, ScheduledCommand $scheduledCommand)
    {
        $this->input = $input;
        $this->scheduledCommand = $scheduledCommand;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param InputInterface $input
     * @return $this
     */
    public function setInput($input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * @return ScheduledCommand
     */
    public function getScheduledCommand()
    {
        return $this->scheduledCommand;
    }

    /**
     * @param ScheduledCommand $scheduledCommand
     * @return $this
     */
    public function setScheduledCommand($scheduledCommand)
    {
        $this->scheduledCommand = $scheduledCommand;

        return $this;
    }
}