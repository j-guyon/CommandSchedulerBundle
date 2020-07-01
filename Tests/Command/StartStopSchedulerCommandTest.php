<?php

namespace JMose\CommandSchedulerBundle\Tests\Command;

use JMose\CommandSchedulerBundle\Command\StartSchedulerCommand;
use JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

/**
 * Class StartStopSchedulerCommandTest.
 */
class StartStopSchedulerCommandTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * Test scheduler:start and scheduler:stop.
     */
    public function testStartAndStopScheduler()
    {
        // DataFixtures create 4 records
        $this->loadFixtures([LoadScheduledCommandData::class]);

        $pidFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.StartSchedulerCommand::PID_FILE;

        $output = $this->runCommand('scheduler:start', [], true)->getDisplay();
        $this->assertStringStartsWith('Command scheduler started in non-blocking mode...', $output);
        $this->assertFileExists($pidFile);

        $output = $this->runCommand('scheduler:stop')->getDisplay();
        $this->assertStringStartsWith('Command scheduler is stopped.', $output);
        $this->assertFileNotExists($pidFile);
    }
}
