<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseController - contains basic functions and members used for all controller
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 */
class BaseController extends Controller
{
    /** @var string doctrine manager name */
    protected $managerName = 'default';

    /** @var EntityManager doctrine manager */
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

    /**
     * get a repository for a given entity
     *
     * @param string $entity name of entity for which a repository is returned
     *
     * @return EntityRepository
     */
    protected function getRepository($entity) {
        return $this->doctrineManager->getRepository($this->bundleName . ':' . $entity);
    }
}
