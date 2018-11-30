<?php

namespace JMose\CommandSchedulerBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MonitorCommand : This class is used for monitoring scheduled commands if they run for too long or failed to execute
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 * @package JMose\CommandSchedulerBundle\Command
 */
class MonitorCommand extends SchedulerBaseCommand
{

    /** @var string|array receiver for statusmail if an error occured */
    private $receiver;

    /**
     * @var boolean if true, current command will send mail even if all is ok.
     */
    private $sendMailIfNoError;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('schedulerTools:monitor')
            ->setDescription('Monitor scheduled commands')
            ->addOption(
                'dump',
                null,
                InputOption::VALUE_NONE,
                'Display result instead of send mail'
            )
            ->setHelp('This class is for monitoring all active commands.');
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

        $this->lockTimeout = $this->getContainer()->getParameter('jmose_command_scheduler.lock_timeout');

        $this->receiver = $this->getContainer()->getParameter('jmose_command_scheduler.monitor_mail');
        $this->sendMailIfNoError = $this->getContainer()->getParameter('jmose_command_scheduler.send_ok');

        $this->em = $this->getContainer()->get('doctrine')->getManager(
            $this->getContainer()->getParameter('jmose_command_scheduler.doctrine_manager')
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // If not in dump mode and none receiver is set, exit.
        if (!$this->dumpMode && count($this->receiver) === 0) {
            $output->writeln('Please add receiver in configuration');
            return;
        }

        // Before continue, we check that the output file is valid and writable (except for gaufrette)
        if (false !== $this->logPath && strpos($this->logPath, 'gaufrette:') !== 0 && false === is_writable($this->logPath)) {
            $output->writeln(
                '<error>'.$this->logPath.
                ' not found or not writable. You should override `log_path` in your config.yml'.'</error>'
            );

            return;
        }

        // Fist, get all failed or potential timeout
        $failedCommands = $this->getRepository('ScheduledCommand')
            ->findFailedAndTimeoutCommands($this->lockTimeout);

        // Commands in error
        if (count($failedCommands) > 0) {
            $message = "";

            foreach ($failedCommands as $command) {
                $message .= sprintf("%s: returncode %s, locked: %s, last execution: %s\n",
                    $command->getName(),
                    $command->getLastReturnCode(),
                    $command->getLocked(),
                    $command->getLastExecution()->format('Y-m-d H:i')
                );
            }

            // if --dump option, don't send mail
            if ($this->dumpMode) {
                $output->writeln($message);
            } else {
                $this->sendMails($message);
            }

        } else {
            if ($this->dumpMode) {
                $output->writeln('No errors found.');
            } elseif ($this->sendMailIfNoError) {
                $this->sendMails('No errors found.');
            }
        }
    }

    /**
     * Send message to email receivers
     *
     * @param string $message message to be sent
     */
    private function sendMails($message)
    {
        // prepare email constants
        $hostname = gethostname();
        $subject = "cronjob monitoring " . $hostname . ", " . date('Y-m-d H:i:s');
        $headers = 'From: cron-monitor@' . $hostname . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        foreach ($this->receiver as $rcv) {
            mail(trim($rcv), $subject, $message, $headers);
        }
    }
}
