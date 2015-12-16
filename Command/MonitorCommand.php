<?php

namespace JMose\CommandSchedulerBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;

/**
 * Class MonitorCommand : This class is used for monitoring scheduled commands if they run for too long or failed to execute
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 */
class MonitorCommand extends SchedulerBaseCommand
{

    /** @var string|array receiver for statusmail if an error occured */
    private $receiver;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('scheduler:monitor')
            ->setDescription('Monitor scheduled commands')
            ->addOption('dump', null, InputOption::VALUE_NONE, 'Display result before mailing (even if no receiver is set)')
            ->addOption('no-output', null, InputOption::VALUE_NONE, 'Disable output message from scheduler')
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

        $this->receiver = $this->getContainer()->getParameter('jmose_command_scheduler.monitor_mail');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Start : ' . ($this->dumpMode ? 'Dump' : 'Execute') . ' all scheduled command</info>');

        // Before continue, we check that the output file is valid and writable (except for gaufrette)
        if (false !== $this->logPath && strpos($this->logPath, 'gaufrette:') !== 0 && false === is_writable($this->logPath)) {
            $output->writeln(
                '<error>'.$this->logPath.
                ' not found or not writable. You should override `log_path` in your config.yml'.'</error>'
            );

            return;
        }

        $commands = $this->entityManager->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->findAll();

        $timeoutValue = $this->getContainer()->getParameter('jmose_command_scheduler.lock_timeout');

        $failed = array();
        $now = time();

        /** @var ScheduledCommand $command */
        foreach ($commands as $command) {
                // don't care about disabled commands
                if($command->isDisabled()) {
                    continue;
                }

                $executionTime = $command->getLastExecution();
                $executionTimestamp = $executionTime->getTimestamp();

                $timedOut = (($executionTimestamp + $timeoutValue) < $now);

                if(
                    ($command->getLastReturnCode() != 0) || // last return code not OK
                    (
                        $command->getLocked() &&
                        (
                            ($timeoutValue === false) || // don't check for timeouts -> locked is bad
                            $timedOut // check for timeouts, but (starttime + timeout) is in the past
                        )
                    )
                ) {
                    $failed[$command->getName()] = array(
                        'LAST_RETURN_CODE' => $command->getLastReturnCode(),
                        'B_LOCKED' => $command->getLocked() ? 'true' : 'false',
                        'DH_LAST_EXECUTION' => $executionTime
                    );
                }
        }

        if (count($failed)) { // errors occured
            $message = "";

            foreach($failed as $commandName => $fail) {
                $message .= sprintf("%s: returncode %s, locked: %s, last execution: %s\n",
                    $commandName,
                    $fail['LAST_RETURN_CODE'],
                    $fail['B_LOCKED'],
                    $fail['DH_LAST_EXECUTION']->format('Y-m-d H:i')
                );
            }

            if($this->dumpMode) {
                $output->writeln($this->dumpMode);
            }

            // send mail to every receiver
            if(count($this->receiver)) {
                $this->sendMails($message);
            }
        } else {// no errors
            $sendOk = $this->getContainer()->getParameter('jmose_command_scheduler.send_ok');

            if($sendOk) {
                $this->sendMails('no errors found');
            }
            if($this->dumpMode) { // dumpmode
                $output->writeln('Nothing to do.');
            }
        }
    }

    /**
     * send message to email receivers
     *
     * @param string $message message to be sent
     */
    private function sendMails($message) {
        // prepare email constants
        $hostname = gethostname();
        $subject = "cronjob monitoring " . $hostname . ", " . date('Y-m-d H:i:s');
        $headers = 'From: cron-monitor@' . $hostname . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        foreach($this->receiver as $rcv) {
            mail(trim($rcv), $subject, $message, $headers);
        }
    }
}
