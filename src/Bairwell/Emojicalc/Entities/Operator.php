<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc\Entities;

use Bairwell\Emojicalc\Exceptions\UnrecognisedOperator;

/**
 * Class Operator
 *
 * Holds details of which operator this is (along with validation).
 *
 * @package Bairwell\Emojicalc
 */
class Operator
{
    /**
     * List of allowed operators.
     *
     * @var array
     */
    private static $allowedOperators = [
        '+' => 'addition',
        '-' => 'subtraction',
        '*' => 'multiply',
        '/' => 'division'
    ];
    /**
     * The type of operator this is.
     *
     * @var string
     */
    private $operatorType;
    /**
     * The unicode (or similar) symbol to use for rendering.
     *
     * @var Symbol
     */
    private $symbol;

    /**
     * Operator constructor.
     * @param string $operatorType The operator (+/-/* etc) this is.
     * @param Symbol $symbol The symbol this operator relates to.
     *
     * @throws \InvalidArgumentException Thrown if unrecognised operator.
     */
    public function __construct(string $operatorType, Symbol $symbol)
    {
        if (false === $this->validateOperator($operatorType)) {
            throw new \InvalidArgumentException('Unrecognised operator');
        }
        $this->operatorType = $operatorType;
        $this->symbol = $symbol;
    }

    /**
     * Validate that the operator is one of the recognised ones.
     *
     * @param string $operator The operator we are checking.
     * @return bool False if not allowed, true if is allowed.
     */
    protected function validateOperator(string $operator): bool
    {
        return array_key_exists($operator, self::$allowedOperators);
    }

    /**
     * Get the associated symbol.
     *
     * @return Symbol
     */
    public function getSymbol(): Symbol
    {
        return $this->symbol;
    }

    /**
     * Get the operator type.
     *
     * @return string
     */
    public function getOperatorType(): string
    {
        return $this->operatorType;
    }

    /**
     * Get the name of this operator.
     *
     * @return string
     */
    public function getOperatorName(): string
    {
        return self::$allowedOperators[$this->operatorType];
    }

    /**
     * Actually perform the calculation.
     * @param float $first The first number.
     * @param float $second The second number.
     * @return float Return value.
     */
    public function performCalculation(float $first, float $second): float
    {
        switch ($this->operatorType) {
            case '+':
                return $first + $second;
                break;
            case '-':
                return $first - $second;
                break;
            case '/':
                return $first / $second;
                break;
            case '*':
                return $first * $second;
                break;
            default:
                // If not recognised, throw.
                throw new UnrecognisedOperator('During calculation');
        }
    }
}