<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 27.12.15
 * Time: 12:32
 */

namespace JMose\CommandSchedulerBundle\Tests\Controller;


use JMose\CommandSchedulerBundle\Tests\CommandSchedulerBaseTest;

class UserHostControllerTest extends CommandSchedulerBaseTest
{
    /**
     * Test "Create a new command" button.
     */
    public function testInitNewUserHost()
    {
        // get input form
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/detail/rights/new'
        );

        $fields = array(
            '#user_host_title',
            '#user_host_user',
            '#user_host_host',
            '#user_host_user_excluded',
            '#user_host_host_excluded',
            '#user_host_save',
            '#user_host_info',
        );

        foreach ($fields as $field) {
            // check for fields
            $this->assertEquals(1, $crawler->filter($field)->count());
        }
    }

    /**
     * Test "Edit a command" action
     */
    public function testInitEditUserHost()
    {
        $this->loadDataFixtures();

        // get form
        $crawler = $this->callUrl('GET', '/command-scheduler/detail/rights/edit/8');

        $fields = array(
            '#user_host_title',
            '#user_host_user',
            '#user_host_host',
            '#user_host_user_excluded',
            '#user_host_host_excluded',
            '#user_host_info',
            '#user_host_save',
            'a.btn' // back button
        );

        // check if the fields are there
        foreach ($fields as $field) {
            $this->assertEquals(1,
                $crawler->filter($field)->count()
            );
        }

        $buttonCrawlerNode = $crawler->selectButton('Save');
        $form = $buttonCrawlerNode->form();
        $fixtureSet = array(
            'user_host[id]' => "8",
            'user_host[title]' => "title",
            'user_host[user]' => "user",
            'user_host[host]' => "host",
            'user_host[user_excluded]' => "userExcluded",
            'user_host[host_excluded]' => "hostExcluded",
            'user_host[info]' => "info",
        );

        $this->assertArraySubset($fixtureSet, $form->getValues());
    }

    /**
     * Test new scheduling creation
     */
    public function testNewSave()
    {
        $crawler = $this->callFormUrlValues(
            'GET',
            '/command-scheduler/detail/rights/new',
            array(
                'user_host[title]' => "title",
                'user_host[user]' => "user",
                'user_host[host]' => "host",
                'user_host[user_excluded]' => "userExcluded",
                'user_host[host_excluded]' => "hostExcluded",
            )
        );

        $count = $crawler->filter('tr.userHost')->count();
        $title = trim($crawler->filter('td')->eq(0)->text());

        // make sure there is one command
        $this->assertEquals(1, $count);
        $this->assertEquals("title", $title);
    }

    /**
     * Test "Edit and save a scheduling"
     */
    public function testEditSave()
    {
        $this->loadDataFixtures();
        $title = 'edited';

        // edit command
        $crawler = $this->callFormUrlValues(
            'GET',
            '/command-scheduler/detail/rights/edit/1',
            array(
                'user_host[title]' => $title
            )
        );

        // assert the command was changed
        $this->assertEquals($title, trim($crawler->filter('td')->eq(0)->text()));
    }

    /**
     * test removing a command
     */
    public function testRemoveUserHost()
    {
        $this->loadDataFixtures();

        // remove command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/right/8'
        );

        $numberRights = $crawler
            ->filter('tr.userHost')
            ->count();

        $this->assertEquals(NUMBER_RIGHTS_TOTAL - 1, $numberRights);
    }
}
