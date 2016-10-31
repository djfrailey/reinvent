<?php
declare (strict_types=1);

namespace Reinvent\Support;

use \InvalidArgumentException;
use \ArrayObject;
use \ArrayAccess;
use \Iterator;

class Collection implements ArrayAccess, Iterator
{
    protected $data = [];

    public function __construct($arrayable = [])
    {
        $this->data = $this->getArrayableValues($arrayable);
    }

    public function changeKeyCase($case = CASE_LOWER) : Collection
    {
        array_change_key_case($this->data, $case);
        return $this;
    }

    public function column($column) : Collection
    {
        return new static(array_column($this->data, $column));
    }

    public function countValues() : Collection
    {
        return new static(array_count_values($this->data));
    }

    public function diffAssoc(...$args) : Collection
    {
        $values = $this->getArrayableValuesFromArgs($args);
        return new static(array_diff_assoc($this->data, ...$values));
    }

    public function diffKey(...$args) : Collection
    {
        $values = $this->getArrayableValuesFromArgs($args);
        return new static(array_diff_key($this->data, ...$values));
    }

    public function diffUAssoc($func, ...$args) : Collection
    {
        $values = $this->getArrayableValuesFromArgs($args);
        $values[] = $func;
        return new static(call_user_func_array('array_diff_uassoc', $values));
    }

    public function diffUKey($func, ...$args) : Collection
    {
        $values = $this->getArrayableValuesFromArgs($args);
        $values[] = $func;
        return new static(call_user_func_array('array_diff_ukey', $values));
    }

    public function diff(...$args) : Collection
    {
        $values = $this->getArrayableValuesFromArgs($args);
        return new static(array_diff($this->data, ...$values));
    }

    public function filter($callback, $flag = 0) : Collection
    {
        return new static(array_filter($this->data, $callback, $flag));
    }

    public function intersectAssoc(...$args) : Collection
    {
        $values = $this->getArrayableValuesFromArgs($args);
        return new static(array_intersect_assoc($this->data, ...$values));
    }

    public function intersectKey(...$args) : Collection
    {
        $values = $this->getArrayableValuesFromArgs($args);
        return new static(array_intersect_key($this->data, ...$values));
    }

    public function intersectUAssoc($func, ...$args) : Collection
    {
        $values = $this->getArrayableValuesFromArgs($args);
        $values[] = $func;
        return new static(call_user_func_array('array_intersect_uassoc', $values));
    }

    public function intersectUKey($func, ...$args) : Collection
    {
        $values = $this->getArrayableValuesFromArgs($args);
        $values[] = $func;
        return new static(call_user_func_array('array_intersect_ukey', $values));
    }

    public function intersect(...$args) : Collection
    {
        $values = $this->getArrayableValuesFromArgs($args);
        return new static(array_diff($this->data, ...$values));
    }

    public function map($func) : Collection
    {
        return new static(array_map($func, $this->data));
    }

    public function merge(...$args) : Collection
    {
        $values = $this->getArrayableValuesFromArgs($args);
        return new static(array_merge($this->data, ...$values));
    }

    public function pop()
    {
        return array_pop($this->data);
    }

    public function push(...$values) : Collection
    {
        array_push($this->data, ...$values);
        return $this;
    }

    public function reduce($func, $initial = null)
    {
        return array_reduce($this->data, $func, $initial);
    }

    public function each(callable $func)
    {
        foreach($this->data as $key => $data) {
            call_user_func($func, $key, $data, $this->data);
        }
    }

    public function getGenerator()
    {
        yield from $this->data;
    }

    /**
     * Returns the current value pointed to by the $data array
     * @return mixed
     */
    public function current()
    {
        return current($this->data);
    }
    
    /**
     * Returns the current key pointed to by the $data array
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    public function keys() : Collection
    {
        return new static(array_keys($this->data));
    }
    
    /**
     * Moves the internal pointer in $data one step and then returns the value.
     * @return mixed
     */
    public function next()
    {
        return next($this->data);
    }
    
    /**
     * Resets the internal pointer in $data to the start.
     * @return mixed
     */
    public function rewind()
    {
        reset($this->data);
    }
    
    /**
     * Ensures the position that the internal $data pointer is at exists
     * @return mixed
     */
    public function valid()
    {
        return $this->offsetExists($this->key());
    }

    public function offsetExists($offset) : Bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        $value = null;

        if ($this->offsetExists($offset)) {
            $value = $this->data[$offset];
        }

        return $value;
    }

    public function offsetSet($offset, $value) {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    public static function combine($keys, $values) : Collection
    {
        $keys = $this->getArrayableValues($keys);
        $values = $this->getArrayableValues($values);
        return new static(array_combine($keys, $values));
    }

    protected function getArrayableValues($arrayable) : array
    {
        $values = [];

        if (is_array($arrayable)) {
            $values = $arrayable;
        } else if ($arrayable instanceof Collection) {
            $values = $arrayable->toArray();
        } else if ($arrayable instanceof ArrayObject) {
            $values = $arrayable->getArrayCopy();
        } else {
            throw new InvalidArgumentException("Write me a message.");
        }

        return $values;
    }

    protected function getArrayableValuesFromArgs(...$args) : array
    {
        $values = [];

        foreach($args as $arg) {
            $values[] = $this->getArrayableValues($arg);
        }

        return $values;
    }
}
