<?php

namespace JMose\CommandSchedulerBundle\Tests\Fixtures;

use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class AbstractTestCase extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * @param array $options
     */
    protected static function bootKernel(array $options = array())
    {
        if (method_exists('Symfony\Bundle\FrameworkBundle\Test\KernelTestCase', 'bootKernel')) {
            // Not available in older versions of Symfony, that's why we check method existence.
            parent::bootKernel($options);
        } else {
            if (null !== static::$kernel) {
                static::$kernel->shutdown();
            }
            static::$kernel = static::createKernel($options);
            static::$kernel->boot();
            static::$kernel;
        }
    }

    /**
     * @param array $options An array of options to pass to the createKernel class
     * @return KernelInterface
     */
    protected function getKernel(array $options = array())
    {
        static::bootKernel($options);
        return static::$kernel;
    }

    /**
     * Recreates the database for each tests.
     * Pros: you can use one single test to process the database entirely.
     * Cons: Slower execution.
     * Feel free to change it if it does not fit.
     *
     * @throws \Exception
     */
    public function setUp()
    {
        $kernel = static::getKernel();

        $databaseFile = $kernel->getContainer()->getParameter('database_path');
        $application = new Application($kernel);

        if (file_exists($databaseFile)) {
            unlink($databaseFile);
        }

        // Create database
        $command = new CreateDatabaseDoctrineCommand();
        $application->add($command);
        $input = new ArrayInput(array('command' => 'doctrine:database:create',));
        $command->run($input, new NullOutput());

        // Create database schema
        $command = new CreateSchemaDoctrineCommand();
        $application->add($command);
        $input = new ArrayInput(array('command' => 'doctrine:schema:create',));
        $command->run($input, new NullOutput());
    }

}
