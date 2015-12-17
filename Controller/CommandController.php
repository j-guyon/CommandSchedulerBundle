<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Form\Type\ScheduledCommandType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CommandController
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @author  Daniel Fischer <dfischer000@gmail.com>
 */
class CommandController extends BaseController
{
    /**
     * Handle display of new/existing ScheduledCommand object.
     * This action should not be invoke directly
     *
     * @param ScheduledCommand $scheduledCommand
     * @param Form $scheduledCommandForm
     * @return Response
     */
    public function indexCommandAction(ScheduledCommand $scheduledCommand, Form $scheduledCommandForm = null)
    {
        if (null === $scheduledCommandForm) {
            $scheduledCommandForm = $this->createForm(new ScheduledCommandType(), $scheduledCommand);
        }

        return $this->render(
            'JMoseCommandSchedulerBundle:Detail:command.html.twig', array(
                'scheduledCommandForm' => $scheduledCommandForm->createView()
            )
        );
    }

    /**
     * Initialize a new ScheduledCommand object and forward to the index action (view)
     *
     * @return Response
     */
    public function initNewScheduledCommandAction()
    {
        $scheduledCommand = new ScheduledCommand();

        return $this->forward(
            'JMoseCommandSchedulerBundle:Command:indexCommand', array(
                'scheduledCommand' => $scheduledCommand
            )
        );
    }

    /**
     * Get a ScheduledCommand object with its id and forward it to the index action (view)
     *
     * @param $scheduledCommandId
     * @return Response
     */
    public function initEditScheduledCommandAction($scheduledCommandId)
    {
        $scheduledCommand = $this->getRepository('ScheduledCommand')
            ->find($scheduledCommandId);

        return $this->forward(
            'JMoseCommandSchedulerBundle:Command:indexCommand', array(
                'scheduledCommand' => $scheduledCommand
            )
        );
    }

    /**
     * Handle save after form is submit and forward to the index action (view)
     *
     * @param Request $request
     * @return Response
     */
    public function saveCommandAction(Request $request)
    {
        // Init and populate form object
        $commandDetail = $request->request->get('command_scheduler_detail');
        if ($commandDetail['id'] != '') {
            $scheduledCommand = $this->getRepository('ScheduledCommand')
                ->find($commandDetail['id']);
        } else {
            $scheduledCommand = new ScheduledCommand();
        }

        $scheduledCommandForm = $this->createForm(new ScheduledCommandType(), $scheduledCommand);
        $scheduledCommandForm->handleRequest($request);

        if ($scheduledCommandForm->isValid()) {

            // Handle save to the database
            if (null === $scheduledCommand->getId()) {
                $this->doctrineManager->persist($scheduledCommand);
            }
            $this->doctrineManager->flush();

            // Add a flash message and do a redirect to the list
            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.save', array(), 'JMoseCommandScheduler'));

            return $this->redirect($this->generateUrl('jmose_command_scheduler_list', array('_type' => 'commands')));

        } else {
            // Redirect to indexAction with the form object that has validation errors
            return $this->forward(
                'JMoseCommandSchedulerBundle:Command:index', array(
                    'scheduledCommand' => $scheduledCommand,
                    'scheduledCommandForm' => $scheduledCommandForm
                )
            );
        }
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeCommandAction($id)
    {
        /** @var ScheduledCommand $scheduledCommand */
        $scheduledCommand = $this->getRepository('ScheduledCommand')->find($id);
        $entityManager = $this->doctrineManager;
        $entityManager->remove($scheduledCommand);
        $entityManager->flush();

        // Add a flash message and do a redirect to the list
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.deleted', array(), 'JMoseCommandScheduler'));

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list', array('_type' => 'commands')));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toggleCommandAction($id)
    {
        /** @var ScheduledCommand $scheduledCommand */
        $scheduledCommand = $this->getRepository('ScheduledCommand')->find($id);

        $scheduledCommand->setDisabled(!$scheduledCommand->isDisabled());

        $this->doctrineManager->flush();

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list', array('_type' => 'commands')));
    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toggleLoggingAction($id)
    {
        /** @var ScheduledCommand $scheduledCommand */
        $scheduledCommand = $this->getRepository('ScheduledCommand')->find($id);

        $scheduledCommand->setLogExecutions(!$scheduledCommand->logExecutions());

        $this->doctrineManager->flush();

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list', array('_type' => 'commands')));
    }

    /**
     * set "execute immediately" flag
     *
     * @param int $id command id
     *
     * @return Response
     */
    public function executeCommandAction($id)
    {
        /** @var ScheduledCommand $scheduledCommand */
        $scheduledCommand = $this->getRepository('ScheduledCommand')->find($id);
        $scheduledCommand->setExecuteImmediately(true);
        $this->doctrineManager->flush();

        // Add a flash message and do a redirect to the list
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.execute', array(), 'JMoseCommandScheduler'));

        return $this->redirect(
            $this->generateUrl(
                'jmose_command_scheduler_list',
                array('_type' => 'commands')
            )
        );
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unlockCommandAction($id)
    {
        /** @var ScheduledCommand $scheduledCommand */
        $scheduledCommand = $this->getRepository('ScheduledCommand')->find($id);
        $scheduledCommand->setLocked(false);
        $this->doctrineManager->flush();

        // Add a flash message and do a redirect to the list
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.unlocked', array(), 'JMoseCommandScheduler'));

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list', array('_type' => 'commands')));
    }

}
