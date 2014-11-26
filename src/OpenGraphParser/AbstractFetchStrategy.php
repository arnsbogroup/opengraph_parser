<?php
namespace OpenGraphParser;
abstract class AbstractFetchStrategy {
    public function get($uri) {
        $content = $this->get_content($uri);
        return new Result($content);
    }

    abstract protected function get_content($uri);
}
