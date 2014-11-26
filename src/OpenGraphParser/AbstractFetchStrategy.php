<?php
namespace OpenGraphParser;
abstract class AbstractFetchStrategy {
    public function get($uri) {
        return $this->get_uri($uri);
    }

    abstract protected function get_uri($uri);
}
