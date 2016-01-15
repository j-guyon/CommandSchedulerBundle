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
        $crawler = $this->callUrl('GET', '/command-scheduler/detail/commands/new');

        $fields = array(
            'select#scheduled_command_command', // command select
            'select#scheduled_command_rights', // user/host select

            // input fields
            '#scheduled_command_name',
            '#scheduled_command_arguments',
            '#scheduled_command_cronExpression',
            '#scheduled_command_logFile',
            '#scheduled_command_priority',
            '#scheduled_command_expectedRuntime',
            '#scheduled_command_executeImmediately',
            '#scheduled_command_disabled',
            '#scheduled_command_logExecutions',

            'a.btn', // back button
            'button#scheduled_command_save', // save button

            // cronhelper
            'div#cronhelper',

            '#cron_minute',
            '#cron_minute_modulo',
            '#cron_hour',
            '#cron_hour_modulo',
            '#cron_day',
            '#cron_day_modulo',
            '#cron_month',
            '#cron_month_modulo',
            '#cron_week',
            '#cron_expression',
        );

        foreach ($fields as $field) {
            // check for fields
            $this->assertEquals(1, $crawler->filter($field)->count());
        }
    }

    /**
     * Test "Edit a command" action
     */
    public function testInitEditScheduledCommand()
    {
        $this->loadDataFixtures();

        // get form
        $crawler = $this->callUrl('GET', '/command-scheduler/detail/commands/edit/1');

        $fields = array(
            'select#scheduled_command_command', // command select
            'select#scheduled_command_rights', // user/host select

            // input fields
            '#scheduled_command_name',
            '#scheduled_command_arguments',
            '#scheduled_command_cronExpression',
            '#scheduled_command_logFile',
            '#scheduled_command_priority',
            '#scheduled_command_expectedRuntime',
            '#scheduled_command_executeImmediately',
            '#scheduled_command_disabled',
            '#scheduled_command_logExecutions',

            'a.btn', // back button
            'button#scheduled_command_save', // save button
        );

        foreach ($fields as $field)
            $this->assertEquals(1,
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
        $this->loadDataFixtures();

        // edit command
        $crawler = $this->callFormUrlValues(
            'POST',
            '/command-scheduler/detail/commands/edit/1',
            array(
                'scheduled_command[name]' => "edited one"
            )
        );

        // now we are on list, assert there are toggle buttons
        $this->assertEquals(NUMBER_COMMANDS_TOTAL, $crawler->filter('a[href^="/command-scheduler/action/toggle/command"]')->count());

        // assert the command was changed
        $this->assertEquals("edited one", trim($crawler->filter('td')->eq(1)->text()));
    }

    /**
     * test unlock a locked command
     */
    public function testUnlockCommand()
    {
        $this->loadDataFixtures();

        // make sure there is a locked command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/list/commands'
        );

        $numberLockedBefore = $crawler
            ->filter('a.unlockCommand')
            ->count();

        $this->assertEquals(NUMBER_COMMANDS_LOCKED, $numberLockedBefore);

        // unlock command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/action/unlock/command/2'
        );

        // there shouldn't be a locked command any more
        $numberLockedAfter = $crawler
            ->filter('a.unlockCommand')
            ->count();

        $this->assertEquals(NUMBER_COMMANDS_LOCKED - 1, $numberLockedAfter);
    }

    /**
     * test disable and enable a command
     */
    public function testToggleCommand()
    {
        $this->loadDataFixtures();

        $selector = 'a.toggleCommand > .text-danger.fa-power-off';

        // make sure there is a disabled command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/list/commands'
        );

        $numberLocked = $crawler
            ->filter($selector)
            ->count();

        $this->assertEquals($numberLocked, NUMBER_COMMANDS_INACTIVE);

        // toggle command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/action/toggle/command/3');

        $numberLocked = $crawler
            ->filter($selector)
            ->count();

        $this->assertEquals($numberLocked, NUMBER_COMMANDS_INACTIVE - 1);

        // toggle command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/action/toggle/command/3');

        $numberLocked = $crawler
            ->filter($selector)
            ->count();

        $this->assertEquals($numberLocked, NUMBER_COMMANDS_INACTIVE);
    }

    /**
     * test enable and disable logging
     */
    public function testToggleLogging()
    {
        $this->loadDataFixtures();

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

    public function testExecuteImmediately()
    {
        $this->loadDataFixtures();

        // muted for commands to be executed immediately
        $selector = 'span.text-muted.fa-play';

        // make sure there is a locked command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/list/commands'
        );

        $before = $crawler
            ->filter($selector)
            ->count();

        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/action/execute/command/5'
        );

        $after = $crawler
            ->filter($selector)
            ->count();

        $this->assertEquals($before + 1, $after);
    }

    /**
     * test removing a command
     */
    public function testRemoveCommand()
    {
        $this->loadDataFixtures();

        // remove command
        $crawler = $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/command/4'
        );

        $numberCommands = $crawler
            ->filter('tr.command')
            ->count();
        $this->assertEquals($numberCommands, NUMBER_COMMANDS_TOTAL - 1);
    }
}
