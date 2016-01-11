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
     * Test scheduler:execute without option
     */
    public function testExecute()
    {
        $this->loadFixtures();

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
        $this->loadFixtures();

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
        $this->loadFixtures();

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

    /**
     * test scheduler:execute if there are no commands at all
     */
    public function testExecuteWithoutCommands()
    {
        $output = $this->executeCommand($this->command, $this->commandName);

        $this->assertRegExp('/Nothing to do/', $output);
    }
}
