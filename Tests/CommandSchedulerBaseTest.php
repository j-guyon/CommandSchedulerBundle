<?php
/**
 * Base test class
 */

namespace JMose\CommandSchedulerBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Command\ContainerDebugCommand;
use Symfony\Bundle\FrameworkBundle\Command\RouterDebugCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;

define('NUMBER_COMMANDS_TOTAL', 4);
define('NUMBER_COMMANDS_ACTIVE', 3);
define('NUMBER_COMMANDS_INACTIVE', (NUMBER_COMMANDS_TOTAL - NUMBER_COMMANDS_ACTIVE));
define('NUMBER_COMMANDS_LOCKED', 1);

define('NUMBER_RIGHTS_TOTAL', 7);

class CommandSchedulerBaseTest extends WebTestCase
{
    /** @var  Application */
    protected $application;

    /**
     * prepare test environment
     */
    protected function setUp()
    {
        // prepare test environment for command testing
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->application = new Application($kernel);
        // add two (actually 4) commands to application for testing purposes
        $this->application->add(new ContainerDebugCommand());
        $this->application->add(new RouterDebugCommand());

        $this->decorated = false;

        // prepare database
        $this->dropDatabase(); // remove database just to be safe

        // (re)create database
        self::runCommand('doctrine:database:create');
        // setup database schema
        self::runCommand('doctrine:schema:update',
            array(
                '--force' => true
            )
        );
        // no need to load fixtures here, they will be loaded be each test on demand
    }

    /**
     * Tests completed, clean up
     *
     * TODO: enable as soon as all tests work as expected
     */
//    protected function tearDown()
//    {
//        $this->dropDatabase();
//
//        parent::tearDown();
//    }

    /**
     * drop database
     */
    private function dropDatabase()
    {
        self::runCommand('doctrine:database:drop',
            array(
                '--force' => true
            )
        );
    }

    /**
     * call a URL, fill form with values and submit form
     *
     * @param string $method GET|POST
     * @param string $url
     * @param array $values form values
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function callFormUrlValues($method, $url, $values)
    {
        $client = parent::createClient();
        $client->followRedirects(true);
        $crawler = $client->request($method, $url);
        $buttonCrawlerNode = $crawler->selectButton('Save');
        $form = $buttonCrawlerNode->form();

        $form->setValues($values);
        $crawler = $client->submit($form);

        return $crawler;
    }

    /**
     * call a URL, follow redirects
     *
     * @param string $method GET|POST
     * @param string $url
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function callUrl($method, $url)
    {
        $client = parent::createClient();
        $client->followRedirects(true);
        $crawler = $client->request($method, $url);

        return $crawler;
    }


    /**
     * Execute a command and return the outputs
     *
     * @param ContainerAwareCommand $commandClass instance of command to be tested
     * @param string $name command name
     * @param array $options options to be used for execution
     * @param int $exitCode Reference, will be set to exit code
     *
     * @return string output
     */
    public function executeCommand($commandClass, $name, $options = array(), &$exitCode = null)
    {
        $this->application->add($commandClass);

        $command = $this->application->find($name);

        $commandTester = new CommandTester($command);
        $return = $commandTester->execute($options);

        // $exitCode is defined (something), set to commands exit code
        if($exitCode !== null) {
            $exitCode = $return;
        }

        return $commandTester->getDisplay();
    }

    /**
     * load command fixtures
     */
    protected function loadDataFixtures() {
        //DataFixtures create 4 records
        $this->loadFixtures(array(
            'JMose\CommandSchedulerBundle\DataFixtures\ORM\LoadTestData'
        ));
    }

//    /**
//     * load rights fixtures
//     */
//    protected function loadRightsFixtures() {
//        //DataFixtures create 8 records
//        $this->loadFixtures(array(
//            'JMose\CommandSchedulerBundle\DataFixtures\ORM\LoadUserHostData'
//        ));
//    }

    /**
     * dummy test so there is a test in every child test
     */
    public function testNothing(){
        $this->assertTrue(true);
    }
}
