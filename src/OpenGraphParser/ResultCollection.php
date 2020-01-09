<?php
namespace OpenGraphParser;
class ResultCollection implements \ArrayAccess, \Countable, \Iterator{
    use ArrayTraits\ArrayAccess;
    use ArrayTraits\Countable;
    use ArrayTraits\Iterator;

    protected $elements = [];
    public function add($elm) {
        $this->elements[] = $elm;
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
