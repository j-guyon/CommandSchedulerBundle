<?php

namespace JMose\CommandSchedulerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Entity\UserHost;

class LoadUserHostData implements FixtureInterface
{
    /**
     * @var ObjectManager
     * @var ObjectManager
     */
    protected $manager;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createUserHost(1, 'empty', '', '', '', '', 'nix');
        $this->createUserHost(2, 'user only', 'user', '', '', '', 'seriously');
        $this->createUserHost(3, 'host only', '', 'host', '', '', 'seriously');
        $this->createUserHost(4, 'user and host', 'user', 'host', '', '', 'seriously');
        $this->createUserHost(5, 'user excluded', '', '', 'user', '', 'seriously');
        $this->createUserHost(6, 'host excluded', '', '', '', 'host', 'seriously');
        $this->createUserHost(7, 'user and host excluded', '', '', 'user', 'host', 'seriously');
        $this->createUserHost(8, 'really complex', 'user1', 'host1', 'user2', 'host2', 'seriously');
    }

    /**
     * Create a new ScheduledCommand in database
     *
     * @param integer $id
     * @param string $title
     * @param string $user
     * @param string $host
     * @param string $userExcluded
     * @param string $hostExcluded
     * @param string $info
     */
    protected function createUserHost(
        $id,
        $title,
        $user,
        $host,
        $userExcluded,
        $hostExcluded,
        $info
    )
    {
        /** @var UserHost $userHost */
        $userHost = new UserHost();
        $userHost
            ->setId($id)
            ->setTitle($title)
            ->setUser($user)
            ->setHost($host)
            ->setUserExcluded($userExcluded)
            ->setHostExcluded($hostExcluded)
            ->setInfo($info);

        $this->manager->persist($userHost);
        $this->manager->flush();
    }
}
