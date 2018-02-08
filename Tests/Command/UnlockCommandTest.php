<?php

namespace JMose\CommandSchedulerBundle\Tests\Command;

use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class UnlockCommandTest
 * @package JMose\CommandSchedulerBundle\Tests\Command
 */
class UnlockCommandTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
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
        $this->loadFixtures(
                ['JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData']
        );

        // One command is locked in fixture (2), another have a -1 return code as lastReturn (4)
        $output = $this->runCommand(
                'scheduler:unlock', ['--all' => true]
        );

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
        $this->loadFixtures(
                ['JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData']
        );

        // One command is locked in fixture (2), another have a -1 return code as lastReturn (4)
        $output = $this->runCommand(
                'scheduler:unlock', ['name' => 'two']
        );

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
        $this->loadFixtures(
                ['JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData']
        );

        // One command is locked in fixture with last execution two days ago (2), another have a -1 return code as lastReturn (4)
        $output = $this->runCommand(
                'scheduler:unlock', ['name' => 'two', '--lock-timeout' =>  3 * 24 * 60 * 60 ]
        );

        $this->assertRegExp('/Skipping/', $output);
        $this->assertRegExp('/"two"/', $output);

        $this->em->clear();
        $two = $this->em->getRepository(ScheduledCommand::class)->findOneBy(['name' => 'two']);

        $this->assertTrue($two->isLocked());
    }

}
