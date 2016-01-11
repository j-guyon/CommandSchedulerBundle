<?php

namespace JMose\CommandSchedulerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Entity\UserHost;

/**
 * Class ScheduledCommandRepository
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 */
class ScheduledCommandRepository extends EntityRepository
{

    /**
     * Find all enabled command ordered by priority
     *
     * @return ScheduledCommand[]
     */
    public function findEnabledCommand()
    {
        return $this->findBy(
            array(// criteria
                'disabled' => false,
                'locked' => false
            ),
            array('priority' => 'DESC') // ordering
        );
    }

    /**
     * findAll override to implement the default orderBy clause
     *
     * @return ScheduledCommand[]
     */
    public function findAll()
    {
        return $this->findBy(
            array(), // criteria
            array('priority' => 'DESC') // ordering
        );
    }

    /**
     * find a command by id
     *
     * @return ScheduledCommand
     */
    public function findById($id)
    {
        return $this->findBy(array('id' => $id));
    }
}
