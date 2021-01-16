<?php

namespace JMose\CommandSchedulerBundle\Tests\Command;

use JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData;
use JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandWithDynamicValuesData;
use JMose\CommandSchedulerBundle\Tests\App\Command\TestCommand;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

/**
 * Class ExecuteCommandTest.
 */
class ExecuteCommandTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * Test scheduler:execute without option.
     */
    public function testExecute()
    {
        // DataFixtures create 4 records
        $this->loadFixtures([LoadScheduledCommandData::class]);

        $output = $this->runCommand('scheduler:execute', [], true)->getDisplay();

        $this->assertStringStartsWith('Start : Execute all scheduled command', $output);
        $this->assertRegExp('/debug:container should be executed/', $output);
        $this->assertRegExp('/Execute : debug:container --help/', $output);
        $this->assertRegExp('/Immediately execution asked for : debug:router/', $output);
        $this->assertRegExp('/Execute : debug:router/', $output);

        $output = $this->runCommand('scheduler:execute')->getDisplay();
        $this->assertRegExp('/Nothing to do/', $output);
    }

    /**
     * Test scheduler:execute without option.
     */
    public function testExecuteWithNoOutput()
    {
        // DataFixtures create 4 records
        $this->loadFixtures([LoadScheduledCommandData::class]);

        $output = $this->runCommand(
            'scheduler:execute',
            [
                '--no-output' => true,
            ],
            true
        )->getDisplay();

        $this->assertEquals('', $output);

        $output = $this->runCommand('scheduler:execute')->getDisplay();
        $this->assertRegExp('/Nothing to do/', $output);
    }

    /**
     * Test scheduler:execute with --dump option.
     */
    public function testExecuteWithDump()
    {
        // DataFixtures create 4 records
        $this->loadFixtures([LoadScheduledCommandData::class]);

        $output = $this->runCommand(
            'scheduler:execute',
            [
                '--dump' => true,
            ],
            true
        )->getDisplay();

        $this->assertStringStartsWith('Start : Dump all scheduled command', $output);
        $this->assertRegExp('/Command debug:container should be executed/', $output);
        $this->assertRegExp('/Immediately execution asked for : debug:router/', $output);
    }

    /**
     * Test scheduler:execute without option.
     */
    public function testExecuteWithParameters()
    {
        // DataFixtures create 4 records
        $this->loadFixtures([LoadScheduledCommandWithDynamicValuesData::class]);

        $this->runCommand('scheduler:execute', [], true);
        $result = json_decode(file_get_contents(TestCommand::OUTPUT_LOG_FILE), true);

        $this->assertSame($result[0][TestCommand::LOG_FILE], LoadScheduledCommandWithDynamicValuesData::LOG_FILE);
        $this->assertSame($result[0][TestCommand::LAST_RETURN_CODE], LoadScheduledCommandWithDynamicValuesData::LAST_RETURN_CODE_0);
        $this->assertSame($result[0][TestCommand::LAST_EXECUTION_TIME], LoadScheduledCommandWithDynamicValuesData::LAST_EXECUTION_DATE);

        $this->assertSame($result[1][TestCommand::LOG_FILE], LoadScheduledCommandWithDynamicValuesData::LOG_FILE);
        $this->assertSame($result[1][TestCommand::LAST_RETURN_CODE], LoadScheduledCommandWithDynamicValuesData::LAST_RETURN_CODE_NEGATIVE_1);
        $this->assertSame($result[1][TestCommand::LAST_EXECUTION_TIME], LoadScheduledCommandWithDynamicValuesData::LAST_EXECUTION_DATE);
    }
}
