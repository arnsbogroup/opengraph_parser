<?php
namespace OpenGraphParser;
abstract class AbstractFetchStrategy {
    protected $cacheAdapter;
    public function __construct($options = array()) {
        $cacheAdapter = null;
        extract($options);
        if(is_null($cacheAdapter)) {
            $cacheAdapter = new NoCacheAdapter();
        }
        $this->cacheAdapter = $cacheAdapter;
    }

    public function setCacheAdapter($cacheAdapter) {
        $this->cacheAdapter = $cacheAdapter;
    }

    public function getCacheAdapter() {
        return $this->cacheAdapter;
    }

    public function get($uri) {
        $data = array('uri' => $uri, 'cacheAdapter' => $this->cacheAdapter, 'content' => null, 'openGraphFields' => array());
        if($this->cacheAdapter->has($uri)) {
            $cachedData = $this->cacheAdapter->get($uri);
            $data = array_merge($data, $cachedData);
        } else {
            $data['content'] = $this->get_content($uri);
        }

        return new Result($data);
    }

    abstract protected function get_content($uri);
}
