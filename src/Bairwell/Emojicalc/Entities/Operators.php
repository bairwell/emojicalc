<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc\Entities;

/**
 * Class Operators.
 *
 * A simple collection of operators. Most of the code is taken from
 * http://php.net/manual/en/class.iterator.php
 *
 * @package Bairwell\Emojicalc\Entities
 */
class Operators implements \Iterator {
    /**
     * Our list of operators.
     * @var array
     */
    private $operators=[];

    /**
     * Current iterator position.
     * @var int
     */
    private $position;

    /**
     * Operators constructor.
     */
    public function __construct() {
        $this->position=0;
    }

    /**
     * Add a single operator.
     *
     * @param Operator $operator Operator to add.
     * @return $this Fluent interface.
     */
    public function addOperator(Operator $operator) {
        $this->operators[]=$operator;
        return $this;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return Operator
     * @since 5.0.0
     */
    public function current()
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
    public function key()
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
    public function valid()
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