<?php

namespace JMose\CommandSchedulerBundle\Command;

use Cron\CronExpression;
use JMose\CommandSchedulerBundle\Entity\Execution;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use JMose\CommandSchedulerBundle\Component\CommandOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Class ExecuteCommand : This class is the entry point to execute all scheduled command
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Command
 */
class ExecuteCommand extends SchedulerBaseCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('scheduler:execute')
            ->setDescription('Execute scheduled commands')
            ->addOption('dump', null, InputOption::VALUE_NONE, 'Display next execution')
            ->addOption('no-output', null, InputOption::VALUE_NONE, 'Disable output message from scheduler')
            ->setHelp('This class is the entry point to execute all scheduled command');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Start : ' . ($this->dumpMode ? 'Dump' : 'Execute') . ' all scheduled command</info>');

        // Before continue, we check that the output file is valid and writable (except for gaufrette)
        if (
            false !== $this->logPath &&
            strpos($this->logPath, 'gaufrette:') !== 0 &&
            false === is_writable($this->logPath)
        ) {
            $output->writeln(
                '<error>' . $this->logPath .
                ' not found or not writable. You should override `log_path` in your config.yml' . '</error>'
            );

            return;
        }

        $commands = $this->getRepository('ScheduledCommand')->findEnabledCommand();

        $noneExecution = true;
        foreach ($commands as $command) {

            /** @var ScheduledCommand $command */
            // check if the command's rights (user and host) allow execution of the command at all.
            if (!$command->checkRights()) {
                continue;
            }

            $cron = CronExpression::factory($command->getCronExpression());
            $nextRunDate = $cron->getNextRunDate($command->getLastExecution());
            $now = new \DateTime();

            if ($command->isExecuteImmediately()) {
                $noneExecution = false;
                $output->writeln(
                    'Immediately execution asked for : <comment>' . $command->getCommand() . ' ' . $command->getArguments() . '</comment>'
                );

                if (!$input->getOption('dump')) {
                    $this->executeCommand($command, $output, $input);
                }
            } elseif ($nextRunDate < $now) {
                $noneExecution = false;
                $output->writeln(
                    'Command <comment>' . $command->getCommand() .
                    '</comment> should be executed - last execution : <comment>' .
                    $command->getLastExecution()->format('d/m/Y H:i:s') . '.</comment>'
                );

                if (!$input->getOption('dump')) {
                    $this->executeCommand($command, $output, $input);
                }
            }
        }

        if (true === $noneExecution) $output->writeln('Nothing to do.');
    }

    /**
     * @param ScheduledCommand $scheduledCommand
     * @param OutputInterface $output
     * @param InputInterface $input
     */
    private function executeCommand(ScheduledCommand $scheduledCommand, OutputInterface $output, InputInterface $input)
    {
        $now = new \DateTime();
        /** @var ScheduledCommand $scheduledCommand */
        $scheduledCommand = $this->entityManager->merge($scheduledCommand);
        $scheduledCommand->setLastExecution($now);
        $scheduledCommand->setLocked(true);

        if ($scheduledCommand->logExecutions()) {
            $log = new Execution();
            $log->setCommand($scheduledCommand);
            $log->setExecutionDate($now);
            $this->entityManager->persist($log);
            $scheduledCommand->addLog($log);
        }

        $this->entityManager->flush();

        try {
            $command = $this->getApplication()->find($scheduledCommand->getCommand());
        } catch (\InvalidArgumentException $e) {
            $scheduledCommand->setLastReturnCode(-1);
            $output->writeln('<error>Cannot find ' . $scheduledCommand->getCommand() . '</error>');

            return;
        }

        $input = new ArrayInput(array_merge(
            array(
                'command' => $scheduledCommand->getCommand(),
                '--env' => $input->getOption('env')
            ),
            $scheduledCommand->getArguments(true)
        ));

        // Use a StreamOutput or NullOutput to redirect write() and writeln() in a log file
        if (
            (false === $this->logPath) ||
            ("" == $scheduledCommand->getLogFile()) ||
            ('null' == $scheduledCommand->getLogFile()) ||
            false
        ) {
            $logOutput = new NullOutput();
        } else {
            $logOutput = new StreamOutput(
                fopen(
                    $this->logPath . $scheduledCommand->getLogFile(),
                    'a',
                    false
                ),
                $this->commandsVerbosity
            );
        }

        $commandOutput = new CommandOutput();
        $commandOutput->setDefaultOutput($logOutput);

        // Execute command and get return code
        try {
            $output->writeln('<info>Execute</info> : <comment>' . $scheduledCommand->getCommand()
                . ' ' . $scheduledCommand->getArguments() . '</comment>');
            $result = $command->run($input, $commandOutput);
        } catch (\Exception $e) {
            $logOutput->writeln($e->getMessage());
            $logOutput->writeln($e->getTraceAsString());
            $result = -1;
        }

        if (false === $this->entityManager->isOpen()) {
            $output->writeln('<comment>Entity manager closed by the last command.</comment>');
            $this->entityManager = $this->entityManager->create($this->entityManager->getConnection(), $this->entityManager->getConfiguration());
        }

        $scheduledCommand = $this->entityManager->merge($scheduledCommand);
        $scheduledCommand->setLastReturnCode($result);
        $scheduledCommand->setLocked(false);
        $scheduledCommand->setExecuteImmediately(false);

        if ($scheduledCommand->logExecutions()) {
            /** @var Execution $log */
            $log = $scheduledCommand->getCurrentLog();
            $log->setReturnCode($result);
            $log->setOutput($commandOutput->getBuffer('string'));

            // calculate runtime in seconds
            $now = new \DateTime();
            $runtime = $now->getTimestamp() - $log->getExecutionDate()->getTimestamp();
            $log->setRuntime($runtime);
        }

        $this->entityManager->flush();

        /*
         * This clear() is necessary to avoid conflict between commands and to be sure that none entity are managed
         * before entering in a new command
         */
        $this->entityManager->clear();

        unset($command);
        gc_collect_cycles();
    }
}
