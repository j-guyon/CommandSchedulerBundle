<?php

namespace JMose\CommandSchedulerBundle\Tests\Command;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class ExecuteCommandTest
 * @package JMose\CommandSchedulerBundle\Tests\Command
 */
class ExecuteCommandTest extends WebTestCase
{

    /**
     * Test JMose\CommandSchedulerBundle\Tests:execute without option
     */
    public function testExecute()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(
            array(
                'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
            )
        );

        $output = $this->runCommand('JMose\CommandSchedulerBundle\Tests:execute');

        $this->assertStringStartsWith('Start : Execute all scheduled command', $output);
        $this->assertRegExp('/container:debug should be executed/', $output);
        $this->assertRegExp('/Execute : container:debug --help/', $output);
        $this->assertRegExp('/Immediately execution asked for : router:debug/', $output);
        $this->assertRegExp('/Execute : router:debug/', $output);

        $output = $this->runCommand('JMose\CommandSchedulerBundle\Tests:execute');
        $this->assertRegExp('/Nothing to do/', $output);
    }

    /**
     * Test JMose\CommandSchedulerBundle\Tests:execute without option
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
            'JMose\CommandSchedulerBundle\Tests:execute',
            array(
                '--no-output' => true
            )
        );

        $this->assertEquals('', $output);

        $output = $this->runCommand('JMose\CommandSchedulerBundle\Tests:execute');
        $this->assertRegExp('/Nothing to do/', $output);
    }

    /**
     * Test JMose\CommandSchedulerBundle\Tests:execute with --dump option
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
            'JMose\CommandSchedulerBundle\Tests:execute',
            array(
                '--dump' => true
            )
        );

        $this->assertStringStartsWith('Start : Dump all scheduled command', $output);
        $this->assertRegExp('/Command container:debug should be executed/', $output);
        $this->assertRegExp('/Immediately execution asked for : router:debug/', $output);
    }
}
