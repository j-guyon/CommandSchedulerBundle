<?php

namespace JMose\CommandSchedulerBundle\Tests\Entity;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;

/**
 * Class ScheduledCommandTest
 * @package JMose\CommandSchedulerBundle\Tests\Entity
 */
class ScheduledCommandTest extends WebTestCase
{

    /**
     * Test CommandScheduler parameters parsing
     */
    public function testParametersParsing()
    {
		$scheduledCommand = new ScheduledCommand();
		
		$scheduledCommand->setArguments("argument1='my new value'");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('argument1', $parsedArguments);
		$this->assertEquals($parsedArguments['argument1'], 'my new value');
		
		$scheduledCommand->setArguments("argument2=\"my new value\"");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('argument2', $parsedArguments);
		$this->assertEquals($parsedArguments['argument2'], 'my new value');
		
		$scheduledCommand->setArguments("argument3=\"my new ' value\"");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('argument3', $parsedArguments);
		$this->assertEquals($parsedArguments['argument3'], "my new ' value");
		
		$scheduledCommand->setArguments("argument4='my new \" value'");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('argument4', $parsedArguments);
		$this->assertEquals($parsedArguments['argument4'], 'my new " value');
		
		$scheduledCommand->setArguments("argument5=value");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('argument5', $parsedArguments);
		$this->assertEquals($parsedArguments['argument5'], 'value');
		
		$scheduledCommand->setArguments("argument5=wrong value");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('argument5', $parsedArguments);
		$this->assertNotEquals($parsedArguments['argument5'], 'wrong value');
		
        $scheduledCommand->setArguments("--option1='my new value'");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('--option1', $parsedArguments);
		$this->assertEquals($parsedArguments['--option1'], 'my new value');
		
		$scheduledCommand->setArguments("--option2=\"my new value\"");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('--option2', $parsedArguments);
		$this->assertEquals($parsedArguments['--option2'], 'my new value');
		
		$scheduledCommand->setArguments("--option3=\"my new ' value\"");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('--option3', $parsedArguments);
		$this->assertEquals($parsedArguments['--option3'], "my new ' value");
		
		$scheduledCommand->setArguments("--option4='my new \" value'");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('--option4', $parsedArguments);
		$this->assertEquals($parsedArguments['--option4'], 'my new " value');
		
		$scheduledCommand->setArguments("--option5=value");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('--option5', $parsedArguments);
		$this->assertEquals($parsedArguments['--option5'], 'value');
		
		$scheduledCommand->setArguments("--option5=wrong value");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('--option5', $parsedArguments);
		$this->assertNotEquals($parsedArguments['--option5'], 'wrong value');
		
		$scheduledCommand->setArguments("--flag");
		$parsedArguments = $scheduledCommand->getArguments(true);
		$this->assertArrayHasKey('--flag', $parsedArguments);
		$this->assertEquals($parsedArguments['--flag'], true);
    }
}
