<?php
/**
 * Tests for Hello Command
 */

namespace JMose\CommandSchedulerBundle\Tests\Command;

use JMose\CommandSchedulerBundle\Command\HelloCommand;
use JMose\CommandSchedulerBundle\Tests\CommandSchedulerBaseTest;

class HelloCommandTest extends CommandSchedulerBaseTest {

    public function testHelloCommand() {
        $command = new HelloCommand();
        $commandName = 'schedulerTest:hello';

        $result = $this->executeCommand($command, $commandName);
        $this->assertStringStartsWith("Hello World", $result);

        $result = $this->executeCommand(
            $command,
            $commandName,
            array('--name' => 'Sepp')
        );
        $this->assertStringStartsWith("Hello Sepp", $result);

        $exitCode = "a";
        $result = $this->executeCommand(
            $command,
            $commandName,
            array(
                '--name' => 'Franz',
                '--randSleep' => true,
                '--randReturn' => true,
            ),
            $exitCode
        );
        $this->assertStringStartsWith("Hello Franz", $result);
        $this->assertNotEquals("a", $exitCode);
    }
}
