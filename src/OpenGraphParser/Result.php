<?php
namespace OpenGraphParser;
class Result {
    protected $content;

    public function __construct($content) {
        $this->content = $content;
    }
    public function getOriginalContent() {
        return $this->content;
    }
}
