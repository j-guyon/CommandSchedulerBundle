<?php

namespace JMose\CommandSchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Translator;

/**
 * Class BaseController
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Controller
 */
abstract class BaseController extends AbstractController
{
    /**
     * @var string
     */
    private $managerName;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param $managerName string
     */
    public function setManagerName($managerName)
    {
        $this->managerName = $managerName;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getDoctrineManager()
    {
        return $this->getDoctrine()->getManager($this->managerName);
    }
}
