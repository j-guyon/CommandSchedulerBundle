<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 26.12.15
 * Time: 16:32
 */

namespace JMose\CommandSchedulerBundle\Tests\Command;

use JMose\CommandSchedulerBundle\Command\LogRotateCommand;
use JMose\CommandSchedulerBundle\Tests\CommandSchedulerBaseTest;

class LogRotateCommandTest extends CommandSchedulerBaseTest
{
    /** @var string */
    private $command;
    /** @var string */
    private $commandName;
    /** @var string $noAction Pattern if command does nothing */
    private $noAction = '/no action configured/';
    /** @var string $rowFilter */
    private $rowFilter = 'tr.execution';

    /**
     * set up variables needed in every test
     */
    public function setUp()
    {
        $this->command = new LogRotateCommand();
        $this->commandName = 'schedulerTools:logrotate';

        parent::setUp();
    }

    /**
     * test command call without options
     */
    public function testRotateWithoutOption()
    {
        $output = $this->executeCommand(
            $this->command,
            $this->commandName
        );

        $this->assertRegExp($this->noAction, $output);
    }

    /** test for invalid inputs */
    public function testExecuteWithInvalidOptions()
    {
        $options = array(
            array(
                '--date_limit' => true
            ),
            array(
                '--date_limit' => 'lala'
            ),
            array(
                '--date_limit' => 12345
            ),
            array(
                '--date_limit' => date('D.M.Y')
            ),
            array(
                '--days_limit' => 0
            ),
            array(
                '--days_limit' => -1
            ),
            array(
                '--days_limit' => '0'
            ),
            array(
                '--days_limit' => '-1'
            ),
            array(
                '--days_limit' => 'asdf'
            ),
            array(
                '--nr_limit' => true
            ),
            array(
                '--nr_limit' => 'lala'
            ),
            array(
                '--truncate'
            ),
            array(
                '--truncate' => 'sepp'
            ),
            array(
                '--truncate' => true,
            ),
            array(
                '--truncate' => true,
                '--verify' => 'lala'
            )
        );

        foreach ($options as $opt) {
            $output = $this->executeCommand(
                $this->command,
                $this->commandName,
                $opt
            );

            $this->assertRegExp($this->noAction, $output);
        }
    }

    /**
     * test empty cleanup with date
     */
    public function testDateActionWithoutCommands()
    {
        $output = $this->executeCommand(
            $this->command,
            $this->commandName,
            array(
                '--date_limit' => date('Y-m-d H:i:s')
            ));

        $this->assertRegExp('/date is configured/', $output);
    }

    /**
     * test empty cleanup with days
     */
    public function testDayActionWithoutCommands()
    {
        $output = $this->executeCommand(
            $this->command,
            $this->commandName,
            array(
                '--date_limit' => date('Y-m-d H:i:s')
            ));

        $this->assertRegExp('/date is configured/', $output);
    }

    /**
     * test empty cleanup with number
     */
    public function testNumberActionWithoutCommands()
    {
        $output = $this->executeCommand(
            $this->command,
            $this->commandName,
            array(
                '--nr_limit' => 1
            )
        );

        $this->assertRegExp('/number is configured/', $output);
    }

    /**
     * test empty truncate
     */
    public function testTruncateActionWithoutCommands()
    {
        $output = $this->executeCommand(
            $this->command,
            $this->commandName,
            array(
                '--truncate' => true,
                '--verify' => true
            )
        );

        $this->assertRegExp('/truncate is configured/', $output);
    }

    /**
     * test cleanup with date
     */
    public function testDateAction()
    {
        $this->loadDataFixtures();

        $now = new \DateTime();
        $now = $now->modify('-1d');

        $output = $this->executeCommand(
            $this->command,
            $this->commandName,
            array(
                '--date_limit' => $now->format('Y-m-d H:i:s')
            )
        );
        $crawler = $this->loadPage();
        $count = $crawler->filter($this->rowFilter)->count();

        $this->assertEquals(2, $count);
    }

    /**
     * test cleanup with days
     */
    public function testDaysAction()
    {
        $this->loadDataFixtures();

        $output = $this->executeCommand(
            $this->command,
            $this->commandName,
            array(
                '--days_limit' => 1 // we can't use 0 here
            )
        );
        $crawler = $this->loadPage();
        $count = $crawler->filter($this->rowFilter)->count();

        // remove all logs before yesterday, assume there was an execution today -> 4 logs
        $this->assertEquals(4, $count);
    }

    /**
     * test cleanup with number
     */
    public function testNumberAction()
    {
        $this->loadDataFixtures();

        $output = $this->executeCommand(
            $this->command,
            $this->commandName,
            array(
                '--nr_limit' => 2
            )
        );
        $crawler = $this->loadPage();
        $count = $crawler->filter($this->rowFilter)->count();

        $this->assertEquals(4, $count);
    }

    /**
     * test truncate
     */
    public function testTrunateAction()
    {
        $this->loadDataFixtures();

        $now = new \DateTime();
        $now = $now->modify('-1d');

        $output = $this->executeCommand(
            $this->command,
            $this->commandName,
            array(
                '--truncate' => true,
                '--verify' => true
            )
        );
        $crawler = $this->loadPage();
        $count = $crawler->filter($this->rowFilter)->count();

        $this->assertEquals(0, $count);
    }

    /**
     * fetch list of executions
     *
     * @return mixed
     */
    protected function loadPage()
    {
        $crawler = $this->callUrl(
            'GET',
            'command-scheduler/list/executions'
        );

        return $crawler;
    }
}
