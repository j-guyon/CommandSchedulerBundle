<?php

namespace JMose\CommandSchedulerBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class DetailControllerTest
 * @package JMose\CommandSchedulerBundle\Tests\Controller
 */
class DetailControllerTest extends WebTestCase
{

    /**
     * Display "New command" form.
     */
    public function testInitNewScheduledCommand()
    {
        $this->loadFixtures(array());

        $client = parent::createClient();
        $crawler = $client->request('GET', '/command-scheduler/detail/new');
        $this->assertEquals(1, $crawler->filter('button[id="command_scheduler_detail_save"]')->count());
    }

} 