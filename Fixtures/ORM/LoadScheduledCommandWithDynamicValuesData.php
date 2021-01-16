<?php

namespace JMose\CommandSchedulerBundle\Fixtures\ORM;

use DateTime;
use Doctrine\Persistence\ObjectManager;
use JMose\CommandSchedulerBundle\Tests\App\Command\TestCommand;

/**
 * Class LoadScheduledCommandData.
 *
 * @author  Julien Guyon <julienguyon@hotmail.com>
 */
class LoadScheduledCommandWithDynamicValuesData extends AbstractScheduledCommandData
{
    const LAST_EXECUTION_DATE = '2021-01-02 08:01:02';
    const LAST_RETURN_CODE_0 = 0;
    const LAST_RETURN_CODE_NEGATIVE_1 = -1;
    const LOG_FILE = 'LoadScheduledCommandWithDynamicValuesData_log_file.log';

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createScheduledCommand(
            __CLASS__ . '_one',
            'scheduler:execute:test',
            '%last_execution% %log_file% --' . TestCommand::LAST_RETURN_CODE . '=%last_return_code%',
            '@daily',
            self::LOG_FILE,
            40,
            new DateTime(self::LAST_EXECUTION_DATE),
            false,
            false,
            true,
            self::LAST_RETURN_CODE_0
        );
        $this->createScheduledCommand(
            __CLASS__ . '_one',
            'scheduler:execute:test',
            '%last_execution% %log_file% --' . TestCommand::LAST_RETURN_CODE . '=%last_return_code%',
            '@daily',
            self::LOG_FILE,
            40,
            new DateTime(self::LAST_EXECUTION_DATE),
            false,
            false,
            true,
            self::LAST_RETURN_CODE_NEGATIVE_1
        );

    }

    /**
     * @inheritDoc
     */
    protected function getManager(): ObjectManager
    {
        return $this->manager;
    }
}
