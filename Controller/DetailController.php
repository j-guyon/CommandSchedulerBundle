<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Form\Type\ScheduledCommandType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DetailController
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Controller
 */
class DetailController extends BaseController
{

    /**
     * Handle display of new/existing ScheduledCommand object.
     * This action should not be invoke directly
     *
     * @param ScheduledCommand $scheduledCommand
     * @param Form $scheduledCommandForm
     * @return Response
     */
    public function indexAction(ScheduledCommand $scheduledCommand, Form $scheduledCommandForm = null)
    {
        if (null === $scheduledCommandForm) {
            $scheduledCommandForm = $this->createForm(ScheduledCommandType::class, $scheduledCommand);
        }

        return $this->render(
            '@JMoseCommandScheduler/Detail/index.html.twig',
            [
                'scheduledCommandForm' => $scheduledCommandForm->createView(),
            ]
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
            'JMoseCommandSchedulerBundle:Detail:index',
            [
                'scheduledCommand' => $scheduledCommand,
            ]
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
        $scheduledCommand = $this->getDoctrineManager()->getRepository(ScheduledCommand::class)
            ->find($scheduledCommandId);

        return $this->forward(
            'JMoseCommandSchedulerBundle:Detail:index',
            [
                'scheduledCommand' => $scheduledCommand,
            ]
        );
    }

    /**
     * Handle save after form is submit and forward to the index action (view)
     *
     * @param Request $request
     * @return Response
     */
    public function saveAction(Request $request)
    {
        $entityManager = $this->getDoctrineManager();

        // Init and populate form object
        $commandDetail = $request->request->get('command_scheduler_detail');
        if ($commandDetail['id'] != '') {
            $scheduledCommand = $entityManager->getRepository(ScheduledCommand::class)
                ->find($commandDetail['id']);
        } else {
            $scheduledCommand = new ScheduledCommand();
        }

        $scheduledCommandForm = $this->createForm(ScheduledCommandType::class, $scheduledCommand);
        $scheduledCommandForm->handleRequest($request);

        if ($scheduledCommandForm->isSubmitted() && $scheduledCommandForm->isValid()) {

            // Handle save to the database
            if (null === $scheduledCommand->getId()) {
                $entityManager->persist($scheduledCommand);
            }
            $entityManager->flush();

            // Add a flash message and do a redirect to the list
            $this->get('session')->getFlashBag()
                ->add('success', $this->get('translator')->trans('flash.success', [], 'JMoseCommandScheduler'));

            return $this->redirect($this->generateUrl('jmose_command_scheduler_list'));

        }
        
        // Redirect to indexAction with the form object that has validation errors
        return $this->forward(
            'JMoseCommandSchedulerBundle:Detail:index',
            [
                'scheduledCommand' => $scheduledCommand,
                'scheduledCommandForm' => $scheduledCommandForm,
            ]
        );
    }
}
