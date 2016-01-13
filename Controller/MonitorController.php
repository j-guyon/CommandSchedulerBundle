<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Service\MonitorService;

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

        /** @var MonitorService $monitorService */
        $monitorService = $this->get('jmose_command_scheduler.monitorService');
        $failed = $monitorService->processCommandsJSON($scheduledCommands);

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
        /** @var array $scheduledCommands */
        $scheduledCommands = $this->doctrineManager->getRepository($this->bundleName . ':ScheduledCommand')->findAll();

        $result = $this->render(
            $this->bundleName . ':List:indexCommands.html.twig',
            array('scheduledCommands' => $scheduledCommands)
        );

        return $result;
    }
}
