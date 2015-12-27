<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 27.12.15
 * Time: 12:32
 */

namespace JMose\CommandSchedulerBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class CommandControllerTest extends WebTestCase {
    /**
     * Test "Create a new command" button.
     */
    public function testInitNewScheduledCommand()
    {
        $this->loadFixtures(array());

        $client = parent::createClient();
        $crawler = $client->request('GET', '/command-scheduler/detail/commands/new');
        $this->assertEquals(1, $crawler->filter('button[id="command_JMose\CommandSchedulerBundle\Tests_detail_save"]')->count());
    }

//    /**
//     * Test "Edit a command" action
//     */
//    public function testInitEditScheduledCommand()
//    {
//        //DataFixtures create 4 records
//        $this->loadFixtures(array(
//            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
//        ));
//
//        $client = parent::createClient();
//        $crawler = $client->request('GET', '/command-JMose\CommandSchedulerBundle\Tests/detail/edit/1');
//        $this->assertEquals(1, $crawler->filter('button[id="command_JMose\CommandSchedulerBundle\Tests_detail_save"]')->count());
//
//        $buttonCrawlerNode = $crawler->selectButton('Save');
//        $form = $buttonCrawlerNode->form();
//        $fixtureSet = array(
//            'command_JMose\CommandSchedulerBundle\Tests_detail[id]' => "1",
//            'command_JMose\CommandSchedulerBundle\Tests_detail[name]' => "one",
//            'command_JMose\CommandSchedulerBundle\Tests_detail[command]' => "container:debug",
//            'command_JMose\CommandSchedulerBundle\Tests_detail[arguments]' => "--help",
//            'command_JMose\CommandSchedulerBundle\Tests_detail[cronExpression]' => "@daily",
//            'command_JMose\CommandSchedulerBundle\Tests_detail[logFile]' => "one.log",
//            'command_JMose\CommandSchedulerBundle\Tests_detail[priority]' => "100"
//        );
//
//        $this->assertArraySubset($fixtureSet, $form->getValues());
//    }
//
//    /**
//     * Test new scheduling creation
//     */
//    public function testNewSave()
//    {
//        $this->loadFixtures(array());
//
//        $client = parent::createClient();
//        $client->followRedirects(true);
//        $crawler = $client->request('GET', '/command-JMose\CommandSchedulerBundle\Tests/detail/new');
//        $buttonCrawlerNode = $crawler->selectButton('Save');
//        $form = $buttonCrawlerNode->form();
//
//        $form->setValues(array(
//            'command_JMose\CommandSchedulerBundle\Tests_detail[name]' => "wtc",
//            'command_JMose\CommandSchedulerBundle\Tests_detail[command]' => "translation:update",
//            'command_JMose\CommandSchedulerBundle\Tests_detail[arguments]' => "--help",
//            'command_JMose\CommandSchedulerBundle\Tests_detail[cronExpression]' => "@daily",
//            'command_JMose\CommandSchedulerBundle\Tests_detail[logFile]' => "wtc.log",
//            'command_JMose\CommandSchedulerBundle\Tests_detail[priority]' => "5"
//        ));
//        $crawler = $client->submit($form);
//
//        $this->assertEquals(1, $crawler->filter('a[href^="/command-JMose\CommandSchedulerBundle\Tests/action/toggle/"]')->count());
//        $this->assertEquals("wtc", trim($crawler->filter('td')->eq(1)->text()));
//    }
//
//    /**
//     * Test "Edit and save a scheduling"
//     */
//    public function testEditSave()
//    {
//        //DataFixtures create 4 records
//        $this->loadFixtures(array(
//            'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
//        ));
//
//        $client = parent::createClient();
//        $client->followRedirects(true);
//        $crawler = $client->request('GET', '/command-JMose\CommandSchedulerBundle\Tests/detail/edit/1');
//        $buttonCrawlerNode = $crawler->selectButton('Save');
//        $form = $buttonCrawlerNode->form();
//
//        $form->get('command_JMose\CommandSchedulerBundle\Tests_detail[name]')->setValue('edited one');
//        $crawler = $client->submit($form);
//
//        $this->assertEquals(4, $crawler->filter('a[href^="/command-JMose\CommandSchedulerBundle\Tests/action/toggle/"]')->count());
//        $this->assertEquals("edited one", trim($crawler->filter('td')->eq(1)->text()));
//    }

}
