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
