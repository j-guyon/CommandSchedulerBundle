<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use JMose\CommandSchedulerBundle\Entity\UserHost;
use JMose\CommandSchedulerBundle\Form\Type\UserHostType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RightController
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 * @package JMose\CommandSchedulerBundle\Controller
 */
class RightController extends BaseController
{
    /**
     * Handle display of new/existing ScheduledCommand object.
     * This action should not be invoke directly
     *
     * @param UserHost $userHost
     * @param Form $userHostForm
     * @return Response
     */
    public function indexCommandAction(UserHost $userHost, Form $userHostForm = null)
    {
        if (null === $userHostForm) {
            $userHostForm = $this->createForm(new UserHostType(), $userHost);
        }

        return $this->render(
            'JMoseCommandSchedulerBundle:Detail:rights.html.twig', array(
                'userHostForm' => $userHostForm->createView()
            )
        );
    }

    /**
     * Initialize a new UserHost object and forward to the index action (view)
     *
     * @return Response
     */
    public function initNewUserHostAction()
    {
        $userHost = new UserHost();

        return $this->forward(
            'JMoseCommandSchedulerBundle:Right:indexCommand', array(
                'userHost' => $userHost
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
        $scheduledCommand = $this->doctrineManager->getRepository($this->bundleName . ':ScheduledCommand')
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
            $scheduledCommand = $this->doctrineManager->getRepository($this->bundleName . ':ScheduledCommand')
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
}
