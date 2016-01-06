<?php
/**
 * Tests for command controller
 */

namespace JMose\CommandSchedulerBundle\Tests\Controller;

use JMose\CommandSchedulerBundle\Tests\CommandSchedulerBaseTest;

class CommandControllerTest extends CommandSchedulerBaseTest
{
    /**
     * Test "Create a new command" button.
     */
    public function testInitNewScheduledCommand()
    {
        // get input form
        $client = parent::createClient();
        $crawler = $client->request('GET', '/command-scheduler/detail/commands/new');

        $fields = array(
            'select#scheduled_command_command' => 1, // command select
            'select#scheduled_command_rights' => 1, // user/host select

            // input fields
            '#scheduled_command_name' => 1,
            '#scheduled_command_arguments' => 1,
            '#scheduled_command_cronExpression' => 1,
            '#scheduled_command_logFile' => 1,
            '#scheduled_command_priority' => 1,
            '#scheduled_command_expectedRuntime' => 1,
            '#scheduled_command_executeImmediately' => 1,
            '#scheduled_command_disabled' => 1,
            '#scheduled_command_logExecutions' => 1,

            'a.btn' => 1, // back button
            'button#scheduled_command_save' => 1, // save button

            // cronhelper
            'div#cronhelper' => 1,

            '#cron_minute' => 1,
            '#cron_minute_modulo' => 1,
            '#cron_hour' => 1,
            '#cron_hour_modulo' => 1,
            '#cron_day' => 1,
            '#cron_day_modulo' => 1,
            '#cron_month' => 1,
            '#cron_month_modulo' => 1,
            '#cron_week' => 1,
            '#cron_expression' => 1,
        );

        foreach ($fields as $field => $count) {
            // check for fields
            $this->assertEquals($count, $crawler->filter($field)->count());
        }
    }

    /**
     * Test "Edit a command" action
     */
    public function testInitEditScheduledCommand()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\DataFixtures\ORM\LoadScheduledCommandData'
        ));

        // get form
        $client = parent::createClient();
        $crawler = $client->request('GET', '/command-scheduler/detail/commands/edit/1');

        $fields = array(
            'select#scheduled_command_command' => 1, // command select
            'select#scheduled_command_rights' => 1, // user/host select

            // input fields
            '#scheduled_command_name' => 1,
            '#scheduled_command_arguments' => 1,
            '#scheduled_command_cronExpression' => 1,
            '#scheduled_command_logFile' => 1,
            '#scheduled_command_priority' => 1,
            '#scheduled_command_expectedRuntime' => 1,
            '#scheduled_command_executeImmediately' => 1,
            '#scheduled_command_disabled' => 1,
            '#scheduled_command_logExecutions' => 1,

            'a.btn' => 1, // back button
            'button#scheduled_command_save' => 1, // save button
        );

        foreach ($fields as $field => $count)
            $this->assertEquals($count,
                $crawler->filter($field)->count()
            );

        $buttonCrawlerNode = $crawler->selectButton('Save');
        $form = $buttonCrawlerNode->form();
        $fixtureSet = array(
            'scheduled_command[id]' => "1",
            'scheduled_command[name]' => "one",
            'scheduled_command[command]' => "debug:container",
            'scheduled_command[arguments]' => "--help",
            'scheduled_command[cronExpression]' => "@daily",
            'scheduled_command[logFile]' => "one.log",
            'scheduled_command[priority]' => "100"
        );

        $this->assertArraySubset($fixtureSet, $form->getValues());
    }

    /**
     * Test new scheduling creation
     */
    public function testNewSave()
    {
        $crawler = $this->callFormUrlValues(
            'GET', '/command-scheduler/detail/commands/new',
            array(
                'scheduled_command[name]' => "wtc",
                'scheduled_command[command]' => "translation:update",
                'scheduled_command[arguments]' => "--help",
                'scheduled_command[cronExpression]' => "@daily",
                'scheduled_command[logFile]' => "wtc.log",
                'scheduled_command[priority]' => "5"
            )
        );

        // make sure there is one command
        $this->assertEquals(1, $crawler->filter('a[href^="/command-scheduler/action/toggle/command/"]')->count());
        $this->assertEquals("wtc", trim($crawler->filter('td')->eq(1)->text()));
    }

    /**
     * Test "Edit and save a scheduling"
     */
    public function testEditSave()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\DataFixtures\ORM\LoadScheduledCommandData'
        ));

        // edit command
        $crawler = $this->callFormUrlValues(
            'POST',
            '/command-scheduler/detail/commands/edit/1',
            array(
                'scheduled_command[name]' => "edited one"
            )
        );

        // now we are on list, assert there are toggle buttons
        $this->assertEquals(4, $crawler->filter('a[href^="/command-scheduler/action/toggle/command"]')->count());

        // assert the command was changed
        $this->assertEquals("edited one", trim($crawler->filter('td')->eq(1)->text()));
    }

    /**
     * test unlock a locked command
     */
    public function testUnlockCommand()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\DataFixtures\ORM\LoadScheduledCommandData'
        ));

        // make sure there is a locked command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/list/commands'
        );

        $numberLockedBefore = $crawler
            ->filter('a.unlockCommand')
            ->count();

        $this->assertEquals($numberLockedBefore, 1);

        // unlock command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/action/unlock/command/2'
        );

        // there shouldn't be a locked command any more
        $numberLockedAfter = $crawler
            ->filter('a.unlockCommand')
            ->count();

        $this->assertEquals($numberLockedAfter, 0);
    }

    /**
     * test disable and enable a command
     */
    public function testToggleCommand()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\DataFixtures\ORM\LoadScheduledCommandData'
        ));

        $selector = 'a.toggleCommand > .text-danger.fa-power-off';

        // make sure there is a disabled command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/list/commands'
        );

        $numberLocked = $crawler
            ->filter($selector)
            ->count();

        $this->assertEquals($numberLocked, 1);

        // toggle command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/action/toggle/command/3');

        $numberLocked = $crawler
            ->filter($selector)
            ->count();

        $this->assertEquals($numberLocked, 0);

        // toggle command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/action/toggle/command/3');

        $numberLocked = $crawler
            ->filter($selector)
            ->count();

        $this->assertEquals($numberLocked, 1);
    }

    /**
     * test enable and disable logging
     */
    public function testToggleLogging()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\DataFixtures\ORM\LoadScheduledCommandData'
        ));

        $selector = 'a.toggleLogging > .fa-check-square-o';
        $url = '/command-scheduler/action/toggleLogging/command/1';

        // make sure there is a disabled command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/list/commands'
        );

        $numberLogging = $crawler
            ->filter($selector)
            ->count();

        $this->assertEquals($numberLogging, 0);

        // toggle command
        $crawler = $this->callUrl(
            'GET',
            $url);

        $numberLogging = $crawler
            ->filter($selector)
            ->count();

        $this->assertEquals($numberLogging, 1);

        // toggle command
        $crawler = $this->callUrl(
            'GET',
            $url);

        $numberLogging = $crawler
            ->filter($selector)
            ->count();

        $this->assertEquals($numberLogging, 0);
    }

    /**
     * test removing a command
     */
    public function testRemoveCommand()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\DataFixtures\ORM\LoadScheduledCommandData'
        ));

        // remove command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/command/4'
        );

        $numberCommands = $crawler
            ->filter('tr.command')
            ->count();
        $this->assertEquals($numberCommands, 3);
    }
}
