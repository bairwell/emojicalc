<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc\Entities;

use Bairwell\Emojicalc\Exceptions\UnrecognisedOperator;

/**
 * Class Operators.
 *
 * A simple collection of operators. Most of the code is taken from
 * http://php.net/manual/en/class.iterator.php
 *
 * @package Bairwell\Emojicalc\Entities
 */
class Operators implements \Iterator
{
    /**
     * Our list of operators.
     * @var array
     */
    private $operators = [];

    /**
     * Current iterator position.
     * @var int
     */
    private $position;

    /**
     * Operators constructor.
     */
    public function __construct()
    {
        $this->position = 0;
    }

    /**
     * Add a single operator.
     *
     * Currently allows multiple operators with the same operator and/or same symbol.
     *
     * @param Operator $operator Operator to add.
     * @return $this Fluent interface.
     * @throws \InvalidArgumentException If operator is duplicated.
     */
    public function addOperator(Operator $operator)
    {
        if (true === in_array($operator, $this->operators, true)) {
            throw new \InvalidArgumentException('Duplicated operator');
        }
        $this->operators[] = $operator;
        return $this;
    }

    /**
     * Find an operator by type (i.e. the +/- symbols)
     * @param string $type The operator type to match.
     * @return Operator The matched operator.
     * @throws UnrecognisedOperator If not found.
     */
    public function findOperatorByType(string $type): Operator
    {
        /* @var Operator $currentOperator */
        foreach ($this->operators as $currentOperator) {
            if ($currentOperator->getOperatorType() === $type) {
                return $currentOperator;
            }
        }
        throw new UnrecognisedOperator($type);
    }

    /**
     * Find an operator by symbol code (i.e. the \u234)
     * @param string $symbolCode The symbol code to match.
     * @return Operator The matched operator.
     * @throws UnrecognisedOperator If not found.
     */
    public function findOperatorBySymbol(string $symbolCode): Operator
    {
        /* @var Operator $currentOperator */
        foreach ($this->operators as $currentOperator) {
            if ($currentOperator->getSymbol()->getSymbolCode() === $symbolCode) {
                return $currentOperator;
            }
        }
        throw new UnrecognisedOperator($symbolCode);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return Operator
     * @since 5.0.0
     */
    public function current(): Operator
    {
        return $this->operators[$this->position];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return isset($this->operators[$this->position]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->position = 0;
    }

}