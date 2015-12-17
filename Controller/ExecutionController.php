<?php
/**
 * Class ExecutionController
 *
 * @author Daniel Fischer <dfischer000@gmail.com>
 */

namespace JMose\CommandSchedulerBundle\Controller;

use JMose\CommandSchedulerBundle\Entity\Repository\ExecutionRepository;
use JMose\CommandSchedulerBundle\Entity\Repository\ScheduledCommandRepository;
use Symfony\Component\HttpFoundation\Response;

class ExecutionController extends BaseController
{
    /**
     * show list of all previous executions
     * @param integer $commandId
     * @return Response
     */
    public function showCommandExecutionsAction($commandId)
    {
        /** @var ExecutionRepository $repo */
        $repo = $this->getRepository('Execution');
        $executions = $repo->findCommandExecutions($commandId);
        /** @var ScheduledCommandRepository $repo */
        $repo = $this->doctrineManager->getRepository($this->bundleName . ':ScheduledCommand');
        $command = $repo->findById($commandId);

        return $this->render(
            'JMoseCommandSchedulerBundle:List:commandExecutions.html.twig', array(
                'executions' => $executions,
                'command' => array_shift($command)
            )
        );
    }
}
