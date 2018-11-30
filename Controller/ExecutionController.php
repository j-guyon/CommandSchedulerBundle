<?php
/**
 * Class ExecutionController
 *
 * @author Daniel Fischer <dfischer000@gmail.com>
 */

namespace JMose\CommandSchedulerBundle\Controller;

use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Entity\Execution;
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

    /**
     * get data for execution output
     *
     * @param int $id Execution id
     * @return Response
     */
    public function getOutputAction($id) {
        /** @var ExecutionRepository $repo */
        $repo = $this->getRepository('Execution');
        /** @var Execution $execution */
        $execution = $repo->find($id);

        /** @var ScheduledCommand $command */
        $command = $execution->getCommand();

        $data = array(
            'commandName' => $command->getName(),
            'executionDate' => $execution->getExecutionDate(),
            'output' => nl2br($execution->getOutput())
        );

        return $this->render(
            'JMoseCommandSchedulerBundle:Detail:executionOutput.html.twig',
            $data
        );
    }
}
