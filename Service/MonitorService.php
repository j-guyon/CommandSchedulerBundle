<?php
/**
 * Service to handle processing of commands for monitoring
 */

namespace JMose\CommandSchedulerBundle\Service;

use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;

class MonitorService
{
    /** @var integer|false $timeoutValue timeout as set in config.yml */
    private $timeoutValue = false;

    /** @var integer $now timestamp when service was initialized */
    private $now = 0;

    /**
     * set timeout value
     *
     * @param integer $timeoutValue timespan until a command is considered to have failed
     *
     * @return MonitorService
     */
    public function __construct($timeoutValue)
    {
        if ($timeoutValue !== false) {
            $this->timeoutValue = $timeoutValue;
        }
        $this->now = time();
    }

    /**
     * check array of commands for timeouts and/or failed returncodes
     *
     * @param array $scheduledCommands array of commands
     *
     * @return array
     */
    public function processCommandsJSON($scheduledCommands)
    {
        $failed = array();

        /** @var ScheduledCommand $command */
        foreach ($scheduledCommands as $command) {
            // command was never executed -> ignore
            if ($command->getLastExecution() == null) {
                continue;
            }

            if (
                $this->checkCommandFailed($command)
            ) {
                $failed[$command->getName()] = $this->getFailedJSONEntry($command);
            }
        }

        return $failed;
    }

    /**
     * get entry for failed command to be used in JSON response
     *
     * @param ScheduledCommand $command command to be handled
     * @return array
     */
    private function getFailedJSONEntry($command)
    {
        return array(
            'ID_SCHEDULED_COMMAND' => $command->getId(),
            'LAST_RETURN_CODE' => $command->getLastReturnCode(),
            'B_LOCKED' => $command->getLocked() ? 'true' : 'false',
            'DH_LAST_EXECUTION' => $command->getLastExecution()
        );
    }

    /**
     * check if command has failed (returncode != 0) or locken and timeout exceeded
     *
     * @param ScheduledCommand $command
     *
     * @return bool
     */
    protected function checkCommandFailed($command)
    {
        $executionTime = $command->getLastExecution();

        $executionTimestamp = $executionTime->getTimestamp();

        $timedOut = (($executionTimestamp + $this->timeoutValue) < $this->now);

        return ($command->getLastReturnCode() != 0) || // last return code not OK
        (
            $command->getLocked() &&
            (
                ($this->timeoutValue === false) || // don't check for timeouts -> locked is bad
                $timedOut // check for timeouts, but (starttime + timeout) is in the past
            )
        );
    }
}
