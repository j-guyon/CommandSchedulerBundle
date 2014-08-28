<?php

namespace JMose\CommandSchedulerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CronExpression
 *
 * @package Assureclair\OGM\SinistreBundle\Validator\Constraints
 */
class CronExpression extends Constraint
{

    /**
     * Constraint error message
     *
     * @var string
     */
    public $message;

}
