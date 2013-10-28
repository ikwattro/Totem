<?php

namespace Taluu\Domain\ChangeSet;

use \Countable,
    \ArrayAccess,
    \ArrayIterator,
    \IteratorAggregate,

    \OutOfBoundsException,
    \InvalidArgumentException;

/**
 * Represents a changeset
 *
 * @todo allow more changesets on some other things than just ApiAble objects ?
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Set implements ChangeInterface, ArrayAccess, IteratorAggregate, Countable
{
    private $changes = null;

    public function __construct(array $old, array $new)
    {
        $this->compute($old, $new);
    }

    /**
     * Retrieve a property change
     *
     * @param  string $property
     *
     * @return mixed Set if it was a recursive change, Change otherwise
     */
    public function getChange($property)
    {
        if (!$this->hasChanged($property)) {
            throw new OutOfBoundsException('This property doesn\'t exist or wasn\'t changed');
        }

        return $this->changes[$property];
    }

    /**
     * Test if the given property has been changed
     *
     * @param  string  $property
     *
     * @return boolean
     */
    public function hasChanged($property)
    {
        return isset($this->changes[$property]);
    }

    /** {@inheritDoc} */
    public function toArray($short = true)
    {
        if (true === $short) {
            return array_keys($this->changes);
        }

        return array_map(function (ChangeInterface $change) { return $change->toArray(false); }, $this->changes);
    }

    /** {@inheritDoc} */
    public function getIterator()
    {
        return new ArrayIterator($this->changes);
    }

    /** {@inheritDoc} */
    public function offsetExists($offset)
    {
        return $this->hasChanged($offset);
    }

    /** {@inheritDoc} */
    public function offsetGet($offset)
    {
        return $this->getChange($offset);
    }

    /** {@inheritDoc} */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException('You cannot alter a changeset once it has been calculated');
    }

    /** {@inheritDoc} */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('You cannot alter a changeset once it has been calculated');
    }

    /** {@inheritDoc} */
    public function count()
    {
        return count($this->changes);
    }

    /**
     * Calculate the changeset between two arrays
     *
     * Both arrays must have the same keys and the same size
     *
     * @param array $old Old array
     * @param array $new New array
     *
     * @internal for intern use only
     * @return array the changes brought to the object
     */
    private function compute(array $old, array $new)
    {
        if (array_keys($old) !== array_keys($new)) {
            throw new \InvalidArgumentException('You should compare two arrays with the same keys !');
        }

        $this->changes = [];

        foreach (array_keys($new) as $key) {
            // -- if it is not the same type, then we may consider it changed
            if (gettype($old[$key]) !== gettype($new[$key])) {
                $this->changes[$key] = new Change($old[$key], $new[$key]);
                continue;
            }

            // -- if it is an object (and so is new[key]), just compare the two hashes
            if (is_object($old[$key])) {
                if (spl_object_hash($old[$key]) !== spl_object_hash($new[$key])) {
                    $this->changes[$key] = new Change($old[$key], $new[$key]);
                }

                continue;
            }

            // -- if it is an array several step to check up
            if (is_array($old[$key])) {
                // -- not the same size ? Then it changed, completely
                if (array_keys($old[$key]) !== array_keys($new[$key])) {
                    $this->changes[$key] = new Change($old[$key], $new[$key]);
                    continue;
                }

                // -- same size / same keys ; return only what has changed
                $set = new self($old[$key], $new[$key]);

                if (0 !== count($set)) {
                    $this->changes[$key] = $set;
                }

                continue;
            }

            // -- eventually, check if the two elements are equal
            if ($old[$key] !== $new[$key]) {
                $this->changes[$key] = new Change($old[$key], $new[$key]);
            }
        }

        return $this->changes;
    }

}
