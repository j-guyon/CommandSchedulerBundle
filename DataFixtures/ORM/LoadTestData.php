<?php

namespace JMose\CommandSchedulerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use JMose\CommandSchedulerBundle\Entity\Execution;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Entity\UserHost;

class LoadTestData implements FixtureInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /** @var array */
    protected $rights = array();

    protected $executions = array();

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createRights();
        $this->createCommands();
    }

    protected function createCommands()
    {
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
     * @param mixed $rights
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
        $executeNow = false,
        $rights = null
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
            ->setRights($rights);

        $this->manager->persist($scheduledCommand);
        $this->manager->flush();

        $this->createExecutions($scheduledCommand);
    }

    /**
     * create UserHost requirements
     */
    protected function createRights()
    {
        $currentHostname = gethostname();
        $currentUser = $this->getUsername();

        $this->createUserHost(1, 'empty', '', '', '', '');
        $this->createUserHost(2, 'user only', $currentUser, '', '', '');
        $this->createUserHost(3, 'host only', '', $currentHostname, '', '');
        $this->createUserHost(4, 'user and host', $currentUser, $currentHostname, '', '');
        $this->createUserHost(5, 'user excluded', '', '', $currentUser, '');
        $this->createUserHost(6, 'host excluded', '', '', '', $currentHostname);
        $this->createUserHost(7, 'user and host excluded', '', '', $currentUser, $currentHostname);
    }

    /**
     * Create a new UserHost in database
     *
     * @param integer $id
     * @param string $title
     * @param string $user
     * @param string $host
     * @param string $userExcluded
     * @param string $hostExcluded
     * @param mixed $info
     */
    protected function createUserHost(
        $id,
        $title,
        $user,
        $host,
        $userExcluded,
        $hostExcluded,
        $info = false
    )
    {
        /** @var UserHost $userHost */
        $rights = new UserHost();
        $rights
            ->setId($id)
            ->setUser($user)
            ->setHost($host)
            ->setUserExcluded($userExcluded)
            ->setHostExcluded($hostExcluded)
            ->setTitle($title);

        if ($info) {
            $rights->setInfo($info);
        } else {
            $rights->setInfo($title);
        }

        $this->manager->persist($rights);
        $this->manager->flush();
    }

    /**
     * create Executions for a existing command
     *
     * @param $command
     */
    protected function createExecutions($command)
    {
        $now = new \DateTime();

        $id = 1;
        for ($i = 7; $i; $i--) {
            $time = clone $now;
            $this->createExecution(
                $id++,
                $time->modify('-' . $i . ' days'),
                5 + ($i % 3),
                (($i % 4) ? 0 : 1),
                $command
            );
        }
    }

    /**
     * create new Execution in database
     *
     * @param int $id
     * @param \DateTime $executionDate
     * @param int $runtime
     * @param int $returnCode
     * @param ScheduledCommand $command
     */
    protected function createExecution($id, $executionDate, $runtime, $returnCode, $command)
    {
        $execution = new Execution();
        $execution
            ->setId($id)
            ->setExecutionDate($executionDate)
            ->setRuntime($runtime)
            ->setReturnCode($returnCode)
            ->setCommand($command);

        $this->manager->persist($execution);
        $this->manager->flush();
    }

    /**
     * get current username
     * @see http://stackoverflow.com/questions/7771586/how-to-check-what-user-php-is-running-as
     *
     * @return string
     */
    protected function getUsername()
    {
        $uid = posix_geteuid();
        $name = posix_getpwuid($uid);
        $name = $name['name'];

        return $name;
    }
}
