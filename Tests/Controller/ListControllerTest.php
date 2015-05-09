<?php

namespace JMose\CommandSchedulerBundle\Tests\Controller;

use JMose\CommandSchedulerBundle\Tests\Fixtures\AbstractTestCase;

class ListControllerTest extends AbstractTestCase
{

    public function testOne()
    {
        $client = parent::createClient();

        $crawler = $client->request('GET', '/command-scheduler/list');

        $this->assertEquals(1, $crawler->filter('a[href="/command-scheduler/detail/new"]')->count());
    }

}
