<?php
namespace OpenGraphParser;
class OpenGraphParser {
    public function parse() {
        return new Result();
    }

    public function parseList($urls) {
        $out = array();
        foreach($urls as $url) {
            $out[] = $this->parse($url);
        }
        return $out;
    }
}
