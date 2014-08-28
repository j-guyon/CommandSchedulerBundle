<?php
namespace JMose\CommandSchedulerBundle\Form;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Form\Extension\Core\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class CommandChoiceList
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Form
 */
class CommandChoiceList extends LazyChoiceList
{

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var array
     */
    private $excludedNamespaces;

    /**
     * @param Kernel $kernel
     * @param array  $excludedNamespaces
     */
    public function __construct(Kernel $kernel, array $excludedNamespaces)
    {
        $this->kernel             = $kernel;
        $this->excludedNamespaces = $excludedNamespaces;
    }

    /**
     * Execute the console commande "list" with XML output to have all available command
     *
     * @return \Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface|SimpleChoiceList
     */
    protected function loadChoiceList()
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(
            array(
                'command'  => 'list',
                '--format' => 'xml'
            )
        );

        $output = new StreamOutput(fopen('php://memory', 'w+'));
        $application->run($input, $output);
        rewind($output->getStream());

        return new SimpleChoiceList($this->extractCommandsFromXML(stream_get_contents($output->getStream())));
    }

    /**
     * Extract an array of available symfony command from the XML output
     *
     * @param $xml
     * @return array
     */
    private function extractCommandsFromXML($xml)
    {
        if ($xml == '') {
            return array();
        }

        $node         = new \SimpleXMLElement($xml);
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

}
