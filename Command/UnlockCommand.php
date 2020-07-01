<?php

namespace JMose\CommandSchedulerBundle\Command;

use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to unlock one or all scheduled commands that have surpassed the lock timeout.
 *
 * @author  Marcel Pfeiffer <m.pfeiffer@strucnamics.de>
 */
class UnlockCommand extends Command
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var int
     */
    private $defaultLockTimeout;

    /**
     * @var int|bool Number of seconds after a command is considered as timeout
     */
    private $lockTimeout;

    /**
     * @var bool true if all locked commands should be unlocked
     */
    private $unlockAll;

    /**
     * @var string name of the command to be unlocked
     */
    private $scheduledCommandName = [];

    /**
     * UnlockCommand constructor.
     *
     * @param ManagerRegistry $managerRegistry
     * @param $managerName
     * @param $lockTimeout
     */
    public function __construct(ManagerRegistry $managerRegistry, $managerName, $lockTimeout)
    {
        $this->em = $managerRegistry->getManager($managerName);
        $this->defaultLockTimeout = $lockTimeout;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('scheduler:unlock')
            ->setDescription('Unlock one or all scheduled commands that have surpassed the lock timeout.')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of the command to unlock')
            ->addOption('all', 'A', InputOption::VALUE_NONE, 'Unlock all scheduled commands')
            ->addOption(
                'lock-timeout',
                null,
                InputOption::VALUE_REQUIRED,
                'Use this lock timeout value instead of the configured one (in seconds, optional)'
            );
    }

    /**
     * Initialize parameters and services used in execute function.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->unlockAll = $input->getOption('all');
        $this->scheduledCommandName = $input->getArgument('name');

        $this->lockTimeout = $input->getOption('lock-timeout', null);
        if (null === $this->lockTimeout) {
            $this->lockTimeout = $this->defaultLockTimeout;
        } else {
            if ('false' === $this->lockTimeout) {
                $this->lockTimeout = false;
            }
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (false === $this->unlockAll && null === $this->scheduledCommandName) {
            $output->writeln('Either the name of a scheduled command or the --all option must be set.');

            return 1;
        }

        $repository = $this->em->getRepository(ScheduledCommand::class);

        if (true === $this->unlockAll) {
            $failedCommands = $repository->findLockedCommand();
            foreach ($failedCommands as $failedCommand) {
                $this->unlock($failedCommand, $output);
            }
        } else {
            $scheduledCommand = $repository->findOneBy(['name' => $this->scheduledCommandName, 'disabled' => false]);
            if (null === $scheduledCommand) {
                $output->writeln(
                    sprintf(
                        'Error: Scheduled Command with name "%s" not found or is disabled.',
                        $this->scheduledCommandName
                    )
                );

                return 1;
            }
            $this->unlock($scheduledCommand, $output);
        }

        $this->em->flush();

        return 0;
    }

    /**
     * @param ScheduledCommand $command command to be unlocked
     *
     * @return bool true if unlock happened
     */
    protected function unlock(ScheduledCommand $command, OutputInterface $output)
    {
        if (false === $command->isLocked()) {
            $output->writeln(sprintf('Skipping: Scheduled Command "%s" is not locked.', $command->getName()));

            return false;
        }

        if (false !== $this->lockTimeout &&
            null !== $command->getLastExecution() &&
            $command->getLastExecution() >= (new \DateTime())->sub(
                new \DateInterval(sprintf('PT%dS', $this->lockTimeout))
            )
        ) {
            $output->writeln(
                sprintf('Skipping: Timout for scheduled Command "%s" has not run out.', $command->getName())
            );

            return false;
        }
        $command->setLocked(false);
        $output->writeln(sprintf('Scheduled Command "%s" has been unlocked.', $command->getName()));

        return true;
    }
}
