<?php

namespace JMose\CommandSchedulerBundle\Tests\Command;

use JMose\CommandSchedulerBundle\Command\ExecuteCommand;
use JMose\CommandSchedulerBundle\Tests\CommandSchedulerBaseTest;

/**
 * Class ExecuteCommandTest
 * @package JMose\CommandSchedulerBundle\Tests\Command
 */
class ExecuteCommandTest extends CommandSchedulerBaseTest
{
    /** @var ExecuteCommand $command instance of command */
    protected $command;
    /** @var string $commandName */
    protected $commandName;

    /**
     * set up variables needed in every test
     */
    public function setUp()
    {
        $this->command = new ExecuteCommand();
        $this->commandName = 'scheduler:execute';

        parent::setUp();
    }

    /**
     * test scheduler:execute if there are no commands at all
     */
    public function testExecuteWithoutCommands()
    {
        $output = $this->executeCommand($this->command, $this->commandName);

        $this->assertRegExp('/Nothing to do/', $output);
    }

    /**
     * Test scheduler:execute without option
     */
    public function testExecute()
    {
        $this->loadDataFixtures();

        $output = $this->executeCommand($this->command, $this->commandName);

        $this->assertStringStartsWith('Start : Execute all scheduled command', $output);
        $this->assertRegExp('/debug:container should be executed/', $output);
        $this->assertRegExp('/Execute : debug:container --help/', $output);
        $this->assertRegExp('/Immediately execution asked for : debug:router/', $output);
        $this->assertRegExp('/Execute : debug:router/', $output);

        $output = $this->executeCommand($this->command, $this->commandName);
        $this->assertRegExp('/Nothing to do/', $output);
    }

    /**
     * Test scheduler:execute with --dump option
     */
    public function testExecuteWithDump()
    {
        $this->loadDataFixtures();

        $output = $this->executeCommand(
            $this->command,
            $this->commandName,
            array(
                '--dump' => true
            )
        );

        $this->assertStringStartsWith('Start : Dump all scheduled command', $output);
        $this->assertRegExp('/Command debug:container should be executed/', $output);
        $this->assertRegExp('/Immediately execution asked for : debug:router/', $output);
    }

    /**
     * Test scheduler:execute with no-output option
     */
    public function testExecuteWithNoOutput()
    {
        $this->loadDataFixtures();

        $output = $this->executeCommand(
            $this->command,
            $this->commandName,
            array(
                '--no-output' => true
            )
        );
        $this->assertEquals('', $output);
        $output = $this->executeCommand($this->command, $this->commandName);
        $this->assertRegExp('/Nothing to do/', $output);
    }

    public function testExecuteRights()
    {
        $this->loadDataFixtures();

        // remove commands without rights requirements (we already tested those
        for ($i = 1; $i <= NUMBER_COMMANDS_NO_RIGHTS; $i++) {
            // remove command
            $this->callUrl(
                'GET',
                '/command-scheduler/action/remove/command/' . $i
            );
        }

        // mark all remaining commands as execute immediately
        for ($i = NUMBER_COMMANDS_NO_RIGHTS + 1; $i <= NUMBER_COMMANDS_TOTAL - 2; $i++) {
            $this->callUrl(
                'GET',
                '/command-scheduler/action/execute/command/' . $i
            );
        }

        // dump commands (no need to execute them, we already know dump works fine
        $output = $this->executeCommand(
            $this->command,
            $this->commandName,
            array(
                '--dump' => true
            )
        );

        $this->assertStringStartsWith('Start : Dump all scheduled command', $output);

        $this->assertRegExp('/--trash=[5-8]/', $output);
        $this->assertNotRegExp('/--trash=[9|10-12]/', $output);
    }
}
