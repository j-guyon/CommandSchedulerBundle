<?php

namespace JMose\CommandSchedulerBundle\Tests\Constraints;

use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use JMose\CommandSchedulerBundle\Validator\Constraints\CronExpression;
use JMose\CommandSchedulerBundle\Validator\Constraints\CronExpressionValidator;

/**
 * Class CronExpressionValidatorTest
 * @package JMose\CommandSchedulerBundle\Tests\Constraints
 */
class CronExpressionValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new CronExpressionValidator();
    }

    /**
     * @dataProvider getValidValues
     */
    public function testValidValues($value)
    {
        $this->validator->validate($value, new CronExpression(array('message' => '')));

        $this->assertNoViolation();
    }

    public function getValidValues()
    {
        return array(
            array('* * * * *'),
            array('@daily'),
            array('@yearly'),
            array('*/10 * * * *'),
            array('* * * * * *'), // Remove this value from valid options at 3.0 release.
        );
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testInvalidValues($value)
    {
        $constraint = new CronExpression(
            array(
                'message' => 'myMessage',
            )
        );

        $this->validator->validate($value, $constraint);

        $this->buildViolation('myMessage')
            ->assertRaised();
    }

    public function getInvalidValues()
    {
        return array(
            array('*/10 * * *'),
            array('*/5 * * * ?'),
            array('sometimes'),
            array('never'),
            array('*****'),
            // Uncomment following values at 3.0 release.
            // array('* * * * * * *'),
            // array('* * * * * *'),
        );
    }
}
