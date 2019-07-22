<?php

namespace JMose\CommandSchedulerBundle\Tests\Command;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ExecuteCommandTest
 * @package JMose\CommandSchedulerBundle\Tests\Command
 */
class ExecuteCommandTest extends WebTestCase
{

    /**
     * Test scheduler:execute without option
     */
    public function testExecute()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(
            array(
                'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
            )
        );

        $output = $this->runCommand('scheduler:execute');
        if ($output instanceof CommandTester) {
            $output = $output->getDisplay();
        }

        $this->assertStringStartsWith('Start : Execute all scheduled command', $output);
        $this->assertRegExp('/debug:container should be executed/', $output);
        $this->assertRegExp('/Execute : debug:container --help/', $output);
        $this->assertRegExp('/Immediately execution asked for : debug:router/', $output);
        $this->assertRegExp('/Execute : debug:router/', $output);

        $output = $this->runCommand('scheduler:execute');
        if ($output instanceof CommandTester) {
            $output = $output->getDisplay();
        }
        $this->assertRegExp('/Nothing to do/', $output);
    }

    /**
     * Test scheduler:execute without option
     */
    public function testExecuteWithNoOutput()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(
            array(
                'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
            )
        );

        $output = $this->runCommand(
            'scheduler:execute',
            array(
                '--no-output' => true
            )
        );

        if ($output instanceof CommandTester) {
            $output = $output->getDisplay();
        }

        $this->assertEquals('', $output);

        $output = $this->runCommand('scheduler:execute');
        if ($output instanceof CommandTester) {
            $output = $output->getDisplay();
        }
        $this->assertRegExp('/Nothing to do/', $output);
    }

    /**
     * Test scheduler:execute with --dump option
     */
    public function testExecuteWithDump()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(
            array(
                'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
            )
        );

        $output = $this->runCommand(
            'scheduler:execute',
            array(
                '--dump' => true
            )
        );

        if ($output instanceof CommandTester) {
            $output = $output->getDisplay();
        }
        
        $this->assertStringStartsWith('Start : Dump all scheduled command', $output);
        $this->assertRegExp('/Command debug:container should be executed/', $output);
        $this->assertRegExp('/Immediately execution asked for : debug:router/', $output);
    }
}
