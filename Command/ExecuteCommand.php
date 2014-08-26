<?php

namespace JMose\CommandSchedulerBundle\Command;

use Cron\CronExpression;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;

/**
 * Class ExecuteCommand : This class is the entry point to execute all scheduled command
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 * @package JMose\CommandSchedulerBundle\Command
 */
class ExecuteCommand extends ContainerAwareCommand
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('scheduler:execute')
            ->setDescription('Execute scheduled commands')
            ->addOption('dump', null, InputOption::VALUE_NONE, 'Display next execution')
            ->setHelp('This class is the entry point to execute all scheduled command');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Start : ' . ($input->getOption('dump') ? 'Dump' : 'Execute') . ' all scheduled command</info>');

        $this->em         = $this->getContainer()->get('doctrine')->getManager();
        $scheduledCommand = $this->em->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->findEnabledCommand();

        foreach ($scheduledCommand as $command) {

            /** @var ScheduledCommand $command */
            $cron        = CronExpression::factory($command->getCronExpression());
            $nextRunDate = $cron->getNextRunDate($command->getLastExecution());
            $now         = new \DateTime();

            if ($command->isExecuteImmediately()) {
                $output->writeln(
                    'Immediately execution asked for : <comment>' . $command->getCommand() . '</comment>'
                );

                if (!$input->getOption('dump')) {
                    $this->executeCommand($command, $output, $input);
                    $command->setExecuteImmediately(false);
                }
            } elseif ($nextRunDate < $now) {
                $output->writeln(
                    'Command <comment>' . $command->getCommand() . '</comment> should be executed - last execution : <comment>' .
                    $command->getLastExecution()->format('d/m/Y H:i:s') . '.</comment>'
                );

                if (!$input->getOption('dump')) {
                    $this->executeCommand($command, $output, $input);
                }
            }

            $this->em->merge($command);
            $this->em->flush();

            /*
             * This clear() is necessary to avoid conflict between commands and to be sure that none entity are managed
             * before entering in a new command
             */
            $this->em->clear();
        }
    }

    /**
     * @param ScheduledCommand $scheduledCommand
     * @param OutputInterface  $output
     * @param InputInterface   $input
     */
    private function executeCommand(ScheduledCommand $scheduledCommand, OutputInterface $output, InputInterface $input)
    {
        $scheduledCommand->setLocked(true);
        $this->em->flush();

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
                '--env'   => $input->getOption('env')
            ),
            $scheduledCommand->getArguments(true)
        ));

        // Use a StreamOutput to redirect write() and writeln() in a log file
        $logOutput = new StreamOutput(fopen(
            $this->getContainer()->getParameter('jmose_command_scheduler.log_path') .
            $scheduledCommand->getLogFile(), 'a', false
        ));
        $logOutput->setVerbosity($output->getVerbosity());

        // Execute command and get return code
        try {
            $output->write('<info>Execute</info> : <comment>' . $scheduledCommand->getCommand() . $scheduledCommand->getArguments() . '</comment>');
            $result = $command->run($input, $logOutput);
        } catch (\Exception $e) {
            $logOutput->writeln($e->getMessage());
            $logOutput->writeln($e->getTraceAsString());
            $result = -1;
        }

        $scheduledCommand->setLastReturnCode($result);
        $scheduledCommand->setLastExecution(new \DateTime());
        $scheduledCommand->setLocked(false);
        $this->em->flush();

        unset($command);
        gc_collect_cycles();
    }
}
