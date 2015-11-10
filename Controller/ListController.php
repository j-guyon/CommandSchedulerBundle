<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.deleted', array(), 'JMoseCommandScheduler'));

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
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.execute', array(), 'JMoseCommandScheduler'));

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list'));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unlockAction($id)
    {
        $manager          = ($this->container->hasParameter('jmose_command_scheduler.doctrine_manager')) ? $this->container->getParameter('jmose_command_scheduler.doctrine_manager') : 'default';
        $scheduledCommand = $this->getDoctrine()->getManager($manager)->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find($id);
        $scheduledCommand->setLocked(false);
        $this->getDoctrine()->getManager($manager)->flush();

        // Add a flash message and do a redirect to the list
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.unlocked', array(), 'JMoseCommandScheduler'));

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list'));
    }

    /**
     * method checks if there are jobs which are enabled but did not return 0 on last execution or are locked.<br>
     * if a match is found, HTTP status 417 is sent along with an array which contains name, return code and locked-state.
     * if no matches found, HTTP status 200 is sent with an empty array
     *
     * @return JsonResponse
     */
    public function monitorAction()
    {
        $manager          = ($this->container->hasParameter('jmose_command_scheduler.doctrine_manager')) ? $this->container->getParameter('jmose_command_scheduler.doctrine_manager') : 'default';
        $scheduledCommands = $this->getDoctrine()->getManager($manager)->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->findAll();

        $timeoutValue = $this->container->getParameter('jmose_command_scheduler.lock_timeout');

        $failed = array();
        $now = time();

        foreach($scheduledCommands as $command) {
            // don't care about disabled commands
            if($command->isDisabled()) {
                continue;
            }

            $executionTime = $command->getLastExecution();
            $executionTimestamp = $executionTime->getTimestamp();

            $timedOut = (($executionTimestamp + $timeoutValue) < $now);

            if(
                ($command->getLastReturnCode() != 0) || // last return code not OK
                (
                    $command->getLocked() &&
                    (
                        ($timeoutValue === false) || // don't check for timeouts -> locked is bad
                        $timedOut // check for timeouts, but (starttime + timeout) is in the past
                    )
                )
            ) {
                $failed[$command->getName()] = array(
                    'LAST_RETURN_CODE' => $command->getLastReturnCode(),
                    'B_LOCKED' => $command->getLocked() ? 'true' : 'false',
                    'DH_LAST_EXECUTION' => $executionTime
                );
            }
        }

        $status = count($failed) > 0 ? Response::HTTP_EXPECTATION_FAILED : Response::HTTP_OK;

        $response = new JsonResponse();
        $response->setContent(json_encode($failed));
        $response->setStatusCode($status);

        return $response;
    }
}
