<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ListController
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Controller
 */
class ListController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $manager          = ($this->container->hasParameter('jmose_command_scheduler.doctrine_manager')) ? $this->container->getParameter('jmose_command_scheduler.doctrine_manager') : 'default';
        $scheduledCommands = $this->getDoctrine()->getManager($manager)->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->findAll();

        return $this->render(
            'JMoseCommandSchedulerBundle:List:index.html.twig',
            array('scheduledCommands' => $scheduledCommands)
        );
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction($id)
    {
        $manager          = ($this->container->hasParameter('jmose_command_scheduler.doctrine_manager')) ? $this->container->getParameter('jmose_command_scheduler.doctrine_manager') : 'default';
        $scheduledCommand = $this->getDoctrine()->getManager($manager)->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find($id);
        $entityManager    = $this->getDoctrine()->getManager($manager);
        $entityManager->remove($scheduledCommand);
        $entityManager->flush();

        // Add a flash message and do a redirect to the list
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('commandeScheduler.flash.deleted'));

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list'));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toggleAction($id)
    {
        $manager          = ($this->container->hasParameter('jmose_command_scheduler.doctrine_manager')) ? $this->container->getParameter('jmose_command_scheduler.doctrine_manager') : 'default';
        $scheduledCommand = $this->getDoctrine()->getManager($manager)->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find($id);
        if ($scheduledCommand->isDisabled()) {
            $scheduledCommand->setDisabled(false);
        } else {
            $scheduledCommand->setDisabled(true);
        }

        $this->getDoctrine()->getManager($manager)->flush();

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list'));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function executeAction($id)
    {
        $manager          = ($this->container->hasParameter('jmose_command_scheduler.doctrine_manager')) ? $this->container->getParameter('jmose_command_scheduler.doctrine_manager') : 'default';
        $scheduledCommand = $this->getDoctrine()->getManager($manager)->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find($id);
        $scheduledCommand->setExecuteImmediately(true);
        $this->getDoctrine()->getManager($manager)->flush();

        // Add a flash message and do a redirect to the list
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('commandeScheduler.flash.execute'));

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list'));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unlockAction($id)
    {
        $manager          = ($this->container->hasParameter('jmose_command_scheduler.doctrine_manager')) ? $this->gcontainer->getParameter('jmose_command_scheduler.doctrine_manager') : 'default';
        $scheduledCommand = $this->getDoctrine()->getManager($manager)->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find($id);
        $scheduledCommand->setLocked(false);
        $this->getDoctrine()->getManager($manager)->flush();

        // Add a flash message and do a redirect to the list
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('commandeScheduler.flash.unlocked'));

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list'));
    }
}
