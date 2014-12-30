<?php
namespace OpenGraphParser;
class NoCacheAdapter {
    public function has($uri) {
        return false;
    }

    public function get($uri) {
        return null;
    }

    public function set($uri, $content) {
        return;
    }
}
