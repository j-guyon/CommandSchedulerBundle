<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Form\Type\ScheduledCommandType;

/**
 * Class DetailController
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Controller
 */
class DetailController extends Controller
{

    /**
     * Handle display of new/existing ScheduledCommand object.
     * This action should not be invoke directly
     *
     * @param ScheduledCommand $scheduledCommand
     * @param Form             $scheduledCommandForm
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(ScheduledCommand $scheduledCommand, Form $scheduledCommandForm = null)
    {
        if (null === $scheduledCommandForm) {
            $scheduledCommandForm = $this->createForm(new ScheduledCommandType($this->get('jmose_command_scheduler.command_choice_list')), $scheduledCommand);
        }

        return $this->render(
            'JMoseCommandSchedulerBundle:Detail:index.html.twig', array(
                'scheduledCommandForm' => $scheduledCommandForm->createView()
            )
        );
    }

    /**
     * Initialize a new ScheduledCommand object and forward to the index action (view)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function initNewScheduledCommandAction()
    {
        $scheduledCommand = new ScheduledCommand();

        return $this->forward(
            'JMoseCommandSchedulerBundle:Detail:index', array(
                'scheduledCommand' => $scheduledCommand
            )
        );
    }

    /**
     * Get a ScheduledCommand object with its id and forward it to the index action (view)
     *
     * @param $scheduledCommandId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function initEditScheduledCommandAction($scheduledCommandId)
    {
        $scheduledCommand = $this->getDoctrine()->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')
            ->find($scheduledCommandId);

        return $this->forward(
            'JMoseCommandSchedulerBundle:Detail:index', array(
                'scheduledCommand' => $scheduledCommand
            )
        );
    }

    /**
     * Handle save after form is submit and forward to the index action (view)
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Init and populate form object
        if ($request->request->get('command_scheduler_detail')['id'] != '') {
            $scheduledCommand = $entityManager->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')
                ->find($request->request->get('command_scheduler_detail')['id']);
        } else {
            $scheduledCommand = new ScheduledCommand();
        }

        $scheduledCommandForm = $this->createForm(new ScheduledCommandType($this->get('jmose_command_scheduler.command_choice_list')), $scheduledCommand);
        $scheduledCommandForm->handleRequest($request);

        if ($scheduledCommandForm->isValid()) {

            // Handle save to the database
            if (null === $scheduledCommand->getId()) {
                $scheduledCommand->setLastExecution( new \DateTime());
                $scheduledCommand->setLocked(false);
                $entityManager->persist($scheduledCommand);
            }
            $entityManager->flush();

            // Add a flash message and do a redirect to the list
            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('commandeScheduler.flash.success'));

            return $this->redirect($this->generateUrl('jmose_command_scheduler_list'));

        } else {
            // Redirect to indexAction with the form object that has validation errors
            return $this->forward(
                'JMoseCommandSchedulerBundle:Detail:index', array(
                    'scheduledCommand'     => $scheduledCommand,
                    'scheduledCommandForm' => $scheduledCommandForm
                )
            );
        }
    }
}
