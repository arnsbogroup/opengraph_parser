<?php
namespace OpenGraphParser;
class OpenGraphParser {
    protected $fetchStrategy;

    public static function Http() {
        $obj = new OpenGraphParser();
        $obj->setFetchStrategy(new HttpFetchStrategy());
        return $obj;
    }

    public static function File() {
        $obj = new OpenGraphParser();
        $obj->setFetchStrategy(new FileFetchStrategy());
        return $obj;
    }

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

    public function getFetchStrategy() {
        return $this->fetchStrategy;
    }

}
