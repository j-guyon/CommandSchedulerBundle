<?php

namespace JMose\CommandSchedulerBundle\Tests\Command;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class MonitorCommandTest
 * @package JMose\CommandSchedulerBundle\Tests\Command
 */
class MonitorCommandTest extends WebTestCase
{
    /**
     * Test scheduler:execute without option
     */
    public function testExecuteWithError()
    {
        //DataFixtures create 4 records
        $this->loadFixtures(
            array(
                'JMose\CommandSchedulerBundle\Fixtures\ORM\LoadScheduledCommandData'
            )
        );

        // One command is locked in fixture (2), another have a -1 return code as lastReturn (4)
        $output = $this->runCommand(
            'scheduler:monitor',
            array(
                '--dump' => true
            )
        );

        $this->assertRegExp('/two:/', $output);
        $this->assertRegExp('/four:/', $output);
    }

}
