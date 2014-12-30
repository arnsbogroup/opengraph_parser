<?php
namespace OpenGraphParser;
class OpenGraphParser {
    protected $fetchStrategy;

    public static function Http() {
        $obj = new OpenGraphParser(array('fetchStrategy' => new HttpFetchStrategy()));
        return $obj;
    }

    public static function File() {
        $obj = new OpenGraphParser(array('fetchStrategy' => new FileFetchStrategy()));
        return $obj;
    }

    /**
     * Constructs a new parser with the given options
     *
     * Currently only fetchStrategy is used
     */
    public function __construct($options=array()) {
        $fetchStrategy = null;
        $cacheAdapter = null;

        extract($options);

        if(is_null($fetchStrategy)) {
            $fetchStrategy = new FetchStrategy();
        }

        $this->fetchStrategy = $fetchStrategy;
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
