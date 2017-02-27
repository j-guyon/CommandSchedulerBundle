<?php

namespace JMose\CommandSchedulerBundle\Tests\Entity;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;

/**
 * Class ScheduledCommandTest
 * @package JMose\CommandSchedulerBundle\Tests\Entity
 */
class ScheduledCommandTest extends WebTestCase {

    /**
     * Test CommandScheduler parameters parsing with no quotes
     */
    public function testNoQuotesParametersParsing() {
        $scheduledCommand = new ScheduledCommand();

        $scheduledCommand->setArguments("argument5=value");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('argument5', $parsedArguments);
        $this->assertEquals($parsedArguments['argument5'], 'value');

        $scheduledCommand->setArguments("argument5=wrong value");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('argument5', $parsedArguments);
        $this->assertNotEquals($parsedArguments['argument5'], 'wrong value');
    }

    /**
     * Test CommandScheduler parameters parsing with double quotes
     */
    public function testDoubleQuotesParametersParsing() {
        $scheduledCommand = new ScheduledCommand();

        $scheduledCommand->setArguments("argument2=\"my new value\"");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('argument2', $parsedArguments);
        $this->assertEquals($parsedArguments['argument2'], 'my new value');

        $scheduledCommand->setArguments("argument3=\"my new ' value\"");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('argument3', $parsedArguments);
        $this->assertEquals($parsedArguments['argument3'], "my new ' value");

        $scheduledCommand->setArguments("--option2=\"my new value\"");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('--option2', $parsedArguments);
        $this->assertEquals($parsedArguments['--option2'], 'my new value');

        $scheduledCommand->setArguments("--option3=\"my new ' value\"");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('--option3', $parsedArguments);
        $this->assertEquals($parsedArguments['--option3'], "my new ' value");

        //Wrong value
        $scheduledCommand->setArguments("argument=\"my bad value'");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('argument', $parsedArguments);
        $this->assertNotEquals($parsedArguments['argument'], 'my bad value');
    }

    /**
     * Test CommandScheduler parameters parsing with single quotes
     */
    public function testSingleQuotesParametersParsing() {
        $scheduledCommand = new ScheduledCommand();

        $scheduledCommand->setArguments("argument1='my new value'");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('argument1', $parsedArguments);
        $this->assertEquals($parsedArguments['argument1'], 'my new value');

        $scheduledCommand->setArguments("argument4='my new \" value'");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('argument4', $parsedArguments);
        $this->assertEquals($parsedArguments['argument4'], 'my new " value');

        $scheduledCommand->setArguments("--option1='my new value'");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('--option1', $parsedArguments);
        $this->assertEquals($parsedArguments['--option1'], 'my new value');

        $scheduledCommand->setArguments("--option4='my new \" value'");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('--option4', $parsedArguments);
        $this->assertEquals($parsedArguments['--option4'], 'my new " value');

        //Wrong value
        $scheduledCommand->setArguments("argument='my bad value\"");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('argument', $parsedArguments);
        $this->assertNotEquals($parsedArguments['argument'], 'my bad value');
    }

    /**
     * Test CommandScheduler array parameters parsing with single quotes
     */
    public function testArrayParametersParsing() {
        $scheduledCommand = new ScheduledCommand();

        $scheduledCommand->setArguments("--array-option=value1 --array-option='long value' --array-option=\"other long value\"");
        $parsedArguments = $scheduledCommand->getArguments(true);

        $this->assertArrayHasKey('--array-option', $parsedArguments);
        $this->assertInternalType('array', $parsedArguments['--array-option']);
        $this->assertEquals(3, count($parsedArguments['--array-option']));
        $this->assertContains("value1", $parsedArguments['--array-option']);
        $this->assertContains("long value", $parsedArguments['--array-option']);
        $this->assertContains("other long value", $parsedArguments['--array-option']);
    }

    /**
     * Test CommandScheduler flag parameters parsing
     */
    public function testFlagParametersParsing() {
        $scheduledCommand = new ScheduledCommand();

        $scheduledCommand->setArguments("--flag");
        $parsedArguments = $scheduledCommand->getArguments(true);
        $this->assertArrayHasKey('--flag', $parsedArguments);
        $this->assertEquals($parsedArguments['--flag'], true);
    }

}
