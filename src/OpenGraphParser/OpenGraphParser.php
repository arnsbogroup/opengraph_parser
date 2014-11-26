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
        return $this->fetchStrategy->get($uri);
    }

    public function parseList($urls) {
        $out = array();
        foreach($urls as $url) {
            $out[] = $this->parse($url);
        }
        return $out;
    }

    public function setFetchStrategy(\OpenGraphParser\AbstractFetchStrategy $strategy) {
        $this->fetchStrategy = $strategy;
    }

}
