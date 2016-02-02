<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 27.12.15
 * Time: 12:32
 */

namespace JMose\CommandSchedulerBundle\Tests\Controller;


use JMose\CommandSchedulerBundle\Tests\CommandSchedulerBaseTest;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MonitorControllerTest extends CommandSchedulerBaseTest {

    /**
     * test monitor action with no existing commands
     */
    public function testMonitorNoCommands()
    {
        // call monitor
        $json = $this->getMonitorResponse();

        $this->assertEmpty($json);
    }

    /**
     * test monitor action
     */
    public function testMonitorError()
    {
        $this->loadDataFixtures();

        // call monitor
        $json = $this->getMonitorResponse(Response::HTTP_EXPECTATION_FAILED);

        $this->assertNotEmpty($json);
        $this->assertEquals(NUMBER_COMMANDS_MONITOR, count($json));

        // test every entry in array
        array_map(function($e) {
            //check for array keys
            WebTestCase::assertArrayHasKey('ID_SCHEDULED_COMMAND', $e);
            WebTestCase::assertArrayHasKey('LAST_RETURN_CODE', $e);
            WebTestCase::assertArrayHasKey('B_LOCKED', $e);
            WebTestCase::assertArrayHasKey('DH_LAST_EXECUTION', $e);
        }, $json);
    }

    /**
     * test monitor action with no failed commands
     */
    public function testMonitorOK()
    {
        $this->loadDataFixtures();

        // remove commands to get 'OK' status
        $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/command/2'
        );
        $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/command/5'
        );
        $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/command/13'
        );

        // call monitor
        $json = $this->getMonitorResponse();

        $this->assertEmpty($json);
    }

    /**
     * test monitoring view with no commands
     */
    public function testMonitorViewNoCommands()
    {
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/status'
        );

        $result = $crawler->filter('tr.status')->count();
        $this->assertEquals(0, $result);
    }

    /**
     * test monitoring view with no errors
     */
    public function testMonitorViewOK(){
        $this->loadDataFixtures();

        // remove commands to get 'OK' status
        $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/command/2'
        );
        $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/command/5'
        );
        $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/command/13'
        );

        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/status'
        );

        $result = $crawler->filter('tr.status')->count();
        $this->assertEquals(0, $result);
    }

    /**
     * test monitoring view with errors
     */
    public function testMonitorViewError(){
        $this->loadDataFixtures();

        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/status'
        );

        $result = $crawler->filter('tr.status')->count();
        $this->assertEquals(NUMBER_COMMANDS_MONITOR, $result);
    }

    /**
     * call monitoring url, check HTTP status, content_type for application/json and return json-decoded content
     *
     * @param int $statusExpected expected HTTP Status
     *
     * @return mixed
     */
    protected function getMonitorResponse($statusExpected = Response::HTTP_OK)
    {
        /** @var Response $response */
        $response = $this->callUrl(
            'GET',
            '/command-scheduler/monitor',
            'response'
        );

        // check HTTP status
        $status = $response->getStatusCode();
        $this->assertEquals($statusExpected, $status);

        // check content type
        $content_type = $response->headers->get('content-type');
        $content_type = strtolower($content_type);
        $this->assertEquals('application/json', $content_type);

        // content type is json, so decode content
        $json = $response->getContent();
        $json = json_decode($json, true);

        // return content
        return $json;
    }
}
