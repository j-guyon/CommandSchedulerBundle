<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class BaseController
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Controller
 */
abstract class BaseController extends AbstractController
{
    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getDoctrineManager()
    {
        $manager = ($this->container->hasParameter('jmose_command_scheduler.doctrine_manager'))
            ? $this->container->getParameter('jmose_command_scheduler.doctrine_manager')
            : 'default';

        return $this->getDoctrine()->getManager($manager);
    }
}
