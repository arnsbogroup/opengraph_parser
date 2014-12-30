<?php
namespace OpenGraphParser;
class ArrayCacheAdapter {
    protected $cache;

    public function __construct($cache=array()) {
        $this->cache = $cache;
    }

    public function has($uri) {
        return array_key_exists(md5($uri), $this->cache);
    }

    public function get($uri) {
        if(!$this->has($uri))
            return null;
        return $this->cache[md5($uri)];
    }

    public function set($uri, $content) {
        $this->cache[md5($uri)] = $content;
    }
}
