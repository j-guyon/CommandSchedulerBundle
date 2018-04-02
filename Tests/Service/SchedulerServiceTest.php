<?php
/**
 * Created by PhpStorm.
 * User: carlo
 * Date: 6/5/2018
 * Time: 7:51 AM
 */

namespace App\Tests\Service;


use JMose\CommandSchedulerBundle\Exception\CommandNotFoundException;
use JMose\CommandSchedulerBundle\Service\SchedulerService;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class SchedulerServiceTest extends WebTestCase
{
    /**
     * @var SchedulerService
     */
    private $schedulerService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->schedulerService = static::$kernel->getContainer()
            ->get(SchedulerService::class);
    }

    public function testSchedulerService()
    {
        $this->loadFixtures(
            array(
                'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
            )
        );

        $cmdOnDemand = $this->schedulerService->cmd('on-demand');
        $cmdOne = $this->schedulerService->cmd('one');
        $cmdThree = $this->schedulerService->cmd('three');
        $cmdFour = $this->schedulerService->cmd('four');
        $cmdFake = $this->schedulerService->cmd('fake');

        /** Exists */
        $this->assertTrue($cmdOnDemand->exists());
        $this->assertTrue($cmdOne->exists());
        $this->assertTrue($cmdThree->exists());
        $this->assertTrue($cmdFour->exists());
        $this->assertFalse($cmdFake->exists());

        /** is* Tests */
        $this->assertTrue($cmdOnDemand->isOnDemand());
        $this->assertTrue($cmdOne->isAuto());
        $this->assertTrue($cmdThree->isDisabled());
        $this->assertTrue($cmdFour->isFailing());
        $this->assertTrue($cmdFour->isRunning());
    }

    public function testCommandNotFoundException() {
        $this->expectException(CommandNotFoundException::class);
        $this->schedulerService->command('fake')
                        ->run()
                    ;
    }

    public function testInvalidCronException() {
        $this->loadFixtures(
            array(
                'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
            )
        );

        $cmdOnDemand = $this->schedulerService->cmd('on-demand');
        /** Trying to change it to Auto */
        $this->expectException(\InvalidArgumentException::class);
        $cmdOnDemand->setAuto();
    }

    public function testRunOnDemand() {
        $this->loadFixtures(
            array(
                'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
            )
        );

        $cmdOnDemand = $this->schedulerService->cmd('on-demand');
        $cmdOne = $this->schedulerService->cmd('one');
        $cmdTwo = $this->schedulerService->cmd('two');
        $cmdFour = $this->schedulerService->cmd('four');

        $cmdOnDemand->run();
        $cmdFour->stop();
        $cmdOne->setOnDemand();
        $cmdTwo->disable();

        $output = $this->runCommand('scheduler:execute');

        $this->assertStringStartsWith('Start : Execute all scheduled command', $output);
        $this->assertRegExp('/Immediately execution asked for : debug:config/', $output);
        $this->assertRegExp('/Execute : debug:config/', $output);
        $this->assertNotRegExp('/Execute : debug:container/', $output);
        $this->assertNotRegExp('/Execute : debug:router/', $output);

        $output = $this->runCommand('scheduler:execute');
        $this->assertRegExp('/Nothing to do/', $output);
    }

    public function testChangeToAuto()
    {
        $this->loadFixtures(
            array(
                'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
            )
        );

        $cmdOnDemand = $this->schedulerService->cmd('on-demand');

        $cmdOnDemand->setAuto('* * * * *');

        $output = $this->runCommand('scheduler:execute');

        $this->assertStringStartsWith('Start : Execute all scheduled command', $output);
        $this->assertRegExp('/debug:config should be executed/', $output);
        $this->assertRegExp('/Execute : debug:config/', $output);
    }
}