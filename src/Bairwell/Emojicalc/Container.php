<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc;

/**
 * Simple PSR-11 inspired container.
 * @package Bairwell\Emojicalc
 */
class Container implements ContainerInterface
{

    /**
     * The actual store.
     * @var array
     */
    protected $store = [];

    /**
     * Container constructor.
     */
    public function __construct()
    {
        $this->store = [];
    }

    /**
     * Get an item from the container store.
     * @param string $id
     * @return mixed
     * @throws \Exception
     */
    public function get(string $id)
    {
        if (false === $this->has($id)) {
            throw new \RuntimeException('No entry was found for "' . $id . '" identifier.');
        }
        return $this->store[$id];
    }

    /**
     * Has this container got an item?
     * @param string $id Id of the item.
     * @return bool
     */
    public function has(string $id)
    {
        return array_key_exists($id, $this->store);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     * @throws \RuntimeException If used.
     */
    public function offsetExists($offset)
    {
        throw new \RuntimeException('offsetExists on containers cannot be used directly. Use "has".');
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     * @since 5.0.0
     * @throws \RuntimeException If used.
     */
    public function offsetGet($offset)
    {
        throw new \RuntimeException('offsetGet on containers cannot be used directly. Use "get".');
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->store[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->store[$offset]);
    }

}