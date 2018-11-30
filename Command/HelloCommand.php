<?php

namespace JMose\CommandSchedulerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HelloCommand : Say Hello World (can be used for testing purposes
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 * @package JMose\CommandSchedulerBundle\Command
 */
class HelloCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('schedulerTest:hello')
            ->setDescription('Hello world testcommand')
            ->addOption(
                'name',
                null,
                InputOption::VALUE_OPTIONAL,
                'Person who shall be greeted',
                'World'
            )
            ->addOption(
                'trash',
                null,
                InputOption::VALUE_OPTIONAL,
                'can be set to anything, not used',
                false
            )
            ->addOption(
                'randReturn',
                null,
                InputOption::VALUE_OPTIONAL,
                'set to enable random return value',
                false
            )
            ->addOption(
                'randSleep',
                null,
                InputOption::VALUE_OPTIONAL,
                'set to enable random sleep to increase runtime',
                false
            )
            ->setHelp('This class is used for testing purposes only');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption('name');
        $output->writeln('<info>Hello ' . $name . '</info>');

        if ($input->getOption('randSleep')) {
            sleep(rand(1, 10));
        }

        $return = 0;
        if ($input->getOption('randReturn')) {
            $return = rand(0, 5);
        }

        return $return;
    }
}
