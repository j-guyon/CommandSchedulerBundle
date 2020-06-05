<?php

namespace JMose\CommandSchedulerBundle\Command;

use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MonitorCommand : This class is used for monitoring scheduled commands if they run for too long or failed to execute
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 * @package JMose\CommandSchedulerBundle\Command
 */
class MonitorCommand extends Command
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var boolean
     */
    private $dumpMode;

    /**
     * @var integer|boolean Number of seconds after a command is considered as timeout
     */
    private $lockTimeout;

    /**
     * @var string|array receiver for statusmail if an error occured
     */
    private $receiver;

    /**
     * @var string mailSubject subject to be used when sending a mail
     */
    private $mailSubject;

    /**
     * @var boolean if true, current command will send mail even if all is ok.
     */
    private $sendMailIfNoError;

    /**
     * MonitorCommand constructor.
     * @param ManagerRegistry $managerRegistry
     * @param $managerName
     * @param $lockTimeout
     * @param $receiver
     * @param $mailSubject
     * @param $sendMailIfNoError
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        $managerName,
        $lockTimeout,
        $receiver,
        $mailSubject,
        $sendMailIfNoError
    ) {
        $this->em = $managerRegistry->getManager($managerName);
        $this->lockTimeout = $lockTimeout;
        $this->receiver = $receiver;
        $this->mailSubject = $mailSubject;
        $this->sendMailIfNoError = $sendMailIfNoError;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('scheduler:monitor')
            ->setDescription('Monitor scheduled commands')
            ->addOption('dump', null, InputOption::VALUE_NONE, 'Display result instead of send mail')
            ->setHelp('This class is for monitoring all active commands.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // If not in dump mode and none receiver is set, exit.
        $this->dumpMode = $input->getOption('dump');
        if (!$this->dumpMode && count($this->receiver) === 0) {
            $output->writeln('Please add receiver in configuration');

            return 1;
        }

        // Fist, get all failed or potential timeout
        $failedCommands = $this->em->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')
            ->findFailedAndTimeoutCommands($this->lockTimeout);

        // Commands in error
        if (count($failedCommands) > 0) {
            $message = "";

            foreach ($failedCommands as $command) {
                $message .= sprintf(
                    "%s: returncode %s, locked: %s, last execution: %s\n",
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

        return 0;
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
        $subject = $this->getMailSubject();
        $headers = 'From: cron-monitor@'.$hostname."\r\n".
            'X-Mailer: PHP/'.phpversion();

        foreach ($this->receiver as $rcv) {
            mail(trim($rcv), $subject, $message, $headers);
        }
    }

    /**
     * get the subject for monitor mails
     *
     * @return string subject
     */
    private function getMailSubject()
    {
        $hostname = gethostname();

        return sprintf($this->mailSubject, $hostname, date('Y-m-d H:i:s'));
    }

}
