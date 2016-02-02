<?php
namespace JMose\CommandSchedulerBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use JMose\CommandSchedulerBundle\Entity\Repository\UserHostRepository;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class RightsParser
 * read available rights from database to choose from in command mask
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 */
class RightsParser
{
    /** @var Registry */
    private $doctrine;

    /** @var string bundle name to be used in (almost) all actions */
    private $bundleName = 'JMoseCommandSchedulerBundle';

    /**
     * @param Kernel $kernel
     * @param Registry $doctrine doctrine itself
     * @param string $managerName Name of doctrine manager, default 'default'
     */
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Read available User/Host requirements from database
     *
     * @return array
     */
    public function getRights()
    {
        /** @var UserHostRepository $repository */
        $repository = $this->doctrine->getRepository($this->bundleName . ':UserHost');
        $result = $repository->findAllSelect();
return $result;
    }

}
