<?php

namespace JMose\CommandSchedulerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class ScheduledCommandRepository
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Entity\Repository
 */
class ScheduledCommandRepository extends EntityRepository
{

    /**
     * Find all enabled command ordered by priority
     *
     * @return array<ScheduledCommand>
     */
    public function findEnabledCommand()
    {
        $qb = $this->createQueryBuilder('command')
            ->where('command.disabled = :disabled')
            ->andwhere('command.locked = :disabled')
            ->orderBy('command.priority', 'DESC')
            ->setParameter('disabled', false);

        return $qb->getQuery()->getResult();
    }

    /**
     * findAll override to implement the default orderBy clause
     *
     * @return array<ScheduledCommand>
     */
    public function findAll()
    {
        return $this->findBy(array(), array('priority' => 'DESC'));
    }

}
