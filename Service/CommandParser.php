<?php

namespace JMose\CommandSchedulerBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CommandChoiceList
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Form
 */
class CommandParser
{
    /**
     * KernelInterface
     * @var KernelInterface $kernel
     */
    private $kernel;

    /**
     * Array with namespaces to exclude
     *
     * @var array $excludedNamespaces
     */
    private $excludedNamespaces;

    /**
     * EntityManagerInterface
     *
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * CommandParser constructor.
     *
     * @param KernelInterface        $kernel             The kernel
     * @param EntityManagerInterface $em                 The entity manager
     * @param array                  $excludedNamespaces Array with namespaces to exclude
     */
    public function __construct(
        KernelInterface $kernel,
        EntityManagerInterface $em,
        array $excludedNamespaces = array()
    ) {
        $this->kernel = $kernel;
        $this->em = $em;
        $this->excludedNamespaces = $excludedNamespaces;
    }

    /**
     * Execute the console command "list" with XML output to have all available command
     *
     * @return array
     * @throws \Exception
     */
    public function getCommands()
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(
            array(
                'command' => 'list',
                '--format' => 'xml'
            )
        );

        $output = new StreamOutput(fopen('php://memory', 'w+'));
        $application->run($input, $output);
        rewind($output->getStream());

        return $this->extractCommandsFromXML(stream_get_contents($output->getStream()));
    }

    /**
     * Execute the console command "list" with XML output to have all available command
     *
     * @return array
     * @throws \Exception
     */
    public function getAllCommands()
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(
            array(
                'command' => 'list',
                '--format' => 'xml'
            )
        );

        $output = new StreamOutput(fopen('php://memory', 'w+'));
        $application->run($input, $output);
        rewind($output->getStream());


        return $this->extractCommandsFromXmlWithDescription(
            stream_get_contents($output->getStream()),
            $this->getCommandsUsed()
        );
    }

    /**
     * Extract an array of available Symfony command from the XML output
     *
     * @param $xml
     *
     * @return array
     */
    private function extractCommandsFromXML($xml)
    {
        if ($xml == '') {
            return array();
        }

        $node = new \SimpleXMLElement($xml);
        $commandsList = array();


        foreach ($node->namespaces->namespace as $namespace) {
            $namespaceId = (string)$namespace->attributes()->id;

            if (!in_array($namespaceId, $this->excludedNamespaces)) {
                foreach ($namespace->command as $command) {
                    $commandsList[$namespaceId][(string)$command] = (string)$command;
                }
            }
        }

        return $commandsList;
    }

    /**
     * List of commands used
     *
     * @return array
     */
    public function getCommandsUsed()
    {
        $return = [];
        $commands = $this->em->getRepository(ScheduledCommand::class)->findAll();

        foreach ($commands as $command) {
            $return[$command->getCommand()] = ['status' => !$command->isDisabled()];
        }

        return $return;
    }

    /**
     * Extract an array of available Symfony command from the XML output
     *
     * @param       $xml
     * @param array $commandUsed
     *
     * @return array
     */
    private function extractCommandsFromXmlWithDescription($xml, array $commandUsed)
    {
        if ($xml == '') {
            return array();
        }

        $node = new \SimpleXMLElement($xml);
        $commandsList = array();

        $this->excludedNamespaces[] = 'about';
        $this->excludedNamespaces[] = 'help';
        $this->excludedNamespaces[] = 'list';

        foreach ($node->commands->command as $namespace) {
            $namespaceId = (string)$namespace->attributes()->id;
            $desc = (string)$namespace->description;

            if (!in_array(explode(':', $namespaceId)[0], $this->excludedNamespaces)) {
                $return = ['id' => $namespaceId, 'description' => $desc];

                if (isset($commandUsed[$namespaceId])) {
                    $return += $commandUsed[$namespaceId];
                }

                $commandsList[explode(':', $namespaceId)[0]][] = $return;
            }
        }

        return $commandsList;
    }
}
