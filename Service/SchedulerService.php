<?php

namespace JMose\CommandSchedulerBundle\Service;

use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use Cron\CronExpression as CronExpressionLib;
use JMose\CommandSchedulerBundle\Exception\CommandNotFoundException;

/**
 * Provider simplified access to Schedule Commands (ON-Demand)
 *
 * @author Carlos Sosa
 */
class SchedulerService
{

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     *
     * @var string
     */
    private $commandName;

    /**
     *
     * @var ScheduledCommand
     */
    private $command;

    /**
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /** Aliases */

    /**
     * @param $commandName
     * @return SchedulerService
     */
    public function command($commandName)
    {
        return $this->cmd($commandName);
    }

    /**
     * @param $commandName
     * @return SchedulerService
     */
    public function get($commandName)
    {
        return $this->cmd($commandName);
    }

    /**
     * Set command to handle
     *
     * @param string $commandName
     * @return SchedulerService A copy of SchedulerService
     */
    public function cmd($commandName)
    {
        $this->commandName = $commandName;

        return clone $this;
    }

    /**
     * Check if command exists
     *
     * @return bool
     * @throws \ErrorException
     */
    public function exists()
    {
        try {
            if ($this->getCommand()) {
                return true;
            }
        } catch (CommandNotFoundException $e) {
            return false;
        }
    }


    /**
     * Schedule to Run in next cycle
     *
     * @throws \ErrorException
     */
    public function run()
    {
        return $this->commandAction('run');
    }

    /**
     * Prevent from run in next cycle
     *
     * @return SchedulerService
     * @throws \ErrorException
     */
    public function stop()
    {
        return $this->commandAction('stop');
    }

    /**
     * Disable command
     *
     * @return SchedulerService
     * @throws \ErrorException
     */
    public function disable()
    {
        return $this->commandAction('disable');
    }

    /**
     * Enable command
     *
     * @return SchedulerService
     * @throws \ErrorException
     */
    public function enable()
    {
        return $this->commandAction('enable');
    }

    /**
     * Change to On Demand command
     *
     * @return SchedulerService
     * @throws \ErrorException
     */
    public function setOnDemand()
    {
        return $this->commandAction(ScheduledCommand::MODE_ONDEMAND);
    }

    /**
     * Change to Cron Schedule Command (Auto)
     *
     * @param null $newCronExpression
     * @return SchedulerService
     * @throws CommandNotFoundException
     * @throws \ErrorException
     */
    public function setAuto( $newCronExpression = null)
    {
        if ( $newCronExpression ) {
            $this->getCommand()->setCronExpression( $newCronExpression);
        }

        return $this->commandAction(ScheduledCommand::MODE_AUTO);
    }

    /**
     * Command Statuses
     */

    /**
     * Return true if last exec code is -1
     *
     * @return bool
     * @throws \ErrorException
     */
    public function isFailing()
    {
        return $this->commandStatus('failing');
    }

    /**
     * True if command is locked or is scheduled to run in next cycle
     *
     * @return bool
     * @throws \ErrorException
     */
    public function isRunning()
    {
        return $this->commandStatus('running');
    }

    /**
     * True if command is not locked and it is not scheduled to run in next cycle
     *
     * @return bool
     * @throws \ErrorException
     */
    public function isStopped()
    {
        return $this->commandStatus('stopped');
    }

    /**
     * True if command is disabled
     *
     * @return bool
     * @throws \ErrorException
     */
    public function isDisabled()
    {
        return $this->commandStatus('disabled');
    }

    /**
     * True if command is enabled
     *
     * @return bool
     * @throws \ErrorException
     */
    public function isEnabled()
    {
        return $this->commandStatus('enabled');
    }

    /**
     * True if it is an On-Demand command
     *
     * @return bool
     * @throws \ErrorException
     */
    public function isOnDemand()
    {
        return $this->commandStatus(ScheduledCommand::MODE_ONDEMAND);
    }

    /**
     * True if it is not an On-Demand Command
     *
     * @return bool
     * @throws \ErrorException
     */
    public function isAuto()
    {
        return $this->commandStatus(ScheduledCommand::MODE_AUTO);
    }

    /**
     *
     * @return ScheduledCommand
     * @throws CommandNotFoundException
     * @throws \ErrorException
     */
    private function getCommand()
    {
        if ($this->command) {
            return $this->command;
        }

        if (!$this->commandName) {
            throw new \ErrorException('Missing Command Name.');
        }

        $cmd = $this->doctrine->getRepository(ScheduledCommand::class)->findOneBy([
            'name' => $this->commandName
        ]);

        if ($cmd instanceof ScheduledCommand) {
            return $cmd;
        }

        throw new CommandNotFoundException($this->commandName);
    }


    /**
     * This give access to control command behavioral
     *
     * @param string $action
     * @return SchedulerService
     * @throws \ErrorException
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    private function commandAction($action)
    {
        $cmd = $this->getCommand();

        switch ($action) {
            case 'run':
                $cmd->setExecuteImmediately(true);
                break;
            case 'stop':
                $cmd->setExecuteImmediately(false);
                break;
            case 'disable':
                $cmd->setDisabled(true);
                break;
            case 'enable':
                $cmd->setDisabled(false);
                break;
            case ScheduledCommand::MODE_ONDEMAND:
                $cmd->setExecutionMode(ScheduledCommand::MODE_ONDEMAND);
                break;
            case ScheduledCommand::MODE_AUTO:
                if (CronExpressionLib::isValidExpression($cmd->getCronExpression())) {
                    $cmd->setExecutionMode(ScheduledCommand::MODE_AUTO);
                } else {
                    throw new \InvalidArgumentException('Invalid Cron Expression.');
                }
                break;
            default:
                throw new \BadMethodCallException($action . ' is not a valid operation.');
        }

        // Persist changes to DDBB
        $this->doctrine->getManager()->persist($cmd);
        $this->doctrine->getManager()->flush($cmd);

        return $this;
    }

    /**
     * @param string $status
     * @return bool
     * @throws \ErrorException
     */
    private function commandStatus($status)
    {
        $cmd = $this->getCommand();

        switch ($status) {
            case 'failing':
                return ($cmd->getLastReturnCode() == -1);
            case 'running':
                return ($cmd->isLocked() || $cmd->getExecuteImmediately());
            case 'stopped':
                return (!$cmd->isLocked() && !$cmd->getExecuteImmediately());
            case 'enabled':
                return (!$cmd->isDisabled());
            case 'disabled':
                return $cmd->isDisabled();
            case ScheduledCommand::MODE_ONDEMAND:
                return ($cmd->getExecutionMode() == ScheduledCommand::MODE_ONDEMAND);
            case ScheduledCommand::MODE_AUTO:
                return ($cmd->getExecutionMode() == ScheduledCommand::MODE_AUTO);
            default:
                throw new \InvalidArgumentException($status . ' is not a valid operation.');
        }
    }
}