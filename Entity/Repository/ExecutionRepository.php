<?php

namespace JMose\CommandSchedulerBundle\Entity\Repository;

use JMose\CommandSchedulerBundle\Entity\Execution;

/**
 * ExecutionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ExecutionRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * find all executions fo a given command
     *
     * @param integer $commandId
     * @return array
     */
    public function findCommandExecutions($commandId)
    {
        $logs = $this->findBy(array('command' => $commandId), array('id' => 'ASC'));
        $result = array();
        /** @var Execution $log */
        foreach($logs as $log){
            array_push($result, array(
                'executionDate' => $log->getExecutionDate(),
                'runtime' =>$log->getRuntime(),
                'returnCode' => $log->getReturnCode()
            ));
        }

        return $result;
    }
}
