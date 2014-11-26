<?php
namespace OpenGraphParser;
class OpenGraphParser {
    protected $fetchStrategy;

    public function __construct($fetchStrategy=null) {
        if(is_null($fetchStrategy)) {
            $this->fetchStrategy = new FetchStrategy();
        }
    }

    public function parse($uri) {
        $content = $this->fetchStrategy->get($uri);
        return new Result($content);
    }

    public function parseList($urls) {
        $out = array();
        foreach($urls as $url) {
            $out[] = $this->parse($url);
        }
        return $out;
    }

    public function setFetchStrategy(\OpenGraphParser\FetchStrategy $strategy) {
        $this->fetchStrategy = $strategy;
    }

}
