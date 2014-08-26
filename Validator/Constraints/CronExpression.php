<?php

namespace JMose\CommandSchedulerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CronExpression
 * @Annotation
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