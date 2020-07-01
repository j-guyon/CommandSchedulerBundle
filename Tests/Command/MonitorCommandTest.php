<?php

namespace JMose\CommandSchedulerBundle\Tests\Command;

use JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

/**
 * Class MonitorCommandTest.
 */
class MonitorCommandTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Test scheduler:execute without option.
     */
    public function testExecuteWithError()
    {
        // DataFixtures create 4 records
        $this->loadFixtures([LoadScheduledCommandData::class]);

        // One command is locked in fixture (2), another have a -1 return code as lastReturn (4)
        $output = $this->runCommand('scheduler:monitor', ['--dump' => true], true)->getDisplay();

        $this->assertRegExp('/two:/', $output);
        $this->assertRegExp('/four:/', $output);
    }

    /**
     * Test scheduler:execute without option.
     */
    public function testExecuteWithoutError()
    {
        // DataFixtures create 4 records
        $this->loadFixtures([LoadScheduledCommandData::class]);

        $two = $this->em->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find(2);
        $four = $this->em->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find(4);
        $two->setLocked(false);
        $four->setLastReturnCode(0);
        $this->em->flush();

        // None command should be in error status here.

        // One command is locked in fixture (2), another have a -1 return code as lastReturn (4)
        $output = $this->runCommand(
            'scheduler:monitor',
            [
                '--dump' => true,
            ],
            true
        )->getDisplay();

        $this->assertStringStartsWith('No errors found.', $output);
    }
}
