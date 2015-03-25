<?php
namespace OpenGraphParser\ArrayTraits;

trait Countable {
    public function count() {
        return count($this->elements);
    }
}
