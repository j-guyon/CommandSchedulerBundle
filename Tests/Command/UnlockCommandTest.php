<?php

namespace JMose\CommandSchedulerBundle\Tests\Command;

use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData;

/**
 * Class UnlockCommandTest
 * @package JMose\CommandSchedulerBundle\Tests\Command
 */
class UnlockCommandTest extends WebTestCase {

    use FixturesTrait;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager();
    }

    /**
     * Test scheduler:unlock without --all option
     */
    public function testUnlockAll() {
        //DataFixtures create 4 records
        $this->loadFixtures([LoadScheduledCommandData::class]);

        // One command is locked in fixture (2), another have a -1 return code as lastReturn (4)
        $output = $this->runCommand('scheduler:unlock', ['--all' => true], true)->getDisplay();

        $this->assertRegExp('/"two"/', $output);
        $this->assertNotRegExp('/"one"/', $output);
        $this->assertNotRegExp('/"three"/', $output);

        $this->em->clear();
        $two = $this->em->getRepository(ScheduledCommand::class)->findOneBy(['name' => 'two']);

        $this->assertFalse($two->isLocked());
    }

    /**
     * Test scheduler:unlock with given command name
     */
    public function testUnlockByName() {
        //DataFixtures create 4 records
        $this->loadFixtures([LoadScheduledCommandData::class]);

        // One command is locked in fixture (2), another have a -1 return code as lastReturn (4)
        $output = $this->runCommand('scheduler:unlock', ['name' => 'two'], true)->getDisplay();

        $this->assertRegExp('/"two"/', $output);

        $this->em->clear();
        $two = $this->em->getRepository(ScheduledCommand::class)->findOneBy(['name' => 'two']);

        $this->assertFalse($two->isLocked());
    }

    /**
     * Test scheduler:unlock with given command name and timeout
     */
    public function testUnlockByNameWithTimout() {
        //DataFixtures create 4 records
        $this->loadFixtures([LoadScheduledCommandData::class]);

        // One command is locked in fixture with last execution two days ago (2), another have a -1 return code as lastReturn (4)
        $output = $this->runCommand(
            'scheduler:unlock',
            ['name' => 'two', '--lock-timeout' => 3 * 24 * 60 * 60],
            true
        )->getDisplay();

        $this->assertRegExp('/Skipping/', $output);
        $this->assertRegExp('/"two"/', $output);

        $this->em->clear();
        $two = $this->em->getRepository(ScheduledCommand::class)->findOneBy(['name' => 'two']);

        $this->assertTrue($two->isLocked());
    }

}
