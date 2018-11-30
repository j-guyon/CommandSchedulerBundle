<?php

namespace JMose\CommandSchedulerBundle\Command;

use JMose\CommandSchedulerBundle\Entity\Repository\ExecutionRepository;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

    /** @var ExecutionRepository */
    private $executionRepo;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('schedulerTools:logrotate')
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
                'remove logs older than specified date, keep at least one, use format Y-M-D H:i:s',
                false
            )
            ->addOption(
                'days_limit',
                null,
                InputOption::VALUE_OPTIONAL,
                'remove logs older than given number of days, keep at least one',
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

        /** @var ExecutionRepository $repo */
        $this->executionRepo = $this->getRepository('Execution');

        if (
            (($this->limit = $this->input->getOption('days_limit')) !== false) &&
            is_numeric($this->limit) &&
            ($this->limit > 0)
        ) {
            $limit = strtotime('-' . $this->limit . ' days');
            $this->limit = new \DateTime(date('Y-m-d 00:00:00', $limit));

            $this->action = 'date';
        } else if (
            (($this->limit = $this->input->getOption('date_limit')) !== false) &&
            preg_match("/\\d+-\\d+-\\d+ \\d+:\\d+:\\d+/", $this->limit)
        ) {
            $this->limit = new \DateTime($this->limit);

            $this->action = 'date';
        } else if (
            (($this->limit = $this->input->getOption('nr_limit')) !== false) &&
            is_numeric($this->limit)
        ) {
            $this->action = 'number';
        } else if (
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
        $message = '';

        if ($this->action) {
            $output->writeln('<info>' . $this->action . ' is configured</info>');

            switch ($this->action) {
                case 'date':
                    $this->dateAction();
                    break;
                case 'number':
                    $this->numberAction();
                    break;
                case 'truncate':
                    $this->truncateAction();
                    break;
            }
        } else {
            $message = 'no action configured';
        }

        $this->endExecution($message);
    }

    /**
     * remove logs older than specified date but keep at least one
     */
    private function dateAction()
    {
        $this->executionRepo->deleteExecutionsForCommandsDateLimit($this->limit);
    }

    /**
     * remove ALL execution logs
     */
    private function truncateAction()
    {
        $this->executionRepo->truncateExecutions();
    }

    /**
     * remove all execution logs for every command except specified number. if there are less entries none will be removed. At least one entry is kept all the times
     */
    private function numberAction()
    {
        // keep at least one entry
        if ($this->limit < 1) {
            $this->limit = 1;
        }

        /** @var ScheduledCommand $command */
        $this->executionRepo->deleteExecutionsForCommandsKeepLimit($this->limit);
    }

    /**
     * print completion message and optionally additional information
     *
     * @param string $message additional information
     * @param string $type type of output. can be 'info', 'comment' (default), 'error' and 'question'
     */
    private function endExecution($message = "", $type = 'comment')
    {
        if ($message) {
            $this->output->writeln(sprintf("<%s>%s</%s>",
                    $type,
                    $message,
                    $type)
            );
        }
        $this->output->writeln("<info>Finished: Logrotate " . date('Y-m-d H:i:s') . "</info>");
    }
}
