<?php
/**
 * Test Execution controller
 */

namespace JMose\CommandSchedulerBundle\Tests\Controller;

use JMose\CommandSchedulerBundle\Tests\CommandSchedulerBaseTest;
use \Symfony\Component\HttpFoundation\Response;

class ExecutionControllerTest extends CommandSchedulerBaseTest
{
    protected $rowSelector = 'tr.execution';
    protected $graphSelector = 'div.graphs';

    /**
     * test list of executions without any data
     */
    public function testCommandExecutionsEmpty()
    {
        $crawler = $this->loadPage(5);

        $result = $crawler->filter($this->rowSelector)->count();
        $this->assertEquals(0, $result);
    }

    /**
     * test list of executions of a single command without executions
     */
    public function testCommandExecutionsWithoutExecutions()
    {
        $this->loadDataFixtures();

        $crawler = $this->loadPage(1);

        $result = $crawler->filter($this->rowSelector)->count();
        $this->assertEquals(0, $result);
    }

    /**
     * test list of executions of a single command with executions
     */
    public function testCommandExecutions()
    {
        $this->loadDataFixtures();

        $crawler = $this->loadPage(5);

        $executions = $crawler->filter($this->rowSelector)->count();
        $graphs = $crawler->filter($this->graphSelector)->count();

        $this->assertEquals(NUMBER_EXECUTIONS, $executions);
        $this->assertEquals(2, $graphs);
    }

    /**
     * Test ajax call to show the output for a given execution id
     */
    public function testCommandOutput() {
        $this->loadDataFixtures();

        $crawler = $this->loadPage();
        $this->assertTrue($crawler->filter('.openOutput')->count() == $crawler->filter('tr.execution')->count());

        /**
         * @var Response $response
         */
        $response = $this->callUrl(
            'GET',
            'command-scheduler/action/output/execution/1',
            'response'
        );

        $response = $response->getContent();

        $this->assertRegExp('/foo<br.*>/', $response);
        $this->assertRegExp('/bar<br.*>/', $response);
    }

    /**
     * fetch list of executions
     *
     * @param int $commandId command ID (default 5)
     *
     * @return mixed
     */
    protected function loadPage($commandId = 5)
    {
        $crawler = $this->callUrl(
            'GET',
            'command-scheduler/detail/commands/executions/' . $commandId
        );

        return $crawler;
    }

}
