<?php

namespace JMose\CommandSchedulerBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class ListControllerTest
 * @package JMose\CommandSchedulerBundle\Tests\Controller
 */
class ListControllerTest extends WebTestCase
{

    /**
     * Test list display
     */
    public function testIndexCommands()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\DataFixtures\ORM\LoadScheduledCommandData'
        ));

        $client = parent::createClient();
        $crawler = $client->request('GET', '/command-scheduler/list/commands');
        $this->assertEquals(4, $crawler->filter('a[href^="/command-JMose\CommandSchedulerBundle\Tests/action/toggle/"]')->count());
    }
}
