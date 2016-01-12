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
    public function testIndexCommands()
    {
        $this->loadDataFixtures();

        $crawler = $this->callUrl('GET', '/command-scheduler/list/commands');

        $result = $crawler->filter('tr.command')->count();
        $this->assertEquals(NUMBER_COMMANDS_TOTAL, $result);

        $result = $crawler->filter('span.fa-info-circle')->count();
        $this->assertEquals(NUMBER_COMMANDS_RIGHTS, $result);
    }

    /**
     * Test list UserHost
     */
    public function testIndexUserHosts()
    {
        $this->loadDataFixtures();

        $crawler = $this->callUrl('GET', '/command-scheduler/list/rights');

        $result = $crawler->filter('tr.userHost')->count();
        $this->assertEquals(NUMBER_RIGHTS_TOTAL, $result);
    }

    /**
     * Test list Executions
     */
    public function testIndexExecutions()
    {
        $this->loadDataFixtures();

        $crawler = $this->callUrl('GET', '/command-scheduler/list/executions');

        $result = $crawler->filter('tr.execution')->count();
        $this->assertEquals(NUMBER_EXECUTIONS_TOTAL, $result);
    }
}
