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
    public function testIndex()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
        ));

        $client = parent::createClient();
        $crawler = $client->request('GET', '/command-JMose\CommandSchedulerBundle\Tests/list');
        $this->assertEquals(4, $crawler->filter('a[href^="/command-JMose\CommandSchedulerBundle\Tests/action/toggle/"]')->count());
    }

    /**
     * Test permanent deletion on command
     */
    public function testRemove()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
        ));

        $client = parent::createClient();
        $client->followRedirects(true);

        //toggle off
        $crawler = $client->request('GET', '/command-JMose\CommandSchedulerBundle\Tests/action/remove/1');
        $this->assertEquals(3, $crawler->filter('a[href^="/command-JMose\CommandSchedulerBundle\Tests/action/toggle/"]')->count());
    }

    /**
     * Test On/Off toggle on list
     */
    public function testToggle()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
        ));

        $client = parent::createClient();
        $client->followRedirects(true);

        //toggle off
        $crawler = $client->request('GET', '/command-JMose\CommandSchedulerBundle\Tests/action/toggle/1');
        $this->assertEquals(1, $crawler->filter('a[href="/command-JMose\CommandSchedulerBundle\Tests/action/toggle/1"] > span[class="text-danger fa fa-power-off"]')->count());

        //toggle on
        $crawler = $client->request('GET', '/command-JMose\CommandSchedulerBundle\Tests/action/toggle/1');
        $this->assertEquals(0, $crawler->filter('a[href="/command-JMose\CommandSchedulerBundle\Tests/action/toggle/1"] > span[class="text-danger fa fa-power-off"]')->count());
    }

    /**
     * Test Execute now button on list
     */
    public function testExecute()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
        ));

        $client = parent::createClient();
        $client->followRedirects(true);

        //call execute now button
        $crawler = $client->request('GET', '/command-JMose\CommandSchedulerBundle\Tests/action/execute/1');
        $this->assertEquals(1, $crawler->filter('a[data-href="/command-JMose\CommandSchedulerBundle\Tests/action/execute/1"] > span[class="text-muted fa fa-play"]')->count());
    }

    /**
     * Test unlock button on list
     */
    public function testUnlock()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
        ));

        $client = parent::createClient();
        $client->followRedirects(true);

        // One command is locked in fixture (2)
        $crawler = $client->request('GET', '/command-JMose\CommandSchedulerBundle\Tests/list');
        $this->assertEquals(1, $crawler->filter('a[data-href="/command-JMose\CommandSchedulerBundle\Tests/action/unlock/2"]')->count());

        $crawler = $client->request('GET', '/command-JMose\CommandSchedulerBundle\Tests/action/unlock/2');
        $this->assertEquals(0, $crawler->filter('a[data-href="/command-JMose\CommandSchedulerBundle\Tests/action/unlock/2"]')->count());
    }

}
