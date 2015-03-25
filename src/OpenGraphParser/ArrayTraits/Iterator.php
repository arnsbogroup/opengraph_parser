<?php
namespace OpenGraphParser\ArrayTraits;

trait Iterator {

    public function rewind() {
        reset($this->elements);
    }

    public function current() {
        $var = current($this->elements);
        return $var;
    }

    public function key() {
        $var = key($this->elements);
        return $var;
    }

    public function next() {
        $var = next($this->elements);
        return $var;
    }

    public function valid() {
        $key = key($this->elements);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
}
