<?php

namespace JMose\CommandSchedulerBundle\Fixtures\ORM;

use Doctrine\Persistence\ObjectManager;

/**
 * Class LoadScheduledCommandData.
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 */
class LoadScheduledCommandData extends AbstractScheduledCommandData
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $now = new \DateTime();
        $today = clone $now;
        $beforeYesterday = $now->modify('-2 days');

        $this->createScheduledCommand('one', 'debug:container', '--help', '@daily', 'one.log', 100, $beforeYesterday);
        $this->createScheduledCommand('two', 'debug:container', '', '@daily', 'two.log', 80, $beforeYesterday, true);
        $this->createScheduledCommand('three', 'debug:container', '', '@daily', 'three.log', 60, $today, false, true);
        $this->createScheduledCommand('four', 'debug:router', '', '@daily', 'four.log', 40, $today, false, false, true, -1);
    }

    /**
     * @inheritDoc
     */
    protected function getManager(): ObjectManager
    {
        return $this->manager;
    }
}
