<?php

namespace JMose\CommandSchedulerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use JMose\CommandSchedulerBundle\Entity\UserHost;
use JMose\CommandSchedulerBundle\Entity\Execution;

/**
 * Entity ScheduledCommand
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Entity
 */
class ScheduledCommand
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $arguments;

    /**
     * @see http://www.abunchofutils.com/utils/developer/cron-expression-helper/
     * @var string
     */
    private $cronExpression;

    /**
     * @var \DateTime
     */
    private $lastExecution;

    /**
     * @var integer
     */
    private $lastReturnCode;

    /**
     * Log's file name (without path)
     *
     * @var string
     */
    private $logFile;

    /**
     * @var integer
     */
    private $priority;

    /**
     * If true, command will be execute next time regardless cron expression
     *
     * @var boolean
     */
    private $executeImmediately;

    /**
     * @var boolean
     */
    private $disabled;

    /**
     * @var boolean
     */
    private $locked;

    /**
     * @var integer
     */
    private $expectedRuntime = 0;

    /**
     * @var ArrayCollection Execution every time a command is executed an execution is created
     */
    private $executions;

    /**
     * @var boolean should executions be logged in database
     */
    private $logExecutions = false;


    /**
     * @var UserHost $rights requirements for executing user and host
     */
    private $rights = null;

    /**
     * Init new ScheduledCommand
     */
    public function __construct()
    {
        $this->setLastExecution(new \DateTime());
        $this->setLocked(false);

        $this->executions = new ArrayCollection();
        $this->rights = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ScheduledCommand
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set command
     *
     * @param string $command
     * @return ScheduledCommand
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get arguments - If toArray is passed to true, then the argument string is transformed into an exploitable array
     *  to init and InputArgumentArray and run command
     *
     * @param bool $toArray
     * @return array|mixed
     */
    public function getArguments($toArray = false)
    {
        if (false === $toArray) {
            return $this->arguments;
        }

        $argsArray = array();
        if (null !== $this->arguments || '' != $this->arguments) {
            $flatArgsArray = explode(' ', preg_replace('/\s+/', ' ', $this->arguments));
            foreach ($flatArgsArray as $argument) {
                $tmpArray = explode('=', $argument);
                if (count($tmpArray) == 1) {
                    $argsArray[$tmpArray[0]] = true;
                } else {
                    $argsArray[$tmpArray[0]] = $tmpArray[1];
                }
            }
        }

        return $argsArray;
    }

    /**
     * Set arguments
     *
     * @param string $arguments
     * @return ScheduledCommand
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Get cronExpression
     *
     * @return string
     */
    public function getCronExpression()
    {
        return $this->cronExpression;
    }

    /**
     * Set cronExpression
     *
     * @param string $cronExpression
     * @return ScheduledCommand
     */
    public function setCronExpression($cronExpression)
    {
        $this->cronExpression = $cronExpression;

        return $this;
    }

    /**
     * Get lastExecution
     *
     * @return \DateTime
     */
    public function getLastExecution()
    {
        return $this->lastExecution;
    }

    /**
     * Set lastExecution
     *
     * @param \DateTime $lastExecution
     * @return ScheduledCommand
     */
    public function setLastExecution($lastExecution)
    {
        $this->lastExecution = $lastExecution;

        return $this;
    }

    /**
     * Get logFile
     *
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * Set logFile
     *
     * @param string $logFile
     * @return ScheduledCommand
     */
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;

        return $this;
    }

    /**
     * Get lastReturnCode
     *
     * @return integer
     */
    public function getLastReturnCode()
    {
        return $this->lastReturnCode;
    }

    /**
     * Set lastReturnCode
     *
     * @param integer $lastReturnCode
     * @return ScheduledCommand
     */
    public function setLastReturnCode($lastReturnCode)
    {
        $this->lastReturnCode = $lastReturnCode;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return ScheduledCommand
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get executeImmediately
     *
     * @return bool
     */
    public function isExecuteImmediately()
    {
        return $this->executeImmediately;
    }

    /**
     * Get executeImmediately
     *
     * @return boolean
     */
    public function getExecuteImmediately()
    {
        return $this->executeImmediately;
    }

    /**
     * Set executeImmediately
     *
     * @param $executeImmediately
     * @return ScheduledCommand
     */
    public function setExecuteImmediately($executeImmediately)
    {
        $this->executeImmediately = $executeImmediately;

        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * Get disabled
     *
     * @return boolean
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return ScheduledCommand
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Locked Getter
     *
     * @return boolean
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * locked Getter
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * locked Setter
     *
     * @param boolean $locked
     * @return $this
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }


    /**
     * get expected runtime in seconds
     *
     * @return int
     */
    public function getExpectedRuntime()
    {
        return $this->expectedRuntime;
    }

    /**
     * set expected runtime
     *
     * @param int $expectedRuntime
     */
    public function setExpectedRuntime($expectedRuntime)
    {
        $this->expectedRuntime = $expectedRuntime;
    }

    /**
     * get array with all executions since last rotation
     *
     * @return ArrayCollection
     */
    public function getExecutions()
    {
        return $this->executions;
    }

    /**
     * @param ArrayCollection $executions
     */
    public function setExecutions(ArrayCollection $executions)
    {
        $this->executions = $executions;
    }

    /**
     * get right constraints for execution of command
     *
     * @return UserHost
     */
    public function getRights()
    {
        return $this->rights;
    }

    /**
     * set user and host constraints
     *
     * @param UserHost $rights
     */
    public function setRights($rights)
    {
        $this->rights = $rights;
    }

    /**
     * check if command it to be executed under given "circumstances" (user and host matching)
     * @return boolean
     */
    public function checkRights()
    {
        // no requirements assigned -> execute always
        if (!$this->rights) {
            return true;
        }

        $result = true;

        $requiredUser = $this->rights->getUser();
        $requiredHost = $this->rights->getHost();

        $excludedUser = $this->rights->getUserExcluded();
        $excludedHost = $this->rights->getHostExcluded();

        // check user requirements
        $result = (
            $result && // not yet invalidated
            $requiredUser && // user requirements set
            preg_match("{" . $requiredUser . "}", $_SERVER['USER']) // requirement does match executing user
        );

        // check excluded user requirements
        $result = (
            $result && // not yet invalidated
            $excludedUser && // user requirements set
            !preg_match("{" . $excludedUser . "}", $_SERVER['USER']) // requirement must not match executing user
        );

        // check host requirements
        $result = (
            $result && // not yet invalidated
            $requiredHost && // host requirements set
            preg_match("{" . $requiredHost . "}", gethostname()) // requirement does match hostname
        );

        // check excluded host requirements
        $result = (
            $result && // not yet invalidated
            $excludedHost && // host requirements set
            !preg_match("{" . $excludedHost . "}", gethostname()) // requirement must not match hostname
        );

        return $result;
    }

    /**
     * @return boolean
     */
    public function logExecutions()
    {
        return $this->logExecutions;
    }

    /**
     * @param boolean $logExecutions
     */
    public function setLogExecutions($logExecutions)
    {
        $this->logExecutions = $logExecutions;
    }

    /**
     * add new Execution to collection
     * @param Execution $log
     */
    public function addLog($log)
    {
        $this->executions->add($log);
    }

    /**
     * @return Execution
     */
    public function getCurrentLog()
    {
        return $this->executions->last();
    }
}
