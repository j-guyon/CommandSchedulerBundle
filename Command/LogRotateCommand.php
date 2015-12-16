<?php

namespace JMose\CommandSchedulerBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Entity\Repository\ScheduledCommandRepository;
use JMose\CommandSchedulerBundle\Entity\Repository\ExecutionRepository;

/**
 * Class LogRotateCommand : This class rotates (deletes) old Executionlogs
 *
 * @author Daniel Fischer <dfischer000@gmail.com>
 */
class LogRotateCommand extends SchedulerBaseCommand
{


    /** @var string $action action to be executed */
    private $action = '';

    /** @var string $limit */
    private $limit = "";

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('scheduler:logrotate')
            ->setDescription('Cleanup execution logs')
            ->addOption(
                'no-output',
                null,
                InputOption::VALUE_NONE,
                'Disable output messages'
            )
            ->addOption(
                'date_limit',
                null,
                InputOption::VALUE_OPTIONAL,
                'remove logs older than specified date but keep at least one, use format Y-M-D H:i:s',
                false
            )
            ->addOption(
                'nr_limit',
                null,
                InputOption::VALUE_OPTIONAL,
                'keep specified number of entries per command, but at least one',
                false
            )
            ->addOption(
                'truncate',
                null,
                InputOption::VALUE_OPTIONAL,
                'remove all execution logs; option has to be "true" and combined with "--verify=true',
                false
            )
            ->addOption(
                'verify',
                null,
                InputOption::VALUE_OPTIONAL,
                'verify removal of all execution logs',
                false
            )
            ->setHelp('This class removes old execution logs');
    }


    /**
     * Initialize parameters and services used in execute function
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        if (true === $this->input->getOption('no-output')) {
            $this->output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        $this->entityManager = $this->getContainer()->get('doctrine')
            ->getManager(
                $this->getContainer()->getParameter('jmose_command_scheduler.doctrine_manager')
            );

        if (
            (($this->limit = $this->input->getOption('date_limit')) !== false) &&
            preg_match("/\\d+-\\d+-\\d+ \\d+:\\d+:\\d+/", $this->limit)
        ) {
            $this->action = 'date';
            $this->limit = strtotime($this->input->getOption('date_limit'));
        } else if (
            (($this->limit = $this->input->getOption('nr_limit')) !== false) &&
            is_numeric($this->limit)
        ) {
            $this->action = 'number';
        } else if(
            ($this->input->getOption('truncate') == 'true') &&
            ($this->input->getOption('verify') == 'true')
        ) {
            $this->action = 'truncate';
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Start: Logrotate</info>');

        $action = '';
        if($this->action) {
            $output->writeln('<info>' .  $this->action . ' is configured</info>');
            $action = ($this->action . 'Action');

            $this->$action();
        } else {
            $this->endExecution('no action configured');
        }
    }

    /**
     * remove all execution logs for every command except specified number, at least one
     */
    private function numberAction() {
        $commands = $this->entityManager->getRepository($this->bundleName . ':ScheduledCommand')->findAll();

        $delete = array();
        /** @var ScheduledCommand $command */
        foreach($commands as $command) {
            // get all executions
            $executions = $command->getExecutions();
            $executions = $executions->toArray();

            // calculate how many entries are to be removed
            $itemCount = count($executions) - $this->limit;

            if($itemCount > 0) {
                // collect log entries
                $delete = array_merge(
                    $delete,
                    array_slice($executions, 0, $itemCount)
                );
            }
        }

        // now we have every execution log to be removed - let's rock
        foreach($delete as $item){
            $this->entityManager->remove($item);
        }
        $this->entityManager->flush();
    }

    /**
     * print completion message and optionally additional information
     *
     * @param string $message additional information
     * @param string $type type of output. can be 'info', 'comment' (default), 'error' and 'question'
     */
    private function endExecution($message = "", $type = 'comment'){
        if($message) {
            $this->output->writeln(sprintf("<%s>%s</%s>",
                $type,
                $message,
                $type)
            );
        }
        $this->output->writeln("<info>Finished: Logrotate " . date('Y-M-D H:i:s') . "</info>");
    }
}
