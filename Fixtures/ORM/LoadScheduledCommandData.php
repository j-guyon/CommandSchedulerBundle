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

        $this->createScheduledCommand('one', 'debug:container', '--help', '@daily', 'one.log', 100, $beforeYesterday);
        $this->createScheduledCommand('two', 'debug:container', '', '@daily', 'two.log', 80, $beforeYesterday, true);
        $this->createScheduledCommand('three', 'debug:container', '', '@daily', 'three.log',60, $today, false, true);
        $this->createScheduledCommand('four', 'debug:router', '', '@daily', 'four.log', 40, $today, false, false, true, -1);
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
     * @param integer $lastReturnCode
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

        $this->manager->persist($scheduledCommand);
        $this->manager->flush();
    }
}