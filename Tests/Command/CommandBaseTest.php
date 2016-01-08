<?php
/**
 * Base Test for testing commands
 */

namespace JMose\CommandSchedulerBundle\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CommandBaseTest extends \PHPUnit_Framework_TestCase
{
    protected $application;

    /** prepare tests */
    public function setUp()
    {
        $this->application = new Application();
    }

    /**
     * Execute a command and return the outputs
     *
     * @param ContainerAwareCommand $commandClass instance of command to be tested
     * @param string $name command name
     * @param array $options options to be used for execution
     *
     * @return string output
     */
    public function runCommand($commandClass, $name, $options = array())
    {
        $this->application->add($commandClass);

        $command = $this->application->find($name);

        $commandTester = new CommandTester($command);
        $commandTester->execute($options);

        return $commandTester->getDisplay();
    }
}
