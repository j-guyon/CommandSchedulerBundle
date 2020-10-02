<?php

namespace JMose\CommandSchedulerBundle\Tests\Constraints;

use JMose\CommandSchedulerBundle\Validator\Constraints\CronExpression;
use JMose\CommandSchedulerBundle\Validator\Constraints\CronExpressionValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * Class CronExpressionValidatorTest.
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
        $this->validator->validate($value, new CronExpression(['message' => '']));

        $this->assertNoViolation();
    }

    public function getValidValues()
    {
        return [
            ['* * * * *'],
            ['@daily'],
            ['@yearly'],
            ['*/10 * * * *'],
        ];
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testInvalidValues($value)
    {
        $constraint = new CronExpression(
            [
                'message' => 'myMessage',
            ]
        );

        $this->validator->validate($value, $constraint);

        $this->buildViolation('myMessage')
            ->assertRaised();
    }

    public function getInvalidValues()
    {
        return [
            ['*/10 * * *'],
            ['*/5 * * * ?'],
            ['sometimes'],
            ['never'],
            ['*****'],
            ['* * * * * * *'],
            ['* * * * * *'],
        ];
    }
}
