<?php

namespace JMose\CommandSchedulerBundle\Command;

use JMose\CommandSchedulerBundle\Domain\MonitorMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MonitorCommand : This class is used for monitoring scheduled commands if they run for too long or failed to execute
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 * @package JMose\CommandSchedulerBundle\Command
 */
class MonitorCommand extends ContainerAwareCommand
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
     * @var boolean if true, current command will send mail even if all is ok.
     */
    private $sendMailIfNoError;

    /**
     * @var $mailer \Swift_Mailer
     */
    private $mailer;

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
     * Initialize parameters and services used in execute function
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->lockTimeout = $this->getContainer()->getParameter('jmose_command_scheduler.lock_timeout');
        $this->dumpMode = $input->getOption('dump');
        $this->receiver = $this->getContainer()->getParameter('jmose_command_scheduler.monitor_mail');
        $this->sendMailIfNoError = $this->getContainer()->getParameter('jmose_command_scheduler.send_ok');

        $this->em = $this->getContainer()->get('doctrine')->getManager(
            $this->getContainer()->getParameter('jmose_command_scheduler.doctrine_manager')
        );
        $this->mailer = $this->getContainer()->get('mailer');
    }

    /**
     * @param InputInterface $input
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

        // Fist, get all failed or potential timeout
        $failedCommands = $this->em->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')
            ->findFailedAndTimeoutCommands($this->lockTimeout);

        // Commands in error
        if (count($failedCommands) > 0) {
            $messages = array();

            foreach ($failedCommands as $command) {
                $message = new MonitorMessage($command->getName(), $command->getLastReturnCode(), $command->getLocked(), $command->getLastExecution());
                array_push($messages, $message);
            }

            // if --dump option, don't send mail
            if ($this->dumpMode) {
                foreach ($messages as $message){
                    $output->writeln($message->__toString());
                }
            } else {
                $this->sendErrorMails($messages);
            }

        } else {
            if ($this->dumpMode) {
                $output->writeln('No errors found.');
            } elseif ($this->sendMailIfNoError) {
                $this->sendNoErrorMails();
            }
        }
    }

    /**
     * Send message to email receivers
     *
     * @param $messages MonitorMessage[] An array containing one or more MonitorMessage objects
     */
    public function sendErrorMails($messages)
    {
        // override at app/Resources/JMoseCommandSchedulerBundle/views/Emails/error.html.twig
        $body = $this->getContainer()->get('templating')->render('JMoseCommandSchedulerBundle:Emails:error.html.twig', array('messages' => $messages));
        $this->sendMessage($body);
    }

    public function sendNoErrorMails()
    {
        // override at app/Resources/JMoseCommandSchedulerBundle/views/Emails/noerror.html.twig
        $body = $this->getContainer()->get('templating')->render('JMoseCommandSchedulerBundle:Emails:noerror.html.twig');
        $this->sendMessage($body);
    }

    private function sendMessage($body)
    {
        // prepare email constants
        $hostname = gethostname();
        $subject = "cronjob monitoring " . $hostname . ", " . date('Y-m-d H:i:s');
        $message = \Swift_Message::newInstance()->setSubject($subject)->setFrom('cron-monitor@' . $hostname)->setTo($this->receiver)->setBody($body, 'text/html');
        $this->getMailer()->send($message);
    }

    /**
     * @return \Swift_Mailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @param \Swift_Mailer $mailer
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }
}
