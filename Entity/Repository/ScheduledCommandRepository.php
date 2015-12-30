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
     * @param bool $superuser set to true to read superuser superuser commands (testing only)
     *
     * @return ScheduledCommand[]
     */
    public function findEnabledCommand($superuser = false)
    {
        return $this->findBy(
            array(// criteria
                'disabled' => false,
                'locked' => false,
                'superuser' => $superuser
            ),
            array('priority' => 'DESC') // ordering
        );
    }

    /**
     * findAll override to implement the default orderBy clause
     *
     * @param bool $superuser set to true to read superuser superuser commands (testing only)
     *
     * @return ScheduledCommand[]
     */
    public function findAll($superuser = false)
    {
        return $this->findBy(
            array('superuser' => $superuser), // criteria
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
