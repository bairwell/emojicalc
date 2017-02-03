<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc\Entities;

use Bairwell\Emojicalc\Entities\Symbol;

/**
 * Base class for operators to extend.
 *
 * @package Bairwell\Emojicalc\Entities
 */
abstract class Operator {

    /**
     * Private as the child classes don't need to know about it.
     * @var Symbol
     */
    private $symbol;

    /**
     * Operator constructor.
     *
     * Final as there is no reason for child classes to know about symbols.
     *
     * @param Symbol $symbol
     */
    final public function __construct(Symbol $symbol) {
        $this->symbol=$symbol;
    }
    /**
     * Get the associated symbol.

     * Final as there is no reason for child classes to know about symbols.
     *
     * @return Symbol
     */
    final public function getSymbol(): Symbol
    {
        return $this->symbol;
    }

    /**
     * Get the operator type.
     *
     * @return string
     */
    abstract public function getOperatorType(): string;
    /**
     * Get the name of this operator.
     *
     * @return string
     */
    abstract public function getOperatorName(): string;

    /**
     * Actually perform the calculation.
     * @param float $first The first number.
     * @param float $second The second number.
     * @return float Return value.
     */
    abstract public function performCalculation(float $first, float $second): float;
}