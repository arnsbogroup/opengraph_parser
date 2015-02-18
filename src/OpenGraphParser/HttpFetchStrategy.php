<?php
namespace OpenGraphParser;
class HttpFetchStrategy extends AbstractFetchStrategy {
    protected function get_content($uri) {
        $client = new \GuzzleHttp\Client();
        $response = $client->get($uri);
        if($response->getStatusCode()!=200) throw new OpenGraphFetchException;
        return $response->getBody();
    }
}
