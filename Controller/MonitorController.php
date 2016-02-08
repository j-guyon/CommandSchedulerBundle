<?php

namespace JMose\CommandSchedulerBundle\Controller;

use JMose\CommandSchedulerBundle\Service\MonitorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MonitorController handle monitoring requests
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 */
class MonitorController extends BaseController
{
    /** @var MonitorService */
    private $monitorService;

    /** @var integer|false */
    private $timeoutValue = false;

    /**
     * method checks if there are jobs which are enabled but did not return 0 on last execution or are locked.<br>
     * if a match is found, HTTP status 417 is sent along with an array which contains name, return code and locked-state.
     * if no matches found, HTTP status 200 is sent with an empty array
     *
     * @return JsonResponse
     */
    public function monitorAction()
    {
        $scheduledCommands = $this->getMonitoringData();

        $failed = $this->monitorService->processCommandsJSON($scheduledCommands);

        $status = count($failed) > 0 ? Response::HTTP_EXPECTATION_FAILED : Response::HTTP_OK;

        $response = new JsonResponse();
        $response->setContent(json_encode($failed));
        $response->setStatusCode($status);

        return $response;
    }

    /**
     * render monitoring results for Website
     *
     * @return Response
     */
    public function statusAction()
    {
        $scheduledCommands = $this->getMonitoringData();
        $data = $this->monitorService->processCommandsHTML($scheduledCommands);

        $result = $this->render(
            $this->bundleName . ':Tools:monitor.html.twig',
            array('failed' => $data)
        );

        return $result;
    }

    /**
     * get commands and init service
     *
     * @return array
     */
    private function getMonitoringData()
    {
        $this->timeoutValue = $this->container->getParameter('jmose_command_scheduler.lock_timeout');

        /** @var MonitorService $monitorService */
        $this->monitorService = $this->get('jmose_command_scheduler.monitorService');

        $this->setManager();

        $scheduledCommands = $this->getRepository('ScheduledCommand')->findFailedAndTimeoutCommands($this->timeoutValue);

        return $scheduledCommands;
    }
}
