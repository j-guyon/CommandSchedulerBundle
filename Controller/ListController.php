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
class ListController extends BaseController
{
    /**
     * @param string $_type listtype to be shown, can be commands, rights or executions
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($_type = '')
    {
        $function = 'getList' . ucfirst($_type);

        if (method_exists($this, $function)) {
            $result = $this->$function();
        } else {
            $result = new Response('Method not allowed', Response::HTTP_METHOD_NOT_ALLOWED);
        }

        return $result;
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeCommandAction($id)
    {
        $scheduledCommand = $this->doctrineManager->getRepository($this->bundleName . ':ScheduledCommand')->find($id);
        $entityManager = $this->doctrineManager;
        $entityManager->remove($scheduledCommand);
        $entityManager->flush();

        // Add a flash message and do a redirect to the list
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.deleted', array(), 'JMoseCommandScheduler'));

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list_commands', array('_type' => 'commands')));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toggleCommandAction($id)
    {
        $scheduledCommand = $this->doctrineManager->getRepository($this->bundleName . ':ScheduledCommand')->find($id);

        $scheduledCommand->setDisabled(!$scheduledCommand->isDisabled());

        $this->doctrineManager->flush();

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list_commands', array('_type' => 'commands')));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function executeCommandAction($id)
    {
        $scheduledCommand = $this->doctrineManager->getRepository($this->bundleName . ':ScheduledCommand')->find($id);
        $scheduledCommand->setExecuteImmediately(true);
        $this->doctrineManager->flush();

        // Add a flash message and do a redirect to the list
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.execute', array(), 'JMoseCommandScheduler'));

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list_commands', array('_type' => 'commands')));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unlockCommandAction($id)
    {
        $scheduledCommand = $this->doctrineManager->getRepository($this->bundleName . ':ScheduledCommand')->find($id);
        $scheduledCommand->setLocked(false);
        $this->doctrineManager->flush();

        // Add a flash message and do a redirect to the list
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.unlocked', array(), 'JMoseCommandScheduler'));

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list_commands', array('_type' => 'commands')));
    }

    /**
     * render list of all existing commands
     *
     * @return Response
     */
    private function getListCommands()
    {
        $scheduledCommands = $this->doctrineManager->getRepository($this->bundleName . ':ScheduledCommand')->findAll();

        $result = $this->render(
            $this->bundleName . ':List:indexCommands.html.twig',
            array('scheduledCommands' => $scheduledCommands)
        );

        return $result;
    }

    /**
     * render list of all existing user/host requirements
     *
     * @return Response
     */
    private function getListRights()
    {
        $rights = $this->doctrineManager->getRepository($this->bundleName . ':UserHost')->findAll();

        $result = $this->render(
            $this->bundleName . ':List:indexRights.html.twig',
            array('userHosts' => $rights)
        );

        return $result;
    }

    /**
     * render list of all previous executions
     *
     * @return Response
     */
    private function getListExecutions()
    {
        $result = new Response("not yet supported", Response::HTTP_METHOD_NOT_ALLOWED);
        return $result;
    }
}
