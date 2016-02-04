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

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->rights = array();

        $this->createRights();
        $this->createCommands();
    }

    /**
     * create several test commands
     */
    protected function createCommands()
    {
        $now = new \DateTime();
        $today = clone $now;
        $beforeYesterday = $now->modify('-2 days');
        $id = 1;
        $rightId = 0;

        // "regular" command
        $this->createScheduledCommand($id++, 'one', 'debug:container', '--help', '@daily', 'one.log', 100, $beforeYesterday);

        // locked command
        $this->createScheduledCommand($id++, 'two', 'debug:container', '', '@daily', 'two.log', 80, $beforeYesterday, 0, true);

        // disabled command
        $this->createScheduledCommand($id++, 'three', 'debug:container', '', '@daily', 'three.log', 60, $today, 0, false, true, true);

        // execute immediately
        $this->createScheduledCommand($id++, 'four', 'debug:router', '', '@daily', 'four.log', 40, $today, 0, false, false, true);

        // command with empty userhost with executions
        $this->createScheduledCommand($id++, 'no rights', 'schedulerTest:hello', '--trash=' . ($id - 1), '* * * * *', '', 0, $now, 1, false, false, false, true, $this->rights[$rightId++]);

        // current user
        $this->createScheduledCommand($id++, 'user only', 'schedulerTest:hello', '--trash=' . ($id - 1), '* * * * *', '', 0, null, 0, false, false, false, true, $this->rights[$rightId++]);

        // current host
        $this->createScheduledCommand($id++, 'host only', 'schedulerTest:hello', '--trash=' . ($id - 1), '* * * * *', '', 0, null, 0, false, false, false, false, $this->rights[$rightId++]);

        // current user and host
        $this->createScheduledCommand($id++, 'user and host', 'schedulerTest:hello', '--trash=' . ($id - 1), '* * * * *', '', 0, null, 0, false, false, false, false, $this->rights[$rightId++]);

        // not current user
        $this->createScheduledCommand($id++, 'not user only', 'schedulerTest:hello', '--trash=' . ($id - 1), '* * * * *', '', 0, null, 0, false, false, false, false, $this->rights[$rightId++]);

        // not current host
        $this->createScheduledCommand($id++, 'not host only', 'schedulerTest:hello', '--trash=' . ($id - 1), '* * * * *', '', 0, null, 0, false, false, false, false, $this->rights[$rightId++]);

        // not current user and host
        $this->createScheduledCommand($id++, 'not user and host', 'schedulerTest:hello', '--trash=' . ($id - 1), '* * * * *', '', 0, null, 0, false, false, false, false, $this->rights[$rightId++]);

        // locked, timeout and disabled
        $this->createScheduledCommand($id++, 'locked, timeout and disabled', 'debug:container', '--help', '* * * * *', '', 0, $beforeYesterday, 3, true, true);

        // locked, running
        $this->createScheduledCommand($id++, 'locked, running', 'debug:container', '--help', '* * * * *', '', 0, $now, 0, true);
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
     * @param integer $lastReturnCode
     * @param bool $locked
     * @param bool $disabled
     * @param bool $executeNow
     * @param bool $appendExecutions
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
        $lastReturnCode = 0,
        $locked = false,
        $disabled = false,
        $executeNow = 0,
        $appendExecutions = false,
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
            ->setLastReturnCode($lastReturnCode)
            ->setLocked($locked)
            ->setDisabled($disabled)
            ->setExecuteImmediately($executeNow)
            ->setRights($rights);

        $this->manager->persist($scheduledCommand);
        $this->manager->flush();

        if ($appendExecutions) {
            $this->createExecutions($scheduledCommand);
        }
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
        $this->createUserHost(8, 'title', 'user', 'host', 'userExcluded', 'hostExcluded', 'info');
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
        $userHost = new UserHost();
        $userHost
            ->setId($id)
            ->setUser($user)
            ->setHost($host)
            ->setUserExcluded($userExcluded)
            ->setHostExcluded($hostExcluded)
            ->setTitle($title);

        if ($info) {
            $userHost->setInfo($info);
        } else {
            $userHost->setInfo($title);
        }

        array_push($this->rights, $userHost);

        $this->manager->persist($userHost);
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
        for ($i = NUMBER_EXECUTIONS; $i; $i--) {
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
            ->setCommand($command)
            ->setOutput("foo\nbar\n");

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
