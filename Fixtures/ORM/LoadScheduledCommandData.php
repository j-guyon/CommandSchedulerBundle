<?php

namespace JMose\CommandSchedulerBundle\Fixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;

class LoadScheduledCommandData implements FixtureInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createScheduledCommand('one', 'twig:lint', '--help', '@daily', 'one.log', 100, new \DateTime());
        $this->createScheduledCommand('two', 'twig:lint', '', '@daily', 'two.log', 80, new \DateTime(), true);
        $this->createScheduledCommand('three', 'twig:lint', '', '@daily', 'three.log',60, new \DateTime(), false, true);
        $this->createScheduledCommand('four', 'twig:lint', '', '@daily', 'four.log', 40, new \DateTime(), false, false, true);
    }

    /**
     * Create a new ScheduledCommand in database
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
     */
    protected function createScheduledCommand(
        $name, $command, $arguments, $cronExpression, $logFile, $priority, $lastExecution,
        $locked = false, $disabled = false, $executeNow = false)
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
            ->setLastReturnCode(null)
            ->setExecuteImmediately($executeNow);

        $this->manager->persist($scheduledCommand);
        $this->manager->flush();
    }
}