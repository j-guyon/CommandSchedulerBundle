<?php

namespace JMose\CommandSchedulerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Entity\UserHost;

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
     * @return ScheduledCommand[]
     */
    public function findEnabledCommand()
    {
        return $this->findBy(
            array( // criteria
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

    /**
     * Find all locked commands
     *
     * @return ScheduledCommand[]
     */
    public function findLockedCommand()
    {
        return $this->findBy(array('disabled' => false, 'locked' => true), array('priority' => 'DESC'));
    }

    /**
     * Find all failed command
     *
     * @return ScheduledCommand[]
     */
    public function findFailedCommand()
    {
        return $this->createQueryBuilder('command')
            ->where('command.disabled = false')
            ->andWhere('command.lastReturnCode != 0')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param integer|bool $lockTimeout
     * @return array|\JMose\CommandSchedulerBundle\Entity\ScheduledCommand[]
     */
    public function findFailedAndTimeoutCommands($lockTimeout = false)
    {
        // Fist, get all failed commands (return != 0)
        $failedCommands = $this->findFailedCommand();

        // Then, si a timeout value is set, get locked commands and check timeout
        if (false !== $lockTimeout) {
            $lockedCommands = $this->findLockedCommand();
            foreach ($lockedCommands as $lockedCommand) {
                $now = time();
                if ($lockedCommand->getLastExecution()->getTimestamp() + $lockTimeout < $now) {
                    $failedCommands[] = $lockedCommand;
                }
            }
        }

        return $failedCommands;
    }
    /**
     * find all locked, active commands for monitoring
     *
     * @return ScheduledCommand[]
     */
/*    public function findByActiveLocked()
    {
        $commands = $this->findBy(
            array( // criteria
                'disabled' => false
            ),
            array('priority' => 'DESC') // ordering
        );

        // keep only locked commands or commands with last returncode != 0
        $commands = array_filter($commands, function($command){

            return
                $command->isLocked() ||
                ($command->getLastReturnCode() != 0);
        });

        return $commands;
    }
*/
}
