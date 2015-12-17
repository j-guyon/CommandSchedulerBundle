<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Class ListController
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @author  Daniel Fischer <dfischer000@gmail.com>
 */
class ListController extends BaseController
{
    /**
     * @param string $_type list type to be shown, can be commands, rights or executions
     *
     * @return Response
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
     * render list of all existing commands
     *
     * @return Response
     */
    private function getListCommands()
    {
        /** @var array $scheduledCommands */
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
        /** @var ArrayCollection $rights */
        $rights = $this->getRepository('UserHost')->findAll();

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
        $executions = $this->getRepository('Execution')->findAll();

        $result = $this->render(
            $this->bundleName . ':List:indexExecutions.html.twig',
            array(
                'executions' => $executions
            )
        );

        return $result;
    }
}
