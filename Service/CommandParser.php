<?php

namespace JMose\CommandSchedulerBundle\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CommandChoiceList.
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 */
class CommandParser
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var array
     */
    private $excludedNamespaces;

    /**
     * @var array
     */
    private $includedNamespaces;

    /**
     * CommandParser constructor.
     *
     * @param KernelInterface $kernel
     * @param array           $excludedNamespaces
     * @param array           $includedNamespaces
     */
    public function __construct(KernelInterface $kernel, array $excludedNamespaces = [], array $includedNamespaces = [])
    {
        $this->kernel = $kernel;
        $this->excludedNamespaces = $excludedNamespaces;
        $this->includedNamespaces = $includedNamespaces;

        if (count($this->excludedNamespaces) > 0 && count($this->includedNamespaces) > 0) {
            throw new \InvalidArgumentException('Cannot combine excludedNamespaces with includedNamespaces');
        }
    }

    /**
     * Execute the console command "list" with XML output to have all available command.
     *
     * @return array
     */
    public function getCommands()
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(
            [
                'command' => 'list',
                '--format' => 'xml',
            ]
        );

        $output = new StreamOutput(fopen('php://memory', 'w+'));
        $application->run($input, $output);
        rewind($output->getStream());

        return $this->extractCommandsFromXML(stream_get_contents($output->getStream()));
    }

    /**
     * Extract an array of available Symfony command from the XML output.
     *
     * @param $xml
     *
     * @return array
     */
    private function extractCommandsFromXML($xml)
    {
        if ('' == $xml) {
            return [];
        }

        $node = new \SimpleXMLElement($xml);
        $commandsList = [];

        foreach ($node->namespaces->namespace as $namespace) {
            $namespaceId = (string) $namespace->attributes()->id;

            if (
                (count($this->excludedNamespaces) > 0 && in_array($namespaceId, $this->excludedNamespaces))
                ||
                (count($this->includedNamespaces) > 0 && !in_array($namespaceId, $this->includedNamespaces))
            ) {
                continue;
            }

            foreach ($namespace->command as $command) {
                $commandsList[$namespaceId][(string) $command] = (string) $command;
            }
        }

        return $commandsList;
    }
}
