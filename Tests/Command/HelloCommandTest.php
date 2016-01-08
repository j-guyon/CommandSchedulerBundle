<?php
/**
 * Tests for Hello Command
 */

namespace JMose\CommandSchedulerBundle\Tests\Command;

use JMose\CommandSchedulerBundle\Command\HelloCommand;

class HelloCommandTest extends CommandBaseTest {

    public function testHelloCommand() {
        $command = new HelloCommand();

        $result = $this->runCommand($command, 'schedulerTest:hello');
        $this->assertEquals("Hello World", trim($result));

        $result = $this->runCommand($command, 'schedulerTest:hello', array('--name' => 'Sepp'));
        $this->assertEquals("Hello Sepp", trim($result));

    }
}
