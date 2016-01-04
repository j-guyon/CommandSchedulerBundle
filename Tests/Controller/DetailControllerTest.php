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
     * Test "Create a new command" button.
     */
    public function testInitNewScheduledCommand()
    {
        $this->loadFixtures(array());

        $client = parent::createClient();
        $crawler = $client->request('GET', '/command-scheduler/detail/new');
        $this->assertEquals(1, $crawler->filter('button[id="command_scheduler_detail_save"]')->count());
    }

    /**
     * Test "Edit a command" action
     */
    public function testInitEditScheduledCommand()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
        ));

        $client = parent::createClient();
        $crawler = $client->request('GET', '/command-scheduler/detail/edit/1');
        $this->assertEquals(1, $crawler->filter('button[id="command_scheduler_detail_save"]')->count());

        $buttonCrawlerNode = $crawler->selectButton('Save');
        $form = $buttonCrawlerNode->form();
        $fixtureSet = array(
            'command_scheduler_detail[id]' => "1",
            'command_scheduler_detail[name]' => "one",
            'command_scheduler_detail[command]' => "debug:container",
            'command_scheduler_detail[arguments]' => "--help",
            'command_scheduler_detail[cronExpression]' => "@daily",
            'command_scheduler_detail[logFile]' => "one.log",
            'command_scheduler_detail[priority]' => "100"
        );

        $this->assertArraySubset($fixtureSet, $form->getValues());
    }

    /**
     * Test new scheduling creation
     */
    public function testNewSave()
    {
        $this->loadFixtures(array());

        $client = parent::createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/command-scheduler/detail/new');
        $buttonCrawlerNode = $crawler->selectButton('Save');
        $form = $buttonCrawlerNode->form();

        $form->setValues(array(
            'command_scheduler_detail[name]' => "wtc",
            'command_scheduler_detail[command]' => "translation:update",
            'command_scheduler_detail[arguments]' => "--help",
            'command_scheduler_detail[cronExpression]' => "@daily",
            'command_scheduler_detail[logFile]' => "wtc.log",
            'command_scheduler_detail[priority]' => "5"
        ));
        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('a[href^="/command-scheduler/action/toggle/"]')->count());
        $this->assertEquals("wtc", trim($crawler->filter('td')->eq(1)->text()));
    }

    /**
     * Test "Edit and save a scheduling"
     */
    public function testEditSave()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
        ));

        $client = parent::createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/command-scheduler/detail/edit/1');
        $buttonCrawlerNode = $crawler->selectButton('Save');
        $form = $buttonCrawlerNode->form();

        $form->get('command_scheduler_detail[name]')->setValue('edited one');
        $crawler = $client->submit($form);

        $this->assertEquals(4, $crawler->filter('a[href^="/command-scheduler/action/toggle/"]')->count());
        $this->assertEquals("edited one", trim($crawler->filter('td')->eq(1)->text()));
    }

}