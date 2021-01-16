<?php


namespace JMose\CommandSchedulerBundle\Tests\App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    const OUTPUT_LOG_FILE = __DIR__ . '/../logs/TEST_COMMAND_OUTPUT_LOG_FILE.txt';
    const LAST_EXECUTION_TIME = 'last_execution_time';
    const LOG_FILE = 'log_file';
    const LAST_RETURN_CODE = 'last_return_code';

    private static $index = 0;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('scheduler:execute:test')
            ->setDescription('Fake command')
            ->addArgument(self::LAST_EXECUTION_TIME, null, InputArgument::REQUIRED, 'Last execution time params')
            ->addArgument(self::LOG_FILE, null, InputArgument::OPTIONAL, 'Log file')
            ->addOption(self::LAST_RETURN_CODE, null, InputOption::VALUE_REQUIRED, 'Last return Code');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $content = array();
        if (file_exists(self::OUTPUT_LOG_FILE)) {
            $content = json_decode(file_get_contents(self::OUTPUT_LOG_FILE), true);
        }
        $content[self::$index] = array(
            self::LAST_EXECUTION_TIME => $input->getArgument(self::LAST_EXECUTION_TIME),
            self::LOG_FILE => $input->getArgument(self::LOG_FILE),
            self::LAST_RETURN_CODE => (int) $input->getOption(self::LAST_RETURN_CODE)
        );
        file_put_contents(self::OUTPUT_LOG_FILE, json_encode($content));
        self::$index++;
        return 0;
    }
}