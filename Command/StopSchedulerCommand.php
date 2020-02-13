<?php
namespace JMose\CommandSchedulerBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Code originally taken from https://github.com/Cron/Symfony-Bundle/blob/2.1.0/Command/CronStopCommand.php
 * License: MIT (according to https://github.com/Cron/Symfony-Bundle/blob/2.1.0/LICENSE)
 * Original author: Alexander Lokhman <alex.lokhman@gmail.com>
 *
 * Adaption to CommandSchedulerBundle by Christoph Singer <singer@webagentur72.de>
 *
 */
class StopSchedulerCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('scheduler:stop')
            ->setDescription('Stops command scheduler');
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pidFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.StartSchedulerCommand::PID_FILE;
        if (!file_exists($pidFile)) {
            return 0;
        }
        if (!extension_loaded('pcntl')) {
            throw new \RuntimeException('This command needs the pcntl extension to run.');
        }
        if (!posix_kill(file_get_contents($pidFile), SIGINT)) {
            if (!unlink($pidFile)) {
                throw new \RuntimeException('Unable to stop scheduler.');
            }
            $output->writeln(sprintf('<comment>%s</comment>', 'Unable to kill command scheduler process. Scheduler will be stopped before the next run.'));
            return 0;
        }
        unlink($pidFile);
        $output->writeln(sprintf('<info>%s</info>', 'Command scheduler is stopped.'));

        return 0;
    }
}