<?php

namespace JMose\CommandSchedulerBundle\Controller;

use JMose\CommandSchedulerBundle\Entity\UserHost;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RightController
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 */
class UserHostController extends BaseController
{
    /**
     * Handle display of new/existing ScheduledCommand object.
     * This action should not be invoke directly
     *
     * @param UserHost $userHost
     * @param Form $userHostForm
     * @return Response
     */
    public function indexRightAction(UserHost $userHost, Form $userHostForm = null)
    {
        if (null === $userHostForm) {
            $userHostForm = $this->createForm('JMose\CommandSchedulerBundle\Form\Type\UserHostType', $userHost);
        }

        return $this->render(
            'JMoseCommandSchedulerBundle:Detail:right.html.twig', array(
                'userHostForm' => $userHostForm->createView()
            )
        );
    }

    /**
     * Initialize a new UserHost object and forward to the index action (view)
     *
     * @return Response
     */
    public function initNewUserHostAction($_returnID = null)
    {
        $userHost = new UserHost();

        return $this->forward(
            'JMoseCommandSchedulerBundle:UserHost:indexRight', array(
                'userHost' => $userHost
            )
        );
    }

    /**
     * Get a ScheduledCommand object with its id and forward it to the index action (view)
     *
     * @param int $rightId
     * @return Response
     */
    public function initEditRightAction($rightId)
    {
        $right = $this->doctrineManager->getRepository($this->bundleName . ':UserHost')
            ->find($rightId);

        return $this->forward(
            'JMoseCommandSchedulerBundle:UserHost:indexRight', array(
                'userHost' => $right
            )
        );
    }

    /**
     * Handle save after form is submit and forward to the index action (view)
     *
     * @param Request $request
     * @return Response
     */
    public function saveRightAction(Request $request)
    {
        // Init and populate form object
        $rightDetail = $request->request->get('user_host');
        if ($rightDetail['id'] != '') {
            $right = $this
                ->getRepository('UserHost')
                ->find($rightDetail['id']);
        } else {
            $right = new UserHost();
        }

        $rightForm = $this->createForm('JMose\CommandSchedulerBundle\Form\Type\UserHostType', $right);
        $rightForm->handleRequest($request);

        if ($rightForm->isValid()) {

            // Handle save to the database
            if (null === $right->getId()) {
                $this->doctrineManager->persist($right);
            }
            $this->doctrineManager->flush();

            // Add a flash message and do a redirect to the list
            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.save', array(), 'JMoseCommandScheduler'));

            return $this->redirect($this->generateUrl('jmose_command_scheduler_list_details', array('_type' => 'rights')));

        } else {
            // Redirect to indexAction with the form object that has validation errors
            return $this->forward(
                'JMoseCommandSchedulerBundle:UserHost:indexRight', array(
                    'userHost' => $right,
                    'userHostForm' => $rightForm
                )
            );
        }
    }

    /**
     * Remove User/Host requirement from database (once and for all, no backup)
     *
     * @param int $id RightID
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeRightAction($id)
    {
        /** @var UserHost $right */
        $right = $this->doctrineManager->getRepository($this->bundleName . ':UserHost')->find($id);
        $entityManager = $this->doctrineManager;
        $entityManager->remove($right);
        $entityManager->flush();

        // Add a flash message and do a redirect to the list
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('flash.deleted', array(), 'JMoseCommandScheduler'));

        return $this->redirect($this->generateUrl('jmose_command_scheduler_list_details', array('_type' => 'rights')));
    }
}
