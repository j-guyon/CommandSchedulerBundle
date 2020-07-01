<?php

namespace JMose\CommandSchedulerBundle\Validator\Constraints;

use Cron\CronExpression as CronExpressionLib;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CronExpressionValidator.
 */
class CronExpressionValidator extends ConstraintValidator
{
    /**
     * Validate method for CronExpression constraint.
     *
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $value = (string) $value;

        if ('' === $value) {
            return;
        }

        try {
            CronExpressionLib::factory($value);
        } catch (\InvalidArgumentException $e) {
            // This condition is required in order to respect BC with "mtdowling/cron-expression".
            // It must be removed at 3.0 release.
            // @see https://github.com/mtdowling/cron-expression/commit/56e89730e60a0e945bf4ea10c48b80a406c7e7a0.
            if ('6 is not a valid position' === $e->getMessage()) {
                @trigger_error($e->getMessage().' and its support is deprecated since jmose/command-scheduler-bundle 2.x.', E_USER_DEPRECATED);

                return;
            }

            $this->context->addViolation($constraint->message, [], $value);
        }
    }
}
