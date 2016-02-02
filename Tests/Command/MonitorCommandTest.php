<?php

namespace JMose\CommandSchedulerBundle\Tests\Command;

use JMose\CommandSchedulerBundle\Command\MonitorCommand;
use JMose\CommandSchedulerBundle\Tests\CommandSchedulerBaseTest;

/**
 * Class MonitorCommandTest
 * @package JMose\CommandSchedulerBundle\Tests\Command
 */
class MonitorCommandTest extends CommandSchedulerBaseTest
{
    private $noErrors = 'No errors found.';

    /**
     * test monitor action with no existing commands
     */
    public function testExecuteWithNoCommands()
    {
        // call monitor
        $output = $this->callCommand();

        $this->assertEquals($this->noErrors, $output);
    }

    /**
     * test monitor action
     */
    public function testExecutionWithErrors()
    {
        $this->loadDataFixtures();

        $output = $this->callCommand();

        $expressions = array(
            'no rights: returncode 1, locked: ,',
            'two: returncode 0, locked: 1',
            'locked, running: returncode 0'
        );

        $lines = explode("\n", $output);
        $this->assertStringStartsNotWith($this->noErrors, $output);
        $this->assertEquals(NUMBER_COMMANDS_MONITOR, count($lines));

        // check every expected expression
        foreach($expressions as $exp){
            $found = false;

            // search for expression in all lines
            foreach($lines as $line) {
                // if one is found everything's ok
                $found = ($found || (stripos($line, $exp) === 0));
            }

            // expression found
            $this->assertTrue($found);
        }
    }

    /**
     * test monitor action with no failed commands
     */
    public function testMonitorOK()
    {
        $this->loadDataFixtures();

        // remove commands to get 'OK' status
        $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/command/2'
        );
        $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/command/5'
        );
        $this->callUrl(
            'GET',
            '/command-scheduler/action/remove/command/13'
        );

        // call monitor
        $output = $this->callCommand();

        $this->assertEquals($this->noErrors, $output);
    }

    /**
     * call command and return output
     */
    private function callCommand()
    {
        $output = $this->executeCommand(
            new MonitorCommand(),
            'schedulerTools:monitor',
            array(
                '--dump' => true
            )
        );

        return trim($output);
    }
}
