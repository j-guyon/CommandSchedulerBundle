<?php

namespace JMose\CommandSchedulerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Entity\UserHost;

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

        $this->createScheduledCommand(1, 'one', 'debug:container', '--help', '@daily', 'one.log', 100, $beforeYesterday);
        $this->createScheduledCommand(2, 'two', 'debug:container', '', '@daily', 'two.log', 80, $beforeYesterday, true);
        $this->createScheduledCommand(3, 'three', 'debug:container', '', '@daily', 'three.log', 60, $today, false, true);
        $this->createScheduledCommand(4, 'four', 'debug:router', '', '@daily', 'four.log', 40, $today, false, false, true);
    }

    /**
     * Create a new ScheduledCommand in database
     *
     * @param integer $id
     * @param string $name
     * @param string $command
     * @param string $arguments
     * @param string $cronExpression
     * @param string $logFile
     * @param integer $priority
     * @param string $lastExecution
     * @param bool $locked
     * @param bool $disabled
     * @param bool $executeNow
     */
    protected function createScheduledCommand(
        $id,
        $name,
        $command,
        $arguments,
        $cronExpression,
        $logFile,
        $priority,
        $lastExecution,
        $locked = false,
        $disabled = false,
        $executeNow = false
    )
    {
        $scheduledCommand = new ScheduledCommand();
        $scheduledCommand
            ->setId($id)
            ->setName($name)
            ->setCommand($command)
            ->setArguments($arguments)
            ->setCronExpression($cronExpression)
            ->setLogFile($logFile)
            ->setPriority($priority)
            ->setLastExecution($lastExecution)
            ->setLocked($locked)
            ->setDisabled($disabled)
            ->setLastReturnCode(0)
            ->setExecuteImmediately($executeNow)
            ->setRights(null);

        $this->manager->persist($scheduledCommand);
        $this->manager->flush();
    }
}
