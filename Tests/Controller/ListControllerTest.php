<?php

namespace JMose\CommandSchedulerBundle\Tests\Controller;

use JMose\CommandSchedulerBundle\Tests\CommandSchedulerBaseTest;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class ListControllerTest
 * @package JMose\CommandSchedulerBundle\Tests\Controller
 */
class ListControllerTest extends CommandSchedulerBaseTest
{

    /**
     * Test list commands
     */
//    public function testIndexCommands()
//    {
//        $this->loadCommandFixtures();
//
//        $crawler = $this->callUrl('GET', '/command-scheduler/list/commands');
//
//        $result = $crawler->filter('tr.command')->count();
//        $this->assertEquals(NUMBER_COMMANDS_TOTAL, $result);
//    }

    /**
     * Test list UserHost
     */
    public function testIndexUserHosts()
    {
        $this->loadRightsFixtures();

        $crawler = $this->callUrl('GET', '/command-scheduler/list/rights');

        $result = $crawler->filter('tr.right')->count();
        $this->assertEquals(NUMBER_RIGHTS_TOTAL, $result);
    }
}
