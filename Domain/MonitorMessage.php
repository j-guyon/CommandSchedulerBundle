<?php
/**
 * Created by PhpStorm.
 * User: cjohnson
 * Date: 5/18/17
 * Time: 1:15 PM
 */

namespace JMose\CommandSchedulerBundle\Domain;

class MonitorMessage
{
    /**
     * @var $commandName string The name of the command which failed
     */
    private $commandName;
    /**
     * @var $commandLastReturnCode int The return code of the command which failed
     */
    private $commandLastReturnCode;
    /**
     * @var $commandLocked boolean A boolean which says if the command is locked or not
     */
    private $commandLocked;
    /**
     * @var $commandLastExecuted \DateTime The date and time the command was last executed
     */
    private $commandLastExecuted;

    public function __construct($commandName, $commandLastReturnCode, $commandLocked, $commandLastExecuted)
    {
        $this->setCommandName($commandName);
        $this->setCommandLastReturnCode($commandLastReturnCode);
        $this->setCommandLocked($commandLocked);
        $this->setCommandLastExecuted($commandLastExecuted);
    }

    /**
     * @return string
     */
    public function getCommandName()
    {
        return $this->commandName;
    }

    /**
     * @param string $commandName
     */
    public function setCommandName($commandName)
    {
        $this->commandName = $commandName;
    }

    /**
     * @return int
     */
    public function getCommandLastReturnCode()
    {
        return $this->commandLastReturnCode;
    }

    /**
     * @param int $commandLastReturnCode
     */
    public function setCommandLastReturnCode($commandLastReturnCode)
    {
        $this->commandLastReturnCode = $commandLastReturnCode;
    }

    /**
     * @return bool
     */
    public function isCommandLocked()
    {
        return $this->commandLocked;
    }

    /**
     * @param bool $commandLocked
     */
    public function setCommandLocked($commandLocked)
    {
        $this->commandLocked = $commandLocked;
    }

    /**
     * @return \DateTime
     */
    public function getCommandLastExecuted()
    {
        return $this->commandLastExecuted;
    }

    /**
     * @param \DateTime $commandLastExecuted
     */
    public function setCommandLastExecuted(\DateTime $commandLastExecuted)
    {
        $this->commandLastExecuted = $commandLastExecuted;
    }

    public function __toString()
    {
        return sprintf("%s: returncode %s, locked: %s, last execution: %s\n", $this->getCommandName(), $this->getCommandLastReturnCode(), $this->isCommandLocked(), $this->getCommandLastExecuted()->format('Y-m-d H:i'));
    }
}