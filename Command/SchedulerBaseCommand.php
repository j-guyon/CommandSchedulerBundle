<?php
/**
 * Base command for scheduler bundle. Contains constants used in the bundle's commands.
 */
namespace JMose\CommandSchedulerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SchedulerBaseCommand extends ContainerAwareCommand {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /** @var string $bundleName */
    protected $bundleName = 'JMoseCommandSchedulerBundle';

    /** @var InputInterface $input */
    protected $input = null;

    /** @var OutputInterface $output */
    protected $output = null;

    /**
     * @var string
     */
    protected $logPath;

    /**
     * @var boolean
     */
    protected $dumpMode;

    /**
     * @var integer
     */
    protected $commandsVerbosity;

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        if( true === $input->getOption('no-output')){
            $output->setVerbosity( OutputInterface::VERBOSITY_QUIET );
        }

        $this->dumpMode = $input->getOption('dump');
        $this->logPath = rtrim($this->getContainer()->getParameter('jmose_command_scheduler.log_path'), '/\\');

        // set logpath to false if specified in parameters to suppress logging
        if(("false" == $this->logPath)||(false == $this->logPath)) {
            $this->logPath = false;
        } else {
            $this->logPath .= DIRECTORY_SEPARATOR;
        }

        // store the original verbosity before apply the quiet parameter
        $this->commandsVerbosity = $output->getVerbosity();

        if( true === $input->getOption('no-output')){
            $output->setVerbosity( OutputInterface::VERBOSITY_QUIET );
        }

        // all configuration to input and output done, save references
        $this->input = $input;
        $this->output = $output;

        // get doctrine manager
        $this->em = $this->getContainer()->get('doctrine')->getManager(
            $this->getContainer()->getParameter('jmose_command_scheduler.doctrine_manager')
        );
    }
}
