<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc\Entities;

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
     * List of allowed operators.
     *
     * @var array
     */
    private $allowedOperators=[
        '+'=>'addition','-'=>'subtraction','*'=>'multiply','/'=>'division'
    ];

    /**
     * Operator constructor.
     * @param string $operatorType The operator (+/-/* etc) this is.
     * @param Symbol $symbol The symbol this operator relates to.
     *
     * @throws \Exception Thrown if unrecognised operator.
     */
    public function __construct(string $operatorType,Symbol $symbol) {
        if (false===$this->validateOperator($operatorType)) {
            throw new \Exception('Unrecognised operator');
        }
        $this->operatorType=$operatorType;
        $this->symbol=$symbol;
    }

    /**
     * Validate that the operator is one of the recognised ones.
     *
     * @param string $operator The operator we are checking.
     * @return bool False if not allowed, true if is allowed.
     */
    protected function validateOperator(string $operator) : bool  {
        return array_key_exists($operator,$this->allowedOperators);
    }

    /**
     * Get the associated symbol.
     *
     * @return Symbol
     */
    public function getSymbol() : Symbol {
        return $this->symbol;
    }

    /**
     * Get the operator type.
     *
     * @return string
     */
    public function getOperatorType() : string {
        return $this->operatorType;
    }
}