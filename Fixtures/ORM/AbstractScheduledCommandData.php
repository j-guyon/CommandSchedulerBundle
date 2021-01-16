<?php

namespace JMose\CommandSchedulerBundle\Fixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;

/**
 * Class LoadScheduledCommandData.
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 */
abstract class AbstractScheduledCommandData implements FixtureInterface
{
    /**
     * Create a new ScheduledCommand in database.
     *
     * @param $name
     * @param $command
     * @param $arguments
     * @param $cronExpression
     * @param $logFile
     * @param $priority
     * @param $lastExecution
     * @param bool $locked
     * @param bool $disabled
     * @param bool $executeNow
     * @param int  $lastReturnCode
     */
    protected function createScheduledCommand(
        $name, $command, $arguments, $cronExpression, $logFile, $priority, $lastExecution,
        $locked = false, $disabled = false, $executeNow = false, $lastReturnCode = null)
    {
        $scheduledCommand = new ScheduledCommand();
        $scheduledCommand
            ->setName($name)
            ->setCommand($command)
            ->setArguments($arguments)
            ->setCronExpression($cronExpression)
            ->setLogFile($logFile)
            ->setPriority($priority)
            ->setLastExecution($lastExecution)
            ->setLocked($locked)
            ->setDisabled($disabled)
            ->setLastReturnCode($lastReturnCode)
            ->setExecuteImmediately($executeNow);

        $this->getManager()->persist($scheduledCommand);
        $this->getManager()->flush();
    }

    /**
     * @return ObjectManager
     */
    abstract protected function getManager(): ObjectManager;
}
