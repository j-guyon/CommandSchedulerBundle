<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ListController
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Controller
 */
class BaseController extends Controller
{

    /** @var string doctrine manager name */
    protected $managerName = 'default';

    /** @var ObjectManager doctrine manager */
    protected $doctrineManager;

    /** @var string bundle name to be used in (almost) all actions */
    protected $bundleName = 'JMoseCommandSchedulerBundle';

    /**
 * Override method to call #containerInitialized method when container set.
 * {@inheritdoc}
 */
public function setContainer(ContainerInterface $container = null)
{
    parent::setContainer($container);
    $this->setManager();
}


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render($this->bundleName . ':List:overview.html.twig');
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
        $this->setManager();

        $scheduledCommands = $this->doctrineManager->getRepository($this->bundleName . ':ScheduledCommand')->findAll();

        $timeoutValue = $this->container->getParameter('jmose_command_scheduler.lock_timeout');

        $failed = array();
        $now = time();

        foreach ($scheduledCommands as $command) {
            // don't care about disabled commands
            if ($command->isDisabled()) {
                continue;
            }

            $executionTime = $command->getLastExecution();
            $executionTimestamp = $executionTime->getTimestamp();

            $timedOut = (($executionTimestamp + $timeoutValue) < $now);

            if (
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

    /**
     * get name of doctrine manager if set in params, return default otherwise
     *
     * @return string
     */
    protected function setManager()
    {
        // parameter name
        $paramName = 'jmose_command_scheduler.doctrine_manager';
        // prepare default value
        $manager = 'default';

        // check parameter and set return value
        if ($this->container->hasParameter($paramName)) {
            $manager = $this->container->getParameter($paramName);
        }

        $this->managerName = $manager;
        $this->doctrineManager = $this->getDoctrine()->getManager($manager);
    }
}
