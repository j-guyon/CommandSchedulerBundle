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

        $now = new \DateTime();
        $today = clone $now;
        $beforeYesterday = $now->modify('-2 days');

        $this->createScheduledCommand('one', 'container:debug', '--help', '@daily', 'one.log', 100, $beforeYesterday);
        $this->createScheduledCommand('two', 'container:debug', '', '@daily', 'two.log', 80, $beforeYesterday, true);
        $this->createScheduledCommand('three', 'container:debug', '', '@daily', 'three.log',60, $today, false, true);
        $this->createScheduledCommand('four', 'router:debug', '', '@daily', 'four.log', 40, $today, false, false, true);
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