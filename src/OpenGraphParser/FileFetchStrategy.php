<?php
namespace OpenGraphParser;
class FileFetchStrategy extends AbstractFetchStrategy {
    protected function get_content($uri) {
        return file_get_contents($uri);
    }
}
