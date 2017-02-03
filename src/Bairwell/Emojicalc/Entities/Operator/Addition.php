<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc\Entities\Operator;

use Bairwell\Emojicalc\Entities\Operator;

/**
 * Addition operator.
 * @package Bairwell\Emojicalc\Entities\Operator
 */
class Addition extends Operator {
    /**
     * Get the operator type.
     *
     * @return string
     */
    public function getOperatorType(): string
    {
        return '+';
    }

    /**
     * Get the name of this operator.
     *
     * @return string
     */
    public function getOperatorName(): string
    {
        return 'addition';
    }

    /**
     * Actually perform the calculation.
     * @param float $first The first number.
     * @param float $second The second number.
     * @return float Return value.
     */
    public function performCalculation(float $first, float $second): float
    {
        return $first+$second;
    }


}