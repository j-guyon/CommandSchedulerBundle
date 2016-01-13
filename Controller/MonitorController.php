<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;

/**
 * Class MonitorController handle monitoring requests
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 */
class MonitorController extends BaseController
{
    /**
     * method checks if there are jobs which are enabled but did not return 0 on last execution or are locked.<br>
     * if a match is found, HTTP status 417 is sent along with an array which contains name, return code and locked-state.
     * if no matches found, HTTP status 200 is sent with an empty array
     *
     * @return JsonResponse
     */
    public function monitorAction()
    {
        $this->setManager();

        $scheduledCommands = $this->getRepository('ScheduledCommand')->findByActiveLocked();

        $timeoutValue = $this->container->getParameter('jmose_command_scheduler.lock_timeout');

        $failed = array();
        $now = time();

        /** @var ScheduledCommand $command */
        foreach ($scheduledCommands as $command) {
            // don't care about disabled commands
            if ($command->isDisabled()) {
                continue;
            }

            $executionTime = $command->getLastExecution();
            if($executionTime == null) {
                continue;
            }

            $executionTimestamp = $executionTime->getTimestamp();

            $timedOut = (($executionTimestamp + $timeoutValue) < $now);

            if (
                ($command->getLastReturnCode() != 0) || // last return code not OK
                (
                    $command->getLocked() &&
                    (
                        ($timeoutValue === false) || // don't check for timeouts -> locked is bad
                        $timedOut // check for timeouts, but (starttime + timeout) is in the past
                    )
                )
            ) {
                $failed[$command->getName()] = array(
                    'ID_SCHEDULED_COMMAND' => $command->getId(),
                    'LAST_RETURN_CODE' => $command->getLastReturnCode(),
                    'B_LOCKED' => $command->getLocked() ? 'true' : 'false',
                    'DH_LAST_EXECUTION' => $executionTime
                );
            }
        }

        $status = count($failed) > 0 ? Response::HTTP_EXPECTATION_FAILED : Response::HTTP_OK;

        $response = new JsonResponse();
        $response->setContent(json_encode($failed));
        $response->setStatusCode($status);

        return $response;
    }
}
