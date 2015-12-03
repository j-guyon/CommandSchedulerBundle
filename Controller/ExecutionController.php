<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 02.12.15
 * Time: 18:56
 */

namespace JMose\CommandSchedulerBundle\Controller;

use JMose\CommandSchedulerBundle\Controller\BaseController;
use JMose\CommandSchedulerBundle\Entity\Repository\ExecutionRepository;
use JMose\CommandSchedulerBundle\Entity\Repository\ScheduledCommandRepository;

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
        $repo = $this->doctrineManager->getRepository($this->bundleName . ':Execution');
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