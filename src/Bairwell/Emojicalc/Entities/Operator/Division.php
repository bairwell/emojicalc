<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc\Entities\Operator;

use Bairwell\Emojicalc\Entities\Operator;

/**
 * Division operator.
 * @package Bairwell\Emojicalc\Entities\Operator
 */
class Division extends Operator
{
    /**
     * Get the operator type.
     *
     * @return string
     */
    public function getOperatorType(): string
    {
        return '/';
    }

    /**
     * Get the name of this operator.
     *
     * @return string
     */
    public function getOperatorName(): string
    {
        return 'division';
    }

    /**
     * Actually perform the calculation.
     * @param float $first The first number.
     * @param float $second The second number.
     * @return float Return value.
     * @throws \DivisionByZeroError If an attempt to divide by zero if made
     */
    public function performCalculation(float $first, float $second): float
    {
        if ((float)0 === $second) {
            throw new \DivisionByZeroError('Cannot divide by zero');
        }
        return $first / $second;
    }


}