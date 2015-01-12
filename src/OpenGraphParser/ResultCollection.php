<?php
namespace OpenGraphParser;
class ResultCollection implements \ArrayAccess, \Countable {
    protected $elements;

    public function __construct() {
    
    }

    public function add($elm) {
        $this->elements[] = $elm;
    }


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

    public function count() {
        return count($this->elements);
    }

    public function filter($filterFunction) {
        $newCollection = new ResultCollection();
        foreach($this->elements as $element) {
            if($filterFunction($element)) {
                $newCollection->add($element);
            }
        }

        return $newCollection;
    }

    public function format($formatFunction) {
        foreach($this->elements as $element) {
            $element->format($formatFunction);
        }
    }

}

