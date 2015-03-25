<?php
namespace OpenGraphParser\ArrayTraits;

trait ArrayAccess {
    public function offsetExists ($offset ) {
        return isset($this->elements[$offset]);
    }

    public function offsetGet ( $offset ) {
        return $this->offsetExists($offset) ? $this->elements[$offset] : null;
    }

    public function offsetSet ( $offset , $value ) {
    if (is_null($offset)) {
            $this->elements[] = $value;
        } else {
            $this->elements[$offset] = $value;
        }
    }

    public function offsetUnset ( $offset ) {
        unset($this->elements[$offset]);
    }
}
