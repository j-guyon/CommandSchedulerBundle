<?php
/**
 * Created by PhpStorm.
 * User: carlo
 * Date: 6/4/2018
 * Time: 7:54 AM
 */

namespace JMose\CommandSchedulerBundle\Exception;


class CommandNotFoundException extends \ErrorException
{
    public function __construct($commandName)
    {
        parent::__construct(sprintf('Command %s does not exist, or it is not a valid command name.', $commandName));
    }
}