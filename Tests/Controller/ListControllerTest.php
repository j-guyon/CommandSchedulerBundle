<?php

namespace JMose\CommandSchedulerBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ListControllerTest
 * @package JMose\CommandSchedulerBundle\Tests\Controller
 */
class ListControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @var KernelBrowser|null
     */
    private static $client = null;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        if (null === self::$client) {
            self::$client = static::createClient();
        }
    }

    /**
     * Test list display
     */
    public function testIndex()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
        ));

        $crawler = self::$client->request('GET', '/command-scheduler/list');
        $this->assertEquals(4, $crawler->filter('a[href^="/command-scheduler/action/toggle/"]')->count());
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

        self::$client->followRedirects(true);

        //toggle off
        $crawler = self::$client->request('GET', '/command-scheduler/action/remove/1');
        $this->assertEquals(3, $crawler->filter('a[href^="/command-scheduler/action/toggle/"]')->count());
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

        self::$client->followRedirects(true);

        //toggle off
        $crawler = self::$client->request('GET', '/command-scheduler/action/toggle/1');
        $this->assertEquals(1, $crawler->filter('a[href="/command-scheduler/action/toggle/1"] > span[class="text-danger glyphicon glyphicon-off"]')->count());

        //toggle on
        $crawler = self::$client->request('GET', '/command-scheduler/action/toggle/1');
        $this->assertEquals(0, $crawler->filter('a[href="/command-scheduler/action/toggle/1"] > span[class="text-danger glyphicon glyphicon-off"]')->count());
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

        self::$client->followRedirects(true);

        //call execute now button
        $crawler = self::$client->request('GET', '/command-scheduler/action/execute/1');
        $this->assertEquals(1, $crawler->filter('a[data-href="/command-scheduler/action/execute/1"] > span[class="text-muted glyphicon glyphicon-play"]')->count());
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

        self::$client->followRedirects(true);

        // One command is locked in fixture (2)
        $crawler = self::$client->request('GET', '/command-scheduler/list');
        $this->assertEquals(1, $crawler->filter('a[data-href="/command-scheduler/action/unlock/2"]')->count());

        $crawler = self::$client->request('GET', '/command-scheduler/action/unlock/2');
        $this->assertEquals(0, $crawler->filter('a[data-href="/command-scheduler/action/unlock/2"]')->count());
    }

    /**
     * Test monitoring URL with json
     */
    public function testMonitorWithErrors()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
        ));

        self::$client->followRedirects(true);

        // One command is locked in fixture (2), another have a -1 return code as lastReturn (4)
        self::$client->request('GET', '/command-scheduler/monitor');
        $this->assertEquals(Response::HTTP_EXPECTATION_FAILED, self::$client->getResponse()->getStatusCode());

        $jsonResponse = self::$client->getResponse()->getContent();
        $jsonArray = json_decode($jsonResponse,true);
        $this->assertEquals(2, count($jsonArray));
    }

    /**
     * Test monitoring URL with json
     */
    public function testMonitorWithoutErrors()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
        ));

        /**
         * @var \Doctrine\ORM\EntityManager
         */
        $em = $this->getContainer()
            ->get('doctrine')
            ->getManager();

        $two = $em->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find(2);
        $four = $em->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find(4);
        $two->setLocked(false);
        $four->setLastReturnCode(0);
        $em->flush();

        self::$client->followRedirects(true);

        // One command is locked in fixture (2), another have a -1 return code as lastReturn (4)
        self::$client->request('GET', '/command-scheduler/monitor');
        $this->assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());

        $jsonResponse = self::$client->getResponse()->getContent();
        $jsonArray = json_decode($jsonResponse,true);
        $this->assertEquals(0, count($jsonArray));
    }
}
