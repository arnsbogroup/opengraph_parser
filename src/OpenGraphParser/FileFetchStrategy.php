<?php
namespace OpenGraphParser;
class FileFetchStrategy extends AbstractFetchStrategy {
    protected function get_content($uri) {
        if(!file_exists($uri)) throw new OpenGraphFetchException;
        return file_get_contents($uri);
    }
}
